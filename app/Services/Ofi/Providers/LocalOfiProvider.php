<?php

namespace App\Services\Ofi\Providers;

use App\Services\Ofi\Contracts\OfiAiProviderInterface;
use App\Services\Ofi\Exceptions\OfiAiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class LocalOfiProvider implements OfiAiProviderInterface
{
    public function generate(array $promptPayload): array
    {
        return match (config('services.ofi_ai_local.driver', 'ollama')) {
            'ollama' => $this->generateWithOllama($promptPayload),
            'openai-compatible' => $this->generateWithOpenAiCompatible($promptPayload),
            default => throw new OfiAiException('Unsupported local AI driver configuration.'),
        };
    }

    private function generateWithOllama(array $promptPayload): array
    {
        $baseUrl = rtrim((string) config('services.ofi_ai_local.base_url'), '/');
        $model = (string) config('services.ofi_ai_local.model');

        try {
            $response = Http::timeout((int) config('services.ofi_ai.timeout', 60))
                ->retry(1, 1000)
                ->post($baseUrl.'/api/chat', [
                    'model' => $model,
                    'stream' => false,
                    'format' => 'json',
                    'keep_alive' => '5m',
                    'messages' => [
                        ['role' => 'system', 'content' => $promptPayload['system_prompt']],
                        ['role' => 'user', 'content' => $promptPayload['user_prompt']],
                    ],
                    'options' => [
                        'temperature' => config('services.ofi_ai.temperature', 0.2),
                        'num_predict' => 900,
                    ],
                ]);
        } catch (ConnectionException $exception) {
            throw new OfiAiException('Local AI request timed out. Increase OFI_AI_TIMEOUT or switch to a faster local model.');
        }

        if ($response->failed()) {
            throw new OfiAiException('Local AI request failed. Check that your local model server is running.');
        }

        return [
            'provider' => 'local',
            'model' => $model,
            'content' => data_get($response->json(), 'message.content', ''),
            'raw_response' => $response->json(),
        ];
    }

    private function generateWithOpenAiCompatible(array $promptPayload): array
    {
        $baseUrl = rtrim((string) config('services.ofi_ai_local.base_url'), '/');
        $model = (string) config('services.ofi_ai_local.model');
        $request = Http::timeout((int) config('services.ofi_ai.timeout', 60))
            ->acceptJson();

        if ($apiKey = config('services.ofi_ai_local.api_key')) {
            $request = $request->withToken($apiKey);
        }

        $response = $request->post($baseUrl.'/v1/chat/completions', [
            'model' => $model,
            'temperature' => config('services.ofi_ai.temperature', 0.2),
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                ['role' => 'system', 'content' => $promptPayload['system_prompt']],
                ['role' => 'user', 'content' => $promptPayload['user_prompt']],
            ],
        ]);

        if ($response->failed()) {
            throw new OfiAiException('Local OpenAI-compatible AI request failed. Check your local endpoint configuration.');
        }

        return [
            'provider' => 'local',
            'model' => $model,
            'content' => data_get($response->json(), 'choices.0.message.content', ''),
            'raw_response' => $response->json(),
        ];
    }
}
