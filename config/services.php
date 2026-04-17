<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ofi_ai' => [
        'enabled' => env('OFI_AI_ENABLED', true),
        'default_provider' => env('OFI_AI_DEFAULT_PROVIDER', 'local'),
        'prompt_version' => env('OFI_AI_PROMPT_VERSION', 'v1'),
        'temperature' => (float) env('OFI_AI_TEMPERATURE', 0.2),
        'timeout' => (int) env('OFI_AI_TIMEOUT', 60),
    ],

    'ofi_ai_local' => [
        'driver' => env('OFI_AI_LOCAL_DRIVER', 'ollama'),
        'base_url' => env('OFI_AI_LOCAL_BASE_URL', 'http://127.0.0.1:11434'),
        'model' => env('OFI_AI_LOCAL_MODEL', 'deepseek-r1:latest'),
        'api_key' => env('OFI_AI_LOCAL_API_KEY'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com/v1'),
        'model' => env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-latest'),
        'version' => env('ANTHROPIC_VERSION', '2023-06-01'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-pro'),
    ],

];
