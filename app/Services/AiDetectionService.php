<?php

namespace App\Services;

use App\DataObjects\DetectionResult;

class AiDetectionService
{
    /**
     * Main detection method.
     */
    public function detect(string $text): DetectionResult
    {
        $startTime = microtime(true);

        // Step 1: Language detection
        $language = $this->detectLanguage($text);

        // Step 2: Statistical analysis (no API needed)
        $stats = $this->analyzeStatistics($text);

        // Step 3: AI API semantic analysis
        $aiScore = $this->runAiAnalysis($text, $language);

        // Step 4: Combine scores
        $finalScore = $this->combineScores($stats, $aiScore);

        // Step 5: Sentence-level scoring
        $sentences = $this->scoreSentences($text, $language);

        $processingMs = (int) ((microtime(true) - $startTime) * 1000);

        return new DetectionResult(
            overallScore:    round($finalScore, 2),
            verdict:         $this->getVerdict($finalScore),
            burstinessScore: round($stats['burstiness'], 2),
            perplexityScore: round($stats['perplexity'], 2),
            sentences:       $sentences,
            language:        $language,
        );
    }

    /**
     * Statistical analysis — no API needed, fast and free.
     */
    protected function analyzeStatistics(string $text): array
    {
        $sentences = $this->splitSentences($text);

        if (count($sentences) < 2) {
            return [
                'burstiness' => 50,
                'perplexity' => 50,
                'ai_signals' => ['patterns' => [], 'total_score' => 0, 'repetition' => 0],
                'statistical_score' => 50,
            ];
        }

        $lengths = array_map('mb_strlen', $sentences);

        // Burstiness: variance in sentence length
        // High burstiness = human-like, Low = AI-like (uniform)
        $mean = array_sum($lengths) / count($lengths);
        $variance = array_sum(array_map(fn($l) => pow($l - $mean, 2), $lengths)) / count($lengths);
        $stdDev = sqrt($variance);
        $burstiness = $mean > 0 ? ($stdDev / $mean) * 100 : 0;

        // Perplexity proxy: vocabulary richness (lexical diversity)
        $words = preg_split('/\s+/u', mb_strtolower(trim($text)));
        $words = array_filter($words, fn($w) => mb_strlen($w) > 1);
        $uniqueWords = array_unique($words);
        $lexicalDiversity = count($words) > 0
            ? (count($uniqueWords) / count($words)) * 100
            : 0;

        // AI pattern signals
        $aiSignals = $this->detectAiPatterns($text);

        $statScore = $this->calcStatScore($burstiness, $lexicalDiversity, $aiSignals);

        return [
            'burstiness' => min(100, $burstiness),
            'perplexity' => $lexicalDiversity,
            'ai_signals' => $aiSignals,
            'statistical_score' => $statScore,
        ];
    }

    /**
     * Detect common AI writing patterns.
     */
    protected function detectAiPatterns(string $text): array
    {
        $signals = [];

        // English AI patterns
        $enPatterns = [
            'delve into' => 15,
            "it's worth noting" => 12,
            'in conclusion' => 8,
            'furthermore' => 6,
            'it is important to' => 10,
            'plays a crucial role' => 10,
            "in today's world" => 8,
            'as an ai' => 20,
            'in the realm of' => 12,
            'it is worth mentioning' => 12,
            'serves as a testament' => 10,
            'navigating the complexities' => 12,
            'it is imperative' => 10,
            'fostering a sense of' => 10,
            'leveraging the power' => 10,
        ];

        // Bangla AI patterns
        $bnPatterns = [
            'উল্লেখযোগ্য যে' => 12,
            'বলা বাহুল্য' => 10,
            'সুতরাং বলা যায়' => 12,
            'পরিশেষে বলা যায়' => 15,
            'বিশেষভাবে উল্লেখ' => 10,
            'নিঃসন্দেহে' => 8,
            'অত্যন্ত গুরুত্বপূর্ণ' => 10,
            'সামগ্রিকভাবে বলা যায়' => 12,
        ];

        $textLower = mb_strtolower($text);
        $totalSignalScore = 0;

        foreach (array_merge($enPatterns, $bnPatterns) as $pattern => $weight) {
            $count = mb_substr_count($textLower, $pattern);
            if ($count > 0) {
                $signals[] = ['pattern' => $pattern, 'count' => $count, 'weight' => $weight];
                $totalSignalScore += $count * $weight;
            }
        }

        // Repetitive sentence starters
        $starters = $this->getSentenceStarters($text);
        $repetition = $this->calcRepetition($starters);
        $totalSignalScore += $repetition * 5;

        return [
            'patterns' => $signals,
            'total_score' => min(100, $totalSignalScore),
            'repetition' => $repetition,
        ];
    }

    /**
     * AI API deep analysis.
     */
    protected function runAiAnalysis(string $text, string $language): float
    {
        $systemPrompt = $this->getSystemPrompt();

        $prompt = $language === 'bangla'
            ? "Analyze the following Bangla text and determine the probability it was written by AI:\n\n{$text}"
            : "Analyze the following text and determine the probability it was written by AI:\n\n{$text}";

        try {
            $response = app(AiService::class)->generateRaw(
                systemPrompt: $systemPrompt,
                userMessage: $prompt,
                maxTokens: 300,
                temperature: 0.1,
            );

            if (!$response->success) {
                return 50.0; // Fallback to neutral if API fails
            }

            // Extract JSON from response
            $content = $response->content;
            if (preg_match('/\{[^}]+\}/s', $content, $matches)) {
                $data = json_decode($matches[0], true);
                return (float) ($data['ai_probability'] ?? 50.0);
            }

            return 50.0;
        } catch (\Throwable $e) {
            \Log::warning('AI detection API failed', ['error' => $e->getMessage()]);
            return 50.0;
        }
    }

    /**
     * System prompt for AI detection.
     */
    protected function getSystemPrompt(): string
    {
        return <<<PROMPT
You are an expert AI content detection system specializing in both English and Bangla text. Analyze the given text and determine the probability that it was written by AI (not a human).

Analyze these factors:
1. Sentence structure uniformity
2. Vocabulary patterns typical of AI (e.g., "delve into", "it's worth noting")
3. Lack of personal voice, opinions, or lived experiences
4. Overly formal or structured writing without natural flow
5. Repetitive transitions and connectives
6. For Bangla: uniform sentence endings, lack of colloquial expressions, overly formal Bengali
7. Perplexity and burstiness patterns

Return ONLY valid JSON:
{"ai_probability": 0-100, "confidence": "high|medium|low", "key_signals": ["signal1", "signal2"], "human_signals": ["signal1", "signal2"]}
PROMPT;
    }

    /**
     * Combine statistical + AI scores.
     */
    protected function combineScores(array $stats, float $aiScore): float
    {
        $statScore = $stats['statistical_score'];
        // Weight: 40% statistical, 60% AI analysis
        $combined = ($statScore * 0.40) + ($aiScore * 0.60);

        // Boost if many AI patterns detected
        if ($stats['ai_signals']['total_score'] > 30) {
            $combined = min(100, $combined + 10);
        }

        return round(max(0, min(100, $combined)), 2);
    }

    /**
     * Score individual sentences using pattern matching (no API).
     */
    protected function scoreSentences(string $text, string $language): array
    {
        $sentences = $this->splitSentences($text);
        $scored = [];

        foreach ($sentences as $sentence) {
            $trimmed = trim($sentence);
            if (mb_strlen($trimmed) < 10) {
                continue;
            }

            $score = $this->quickSentenceScore($trimmed, $language);
            $verdict = match (true) {
                $score >= 70 => 'ai',
                $score >= 40 => 'mixed',
                default => 'human',
            };

            $scored[] = [
                'text' => $trimmed,
                'score' => $score,
                'verdict' => $verdict,
            ];
        }

        return $scored;
    }

    /**
     * Quick per-sentence AI score (pattern matching only, no API).
     */
    protected function quickSentenceScore(string $sentence, string $language): float
    {
        $score = 0;

        $patterns = $language === 'bangla'
            ? [
                'উল্লেখযোগ্য' => 15, 'গুরুত্বপূর্ণ ভূমিকা' => 12,
                'বলা যায়' => 8, 'বিশেষভাবে' => 6,
                'সুতরাং' => 5, 'অত্যন্ত' => 5,
                'সামগ্রিকভাবে' => 10, 'নিঃসন্দেহে' => 8,
            ]
            : [
                'delve' => 15, 'crucial' => 8, 'pivotal' => 10,
                'furthermore' => 8, 'moreover' => 8, 'it is worth' => 12,
                'in conclusion' => 10, 'it is important' => 10,
                'plays a crucial role' => 15, 'it should be noted' => 12,
                'leveraging' => 8, 'fostering' => 8, 'navigating' => 6,
            ];

        $sentenceLower = mb_strtolower($sentence);
        foreach ($patterns as $pattern => $weight) {
            if (mb_strpos($sentenceLower, $pattern) !== false) {
                $score += $weight;
            }
        }

        // Sentence length uniformity signal
        $wordCount = count(preg_split('/\s+/u', trim($sentence)));
        if ($wordCount >= 15 && $wordCount <= 25) {
            $score += 5;
        }

        return min(100, max(0, $score));
    }

    /**
     * Calculate statistical score from burstiness, diversity, and signals.
     */
    protected function calcStatScore(float $burstiness, float $lexicalDiversity, array $aiSignals): float
    {
        // Low burstiness = more AI-like
        $burstScore = max(0, 100 - ($burstiness * 2));

        // Low lexical diversity = more AI-like
        $diversityScore = max(0, 100 - $lexicalDiversity);

        // Pattern-based AI signals
        $signalScore = min(100, $aiSignals['total_score']);

        return ($burstScore * 0.3) + ($diversityScore * 0.3) + ($signalScore * 0.4);
    }

    /**
     * Get the verdict label from a score.
     */
    protected function getVerdict(float $score): string
    {
        return match (true) {
            $score >= 70 => 'ai',
            $score >= 35 => 'mixed',
            default => 'human',
        };
    }

    /**
     * Split text into sentences, handling both English and Bangla.
     */
    protected function splitSentences(string $text): array
    {
        $text = preg_replace('/([।.!?])\s+/u', '$1|SPLIT|', $text);
        $sentences = explode('|SPLIT|', $text);
        return array_values(array_filter($sentences, fn($s) => mb_strlen(trim($s)) > 5));
    }

    /**
     * Detect the primary language of the text.
     */
    protected function detectLanguage(string $text): string
    {
        preg_match_all('/[\x{0980}-\x{09FF}]/u', $text, $bangla);
        $banglaCount = count($bangla[0]);
        $totalChars = mb_strlen(preg_replace('/\s/u', '', $text));

        if ($totalChars === 0) {
            return 'english';
        }

        $banglaRatio = $banglaCount / $totalChars;

        return match (true) {
            $banglaRatio > 0.6 => 'bangla',
            $banglaRatio > 0.2 => 'mixed',
            default => 'english',
        };
    }

    /**
     * Get the first 2-3 words of each sentence.
     */
    protected function getSentenceStarters(string $text): array
    {
        $sentences = $this->splitSentences($text);
        $starters = [];
        foreach ($sentences as $sentence) {
            $words = preg_split('/\s+/u', trim($sentence));
            $starters[] = mb_strtolower(implode(' ', array_slice($words, 0, 2)));
        }
        return $starters;
    }

    /**
     * Calculate repetition ratio of sentence starters.
     */
    protected function calcRepetition(array $starters): float
    {
        if (count($starters) < 3) {
            return 0;
        }

        $counts = array_count_values($starters);
        $maxRepeat = max($counts);
        return ($maxRepeat / count($starters)) * 100;
    }
}
