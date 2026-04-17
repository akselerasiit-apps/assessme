<?php

namespace App\Services\Ofi\Contracts;

interface OfiAiProviderInterface
{
    public function generate(array $promptPayload): array;
}
