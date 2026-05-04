<?php

namespace App\ValueObjects;

class AuditResult
{
    public function __construct(
        public readonly int $overallScore,
        public readonly int $technicalScore,
        public readonly int $onpageScore,
        public readonly int $performanceScore,
        public readonly int $aiReadinessScore,
        public readonly int $issuesCritical,
        public readonly int $issuesWarning,
        public readonly int $issuesPassed,
        public readonly ?string $metaTitle,
        public readonly ?string $metaDescription,
        public readonly ?string $canonicalUrl,
        public readonly ?string $h1,
        public readonly ?int $wordCount,
        public readonly ?float $loadTime,
        public readonly array $auditData
    ) {}
}
