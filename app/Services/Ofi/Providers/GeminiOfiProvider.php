<?php

namespace App\Services\Ofi\Providers;

use App\Services\Ofi\Contracts\OfiAiProviderInterface;
use App\Services\Ofi\Exceptions\OfiAiException;
use Illuminate\Support\Facades\Http;

class GeminiOfiProvider implements OfiAiProviderInterface
{
    public function generate(array $promptPayload): array
    {
        $apiKey = (string) config('services.gemini.api_key');

        if ($apiKey === '') {
            throw new OfiAiException('GEMINI_API_KEY is not configured.');
        }

        $baseUrl = rtrim((string) config('services.gemini.base_url'), '/');
        $model = (string) config('services.gemini.model');

        $response = Http::timeout((int) config('services.ofi_ai.timeout', 60))
            ->acceptJson()
            ->post($baseUrl.'/models/'.$model.':generateContent?key='.$apiKey, [
                'generationConfig' => [
                    'temperature' => config('services.ofi_ai.temperature', 0.2),
                    'responseMimeType' => 'application/json',
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $promptPayload['system_prompt']."\n\n".$promptPayload['user_prompt']],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new OfiAiException('Gemini request failed.');
        }

        return [
            'provider' => 'gemini',
            'model' => $model,
            'content' => data_get($response->json(), 'candidates.0.content.parts.0.text', ''),
            'raw_response' => $response->json(),
        ];
    }
}