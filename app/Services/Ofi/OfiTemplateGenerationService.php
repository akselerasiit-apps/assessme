<?php

namespace App\Services\Ofi;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentGamoSelection;
use App\Models\AssessmentOfi;
use App\Models\GamoObjective;
use App\Models\GamoQuestion;
use Illuminate\Support\Facades\DB;

class OfiTemplateGenerationService
{
    public function generate(Assessment $assessment, GamoObjective $gamo, ?int $userId = null): array
    {
        $data = $this->buildRecommendationData($assessment, $gamo);

        DB::transaction(function () use ($assessment, $gamo, $data, $userId) {
            AssessmentOfi::where('assessment_id', $assessment->id)
                ->where('gamo_objective_id', $gamo->id)
                ->where('type', 'auto')
                ->where(function ($query) {
                    $query->whereNull('generation_source')
                        ->orWhere('generation_source', 'template');
                })
                ->delete();

            if (empty($data['recommendations'])) {
                return;
            }

            AssessmentOfi::create([
                'assessment_id' => $assessment->id,
                'gamo_objective_id' => $gamo->id,
                'title' => $data['title'],
                'description' => $this->buildDescription($data),
                'type' => 'auto',
                'priority' => $data['priority'],
                'status' => 'open',
                'category' => 'Process',
                'recommended_action' => null,
                'current_level' => $data['current_level'],
                'target_level' => $data['target_level'],
                'gap_score' => $data['gap_score'],
                'generation_source' => 'template',
                'generation_provider' => null,
                'generation_model' => null,
                'prompt_version' => null,
                'fallback_used' => false,
                'created_by' => $userId,
            ]);
        });

        return [
            'message' => empty($data['recommendations'])
                ? 'No gap activities found for template generation'
                : 'Template OFI generated successfully',
            'recommendations_count' => count($data['recommendations']),
            'data' => $data,
        ];
    }

    public function buildRecommendationData(Assessment $assessment, GamoObjective $gamo): array
    {
        $selection = AssessmentGamoSelection::where('assessment_id', $assessment->id)
            ->where('gamo_objective_id', $gamo->id)
            ->first();

        if (!$selection) {
            abort(404, 'GAMO not selected');
        }

        $targetLevel = $selection->target_maturity_level;
        $currentLevel = $this->calculateCurrentLevel($assessment, $gamo);
        $recommendations = [];

        if ($currentLevel < $targetLevel) {
            for ($level = $currentLevel + 1; $level <= $targetLevel; $level++) {
                $activities = GamoQuestion::where('gamo_objective_id', $gamo->id)
                    ->where('maturity_level', $level)
                    ->orderBy('code')
                    ->get();

                foreach ($activities as $activity) {
                    $answer = AssessmentAnswer::where('assessment_id', $assessment->id)
                        ->where('question_id', $activity->id)
                        ->first();

                    $compliance = $answer?->compliance_percentage ?? 0;

                    if ($compliance >= 85) {
                        continue;
                    }

                    [$activityName, $translatedText] = $this->splitQuestionText($activity->question_text);

                    $recommendations[] = [
                        'level' => $level,
                        'question_id' => $activity->id,
                        'activity_code' => $activity->code,
                        'activity_name' => $activityName,
                        'translated_text' => $translatedText,
                        'current_compliance' => round((float) $compliance, 2),
                        'capability_rating' => $answer?->capability_rating,
                        'capability_score' => $answer?->capability_score,
                        'weight' => $activity->weight ?? 1,
                        'guidance' => $activity->guidance,
                        'document_requirements' => $activity->document_requirements,
                    ];
                }
            }
        }

        $gapScore = max($targetLevel - $currentLevel, 0);

        return [
            'title' => "Rekomendasi Peningkatan Level {$currentLevel} ke Level {$targetLevel}",
            'current_level' => $currentLevel,
            'target_level' => $targetLevel,
            'gap_score' => $gapScore,
            'priority' => $gapScore >= 2 ? 'high' : 'medium',
            'recommendations' => $recommendations,
        ];
    }

    private function buildDescription(array $data): string
    {
        $description = '<p><strong>Kesenjangan Kapabilitas: Level '.e((string) $data['current_level']).' → Level '.e((string) $data['target_level']).'</strong></p>';
        $description .= '<p>Untuk mencapai target level, disarankan untuk meningkatkan aktivitas berikut:</p>';
        $description .= '<ul>';

        foreach ($data['recommendations'] as $recommendation) {
            $description .= '<li>';
            $description .= '<strong>['.e($recommendation['activity_code']).']</strong> '.e($recommendation['translated_text'] ?: $recommendation['activity_name']).' ';
            $description .= '(Level '.e((string) $recommendation['level']).', Kepatuhan saat ini: '.e((string) $recommendation['current_compliance']).'%)';
            $description .= '</li>';
        }

        $description .= '</ul>';

        return $description;
    }

    private function splitQuestionText(?string $questionText): array
    {
        $parts = explode(' | ', (string) $questionText, 2);
        $primary = trim($parts[0] ?? '');
        $translated = trim($parts[1] ?? $primary);

        return [$primary, $translated];
    }

    private function calculateCurrentLevel(Assessment $assessment, GamoObjective $gamo): int
    {
        $achievedLevel = 0;

        for ($level = 2; $level <= 5; $level++) {
            $activities = GamoQuestion::where('gamo_objective_id', $gamo->id)
                ->where('maturity_level', $level)
                ->get();

            if ($activities->isEmpty()) {
                continue;
            }

            $totalWeight = 0;
            $weightedScore = 0;

            foreach ($activities as $activity) {
                $weight = $activity->weight ?? 1;
                $totalWeight += $weight;

                $answer = AssessmentAnswer::where('assessment_id', $assessment->id)
                    ->where('question_id', $activity->id)
                    ->first();

                if ($answer?->capability_score) {
                    $weightedScore += $weight * $answer->capability_score;
                }
            }

            $compliance = $totalWeight > 0 ? (($weightedScore / $totalWeight) * 100) : 0;

            if ($compliance >= 85) {
                $achievedLevel = $level;
                continue;
            }

            break;
        }

        return $achievedLevel;
    }
}
