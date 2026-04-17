<?php

namespace App\Services\Ofi\Providers;

use App\Services\Ofi\Contracts\OfiAiProviderInterface;
use App\Services\Ofi\Exceptions\OfiAiException;
use Illuminate\Support\Facades\Http;

class OpenAIOfiProvider implements OfiAiProviderInterface
{
    public function generate(array $promptPayload): array
    {
        $apiKey = (string) config('services.openai.api_key');

        if ($apiKey === '') {
            throw new OfiAiException('OPENAI_API_KEY is not configured.');
        }

        $baseUrl = rtrim((string) config('services.openai.base_url'), '/');
        $model = (string) config('services.openai.model');

        $response = Http::timeout((int) config('services.ofi_ai.timeout', 60))
            ->withToken($apiKey)
            ->acceptJson()
            ->post($baseUrl.'/chat/completions', [
                'model' => $model,
                'temperature' => config('services.ofi_ai.temperature', 0.2),
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    ['role' => 'system', 'content' => $promptPayload['system_prompt']],
                    ['role' => 'user', 'content' => $promptPayload['user_prompt']],
                ],
            ]);

        if ($response->failed()) {
            throw new OfiAiException('OpenAI request failed.');
        }

        return [
            'provider' => 'openai',
            'model' => $model,
            'content' => data_get($response->json(), 'choices.0.message.content', ''),
            'raw_response' => $response->json(),
        ];
    }
}
