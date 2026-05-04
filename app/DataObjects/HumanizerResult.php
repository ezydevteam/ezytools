<?php

namespace App\DataObjects;

class HumanizerResult
{
    public function __construct(
        public readonly string $originalText,
        public readonly string $humanizedText,
        public readonly float $originalScore,
        public readonly float $humanizedScore,
        public readonly float $improvement,
        public readonly int $passesCompleted,
    ) {}
}
