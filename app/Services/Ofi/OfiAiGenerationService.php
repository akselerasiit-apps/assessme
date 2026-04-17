<?php

namespace App\Services\Ofi;

use App\Models\Assessment;
use App\Models\AssessmentOfi;
use App\Models\GamoObjective;
use App\Services\Ofi\Contracts\OfiAiProviderInterface;
use App\Services\Ofi\Exceptions\OfiAiException;
use App\Services\Ofi\OfiPromptBuilder;
use App\Services\Ofi\OfiTemplateGenerationService;
use App\Services\Ofi\Providers\AnthropicOfiProvider;
use App\Services\Ofi\Providers\GeminiOfiProvider;
use App\Services\Ofi\Providers\LocalOfiProvider;
use App\Services\Ofi\Providers\OpenAIOfiProvider;
use Illuminate\Support\Facades\DB;

class OfiAiGenerationService
{
    public function __construct(
        private readonly OfiTemplateGenerationService $templateGenerationService,
        private readonly OfiPromptBuilder $promptBuilder,
    ) {
    }

    public function generate(Assessment $assessment, GamoObjective $gamo, ?int $userId = null): array
    {
        if (!config('services.ofi_ai.enabled')) {
            throw new OfiAiException('AI generation is disabled. Enable OFI_AI in your environment first.');
        }

        $templateData = $this->templateGenerationService->buildRecommendationData($assessment, $gamo);

        if (empty($templateData['recommendations'])) {
            return [
                'message' => 'No gap activities found for AI generation',
                'recommendations_count' => 0,
                'generation_provider' => config('services.ofi_ai.default_provider'),
                'generation_model' => null,
            ];
        }

        $provider = $this->resolveProvider();
        $promptPayload = $this->promptBuilder->build($assessment, $gamo, $templateData);
        [$validated, $response] = config('services.ofi_ai.default_provider') === 'local'
            ? $this->generateLocalRecommendations($assessment, $gamo, $templateData, $provider, $promptPayload)
            : $this->generateBatchRecommendations($assessment, $gamo, $templateData, $provider, $promptPayload);

        DB::transaction(function () use ($assessment, $gamo, $templateData, $validated, $response, $userId, $promptPayload) {
            AssessmentOfi::where('assessment_id', $assessment->id)
                ->where('gamo_objective_id', $gamo->id)
                ->where('type', 'auto')
                ->where('generation_source', 'ai')
                ->delete();

            AssessmentOfi::create([
                'assessment_id' => $assessment->id,
                'gamo_objective_id' => $gamo->id,
                'title' => $validated['title'],
                'description' => $this->buildAiDescription($validated, $templateData),
                'type' => 'auto',
                'priority' => $validated['priority'],
                'status' => 'open',
                'category' => 'Process',
                'recommended_action' => json_encode($validated['recommendations'], JSON_UNESCAPED_UNICODE),
                'current_level' => $templateData['current_level'],
                'target_level' => $templateData['target_level'],
                'gap_score' => $templateData['gap_score'],
                'generation_source' => 'ai',
                'generation_provider' => $response['provider'] ?? config('services.ofi_ai.default_provider'),
                'generation_model' => $response['model'] ?? null,
                'generation_prompt' => $response['prompt_preview'] ?? ($promptPayload['prompt_preview'] ?? null),
                'prompt_version' => config('services.ofi_ai.prompt_version'),
                'fallback_used' => false,
                'created_by' => $userId,
            ]);
        });

        return [
            'message' => 'AI OFI generated successfully',
            'recommendations_count' => count($validated['recommendations']),
            'generation_provider' => $response['provider'] ?? config('services.ofi_ai.default_provider'),
            'generation_model' => $response['model'] ?? null,
        ];
    }

    private function resolveProvider(): OfiAiProviderInterface
    {
        return match (config('services.ofi_ai.default_provider')) {
            'local' => app(LocalOfiProvider::class),
            'openai' => app(OpenAIOfiProvider::class),
            'anthropic' => app(AnthropicOfiProvider::class),
            'gemini' => app(GeminiOfiProvider::class),
            default => throw new OfiAiException('Unsupported OFI AI provider configuration.'),
        };
    }

    private function generateBatchRecommendations(
        Assessment $assessment,
        GamoObjective $gamo,
        array $templateData,
        OfiAiProviderInterface $provider,
        array $promptPayload
    ): array {
        $response = $provider->generate($promptPayload);
        $validated = $this->validateAiResponse(
            $response['content'] ?? '',
            $promptPayload['allowed_activity_codes'] ?? [],
            (int) ($promptPayload['required_recommendation_count'] ?? 2)
        );

        return [$validated, $response];
    }

    private function generateLocalRecommendations(
        Assessment $assessment,
        GamoObjective $gamo,
        array $templateData,
        OfiAiProviderInterface $provider,
        array $promptPayload
    ): array {
        $activities = array_slice(
            $promptPayload['activities'] ?? [],
            0,
            (int) ($promptPayload['required_recommendation_count'] ?? 2)
        );

        $recommendations = [];
        $promptPreviews = [];

        foreach ($activities as $index => $activity) {
            $activityPrompt = $this->promptBuilder->buildPerActivity(
                $assessment,
                $gamo,
                $templateData,
                $activity,
                $index + 1,
                count($activities)
            );

            $response = $provider->generate($activityPrompt);
            $recommendation = $this->validateSingleRecommendation(
                $response['content'] ?? '',
                $activity['activity_code']
            );
            $recommendation['activity_name'] = $activity['activity_name'] ?? $activity['activity_code'];
            $recommendation['current_compliance'] = $activity['current_compliance'] ?? null;
            $recommendations[] = $recommendation;
            $promptPreviews[] = $activityPrompt['prompt_preview'] ?? '';
            $providerResponse = $response;
        }

        $validated = [
            'title' => 'Rekomendasi peningkatan kapabilitas '.$gamo->code,
            'summary' => 'Fokus perbaikan diarahkan pada '.count($recommendations).' aktivitas prioritas dengan kepatuhan terendah agar target level dapat dicapai.',
            'priority' => $this->deriveOverallPriority($recommendations, $templateData),
            'rationale' => 'Gap level memerlukan perbaikan terfokus per activity agar penguatan kontrol, dokumentasi, dan pelaksanaan proses berjalan lebih konsisten.',
            'recommendations' => $recommendations,
        ];

        return [$validated, [
            'provider' => $providerResponse['provider'] ?? config('services.ofi_ai.default_provider'),
            'model' => $providerResponse['model'] ?? null,
            'prompt_preview' => implode("\n\n-----\n\n", array_filter($promptPreviews)),
        ]];
    }

    private function validateSingleRecommendation(string $content, string $requiredActivityCode): array
    {
        $decoded = json_decode($this->extractJson($content), true);

        if (!is_array($decoded)) {
            throw new OfiAiException('AI response is not valid JSON. Please review the selected model output.');
        }

        $recommendation = [
            'activity_code' => (string) ($decoded['activity_code'] ?? '-'),
            'issue' => (string) ($decoded['issue'] ?? ''),
            'objective' => (string) ($decoded['objective'] ?? ''),
            'recommended_action' => (string) ($decoded['recommended_action'] ?? ''),
            'expected_evidence' => (string) ($decoded['expected_evidence'] ?? ''),
            'success_indicator' => (string) ($decoded['success_indicator'] ?? ''),
            'priority' => in_array(($decoded['priority'] ?? 'medium'), ['low', 'medium', 'high', 'critical'], true)
                ? $decoded['priority']
                : 'medium',
        ];

        if ($recommendation['activity_code'] !== $requiredActivityCode) {
            throw new OfiAiException('AI response used an unexpected activity code for a per-activity recommendation.');
        }

        foreach (['issue', 'objective', 'recommended_action', 'expected_evidence', 'success_indicator'] as $field) {
            $value = trim($recommendation[$field]);

            if ($value === '' || str_contains($value, '...') || preg_match('/\b(xx|tbd|dummy)\b/i', $value)) {
                throw new OfiAiException('AI response still contains placeholder or overly generic recommendation text.');
            }
        }

        return $recommendation;
    }

    private function deriveOverallPriority(array $recommendations, array $templateData): string
    {
        $priorities = array_column($recommendations, 'priority');
        $priorityOrder = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        $highest = 'medium';

        foreach ($priorities as $priority) {
            if (($priorityOrder[$priority] ?? 0) > ($priorityOrder[$highest] ?? 0)) {
                $highest = $priority;
            }
        }

        if (($templateData['gap_score'] ?? 0) >= 2 && $highest !== 'critical') {
            return 'high';
        }

        return $highest;
    }

    private function validateAiResponse(string $content, array $allowedActivityCodes, int $requiredRecommendationCount): array
    {
        $decoded = json_decode($this->extractJson($content), true);

        if (!is_array($decoded)) {
            throw new OfiAiException('AI response is not valid JSON. Please review the selected model output.');
        }

        foreach (['title', 'summary', 'priority', 'rationale', 'recommendations'] as $requiredField) {
            if (!array_key_exists($requiredField, $decoded)) {
                throw new OfiAiException("AI response is missing required field: {$requiredField}.");
            }
        }

        if (!in_array($decoded['priority'], ['low', 'medium', 'high', 'critical'], true)) {
            $decoded['priority'] = 'medium';
        }

        if (!is_array($decoded['recommendations']) || $decoded['recommendations'] === []) {
            throw new OfiAiException('AI response did not return any recommendations.');
        }

        $allowedActivityCodes = array_values(array_unique(array_filter($allowedActivityCodes)));

        $decoded['recommendations'] = array_map(function (array $recommendation) {
            return [
                'activity_code' => (string) ($recommendation['activity_code'] ?? '-'),
                'issue' => (string) ($recommendation['issue'] ?? ''),
                'objective' => (string) ($recommendation['objective'] ?? ''),
                'recommended_action' => (string) ($recommendation['recommended_action'] ?? ''),
                'expected_evidence' => (string) ($recommendation['expected_evidence'] ?? ''),
                'success_indicator' => (string) ($recommendation['success_indicator'] ?? ''),
                'priority' => in_array(($recommendation['priority'] ?? 'medium'), ['low', 'medium', 'high', 'critical'], true)
                    ? $recommendation['priority']
                    : 'medium',
            ];
        }, $decoded['recommendations']);

        if (count($decoded['recommendations']) !== $requiredRecommendationCount) {
            throw new OfiAiException('AI response must return exactly '.$requiredRecommendationCount.' structured recommendations.');
        }

        $seenActivityCodes = [];

        foreach ($decoded['recommendations'] as $recommendation) {
            if ($allowedActivityCodes !== [] && !in_array($recommendation['activity_code'], $allowedActivityCodes, true)) {
                throw new OfiAiException('AI response used an activity code outside the provided assessment context.');
            }

            if (in_array($recommendation['activity_code'], $seenActivityCodes, true)) {
                throw new OfiAiException('AI response repeated the same activity code in multiple recommendations.');
            }

            $seenActivityCodes[] = $recommendation['activity_code'];

            foreach (['issue', 'objective', 'recommended_action', 'expected_evidence', 'success_indicator'] as $field) {
                $value = trim($recommendation[$field]);

                if ($value === '' || str_contains($value, '...') || preg_match('/\b(xx|tbd|dummy)\b/i', $value)) {
                    throw new OfiAiException('AI response still contains placeholder or overly generic recommendation text.');
                }
            }
        }

        return $decoded;
    }

    private function extractJson(string $content): string
    {
        $trimmed = trim($content);
        $trimmed = preg_replace('/^```json\s*/i', '', $trimmed) ?? $trimmed;
        $trimmed = preg_replace('/^```\s*/i', '', $trimmed) ?? $trimmed;
        $trimmed = preg_replace('/```$/', '', $trimmed) ?? $trimmed;

        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');

        if ($start === false || $end === false || $end <= $start) {
            return $trimmed;
        }

        return substr($trimmed, $start, $end - $start + 1);
    }

    private function buildAiDescription(array $validated, array $templateData): string
    {
        $activityMap = collect($templateData['recommendations'])
            ->keyBy('activity_code');

        $description = '<p><strong>'.e($validated['title']).'</strong></p>';
        $description .= '<p>'.e($validated['summary']).'</p>';
        $description .= '<p><strong>Kesenjangan Kapabilitas:</strong> Level '.e((string) $templateData['current_level']).' → Level '.e((string) $templateData['target_level']).'</p>';
        $description .= '<p><strong>Rationale:</strong> '.e($validated['rationale']).'</p>';

        foreach ($validated['recommendations'] as $recommendation) {
            $activityContext = $activityMap->get($recommendation['activity_code']);
            $activityName = trim((string) ($recommendation['activity_name'] ?? $activityContext['translated_text'] ?? $activityContext['activity_name'] ?? $recommendation['activity_code']));
            $compliance = $recommendation['current_compliance'] ?? $activityContext['current_compliance'] ?? null;

            $description .= '<div class="border rounded p-3 mb-3">';
            $description .= '<div class="d-flex justify-content-between align-items-start gap-3 mb-2">';
            $description .= '<div>';
            $description .= '<div><strong>['.e($recommendation['activity_code']).']</strong> '.e($activityName).'</div>';

            if ($compliance !== null) {
                $description .= '<div class="text-muted small mt-1">Kepatuhan saat ini: '.e((string) $compliance).'%</div>';
            }

            $description .= '</div>';
            $description .= '<span class="badge bg-warning-lt text-warning">'.e(ucfirst($recommendation['priority'])).'</span>';
            $description .= '</div>';
            $description .= '<div class="mb-2"><strong>Issue:</strong> '.e($recommendation['issue']).'</div>';
            $description .= '<div class="mb-2"><strong>Objective:</strong> '.e($recommendation['objective']).'</div>';
            $description .= '<div class="mb-2"><strong>Recommended Action:</strong> '.e($recommendation['recommended_action']).'</div>';
            $description .= '<div class="mb-2"><strong>Expected Evidence:</strong> '.e($recommendation['expected_evidence']).'</div>';
            $description .= '<div><strong>Success Indicator:</strong> '.e($recommendation['success_indicator']).'</div>';
            $description .= '</div>';
        }

        return $description;
    }
}
