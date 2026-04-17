<?php

namespace App\Services\Ofi\Providers;

use App\Services\Ofi\Contracts\OfiAiProviderInterface;
use App\Services\Ofi\Exceptions\OfiAiException;
use Illuminate\Support\Facades\Http;

class AnthropicOfiProvider implements OfiAiProviderInterface
{
    public function generate(array $promptPayload): array
    {
        $apiKey = (string) config('services.anthropic.api_key');

        if ($apiKey === '') {
            throw new OfiAiException('ANTHROPIC_API_KEY is not configured.');
        }

        $baseUrl = rtrim((string) config('services.anthropic.base_url'), '/');
        $model = (string) config('services.anthropic.model');

        $response = Http::timeout((int) config('services.ofi_ai.timeout', 60))
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => config('services.anthropic.version', '2023-06-01'),
            ])
            ->acceptJson()
            ->post($baseUrl.'/messages', [
                'model' => $model,
                'max_tokens' => 2000,
                'temperature' => config('services.ofi_ai.temperature', 0.2),
                'system' => $promptPayload['system_prompt'],
                'messages' => [
                    ['role' => 'user', 'content' => $promptPayload['user_prompt']],
                ],
            ]);

        if ($response->failed()) {
            throw new OfiAiException('Anthropic request failed.');
        }

        return [
            'provider' => 'anthropic',
            'model' => $model,
            'content' => data_get($response->json(), 'content.0.text', ''),
            'raw_response' => $response->json(),
        ];
    }
}
