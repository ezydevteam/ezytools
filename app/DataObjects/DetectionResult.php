<?php

namespace App\DataObjects;

class DetectionResult
{
    public function __construct(
        public readonly float $overallScore,
        public readonly string $verdict,
        public readonly float $burstinessScore,
        public readonly float $perplexityScore,
        public readonly array $sentences,
        public readonly string $language,
    ) {}
}
