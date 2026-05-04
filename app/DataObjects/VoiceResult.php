<?php

namespace App\DataObjects;

use Carbon\Carbon;

class VoiceResult
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $downloadUrl,
        public readonly float $duration,
        public readonly Carbon $expiresAt,
    ) {}
}
