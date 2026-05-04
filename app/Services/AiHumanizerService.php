<?php

namespace App\Services;

use App\DataObjects\HumanizerResult;

class AiHumanizerService
{
    /**
     * Main humanize method.
     */
    public function humanize(
        string $text,
        string $level = 'medium',
        string $language = 'auto',
        string $style = 'conversational',
        bool $isPro = false,
    ): HumanizerResult {
        // Determine number of passes
        $passes = match (true) {
            $isPro && $level === 'heavy' => 3,
            $isPro && $level === 'medium' => 2,
            default => 1,
        };

        // Auto-detect language if needed
        if ($language === 'auto') {
            $language = app(AiDetectionService::class)->detect($text)->language;
        }

        $systemPrompt = match ($level) {
            'light' => $this->getLightPrompt(),
            'heavy' => $this->getHeavyPrompt(),
            default => $this->getMediumPrompt(),
        };

        // Add style modifier
        $systemPrompt .= $this->getStyleModifier($style, $language);

        $current = $text;

        // Multiple passes for better humanization (Pro)
        for ($i = 0; $i < $passes; $i++) {
            $current = $this->runSinglePass($current, $systemPrompt, $language, $i + 1);
        }

        // Calculate improvement by re-detecting
        $detector = app(AiDetectionService::class);
        $originalScore = $detector->detect($text)->overallScore;
        $humanizedScore = $detector->detect($current)->overallScore;

        return new HumanizerResult(
            originalText: $text,
            humanizedText: $current,
            originalScore: $originalScore,
            humanizedScore: $humanizedScore,
            improvement: round($originalScore - $humanizedScore, 1),
            passesCompleted: $passes,
        );
    }

    protected function runSinglePass(
        string $text,
        string $systemPrompt,
        string $language,
        int $passNumber,
    ): string {
        $userMessage = $language === 'bangla'
            ? "Rewrite the following Bangla text to sound more human-written:\n\n{$text}"
            : "Rewrite the following text to sound more human-written:\n\n{$text}";

        if ($passNumber > 1) {
            $userMessage .= "\n\nNote: This is humanization pass #{$passNumber}. Make additional improvements for more natural flow.";
        }

        $response = app(AiService::class)->generateRaw(
            systemPrompt: $systemPrompt,
            userMessage: $userMessage,
            maxTokens: 2000,
            temperature: 0.85,
        );

        return $response->success ? trim($response->content) : $text;
    }

    protected function getLightPrompt(): string
    {
        return <<<'PROMPT'
You are a professional writing editor who specializes in making AI-generated text sound naturally human-written.

TASK: Lightly edit the following text to make it sound more human.

RULES FOR LIGHT HUMANIZATION:
1. Keep 80% of original words and structure
2. Replace overused AI phrases:
   - "delve into" → "explore" or "look at"
   - "it's worth noting" → "notably" or remove entirely
   - "in conclusion" → "finally" or "to wrap up"
   - "furthermore" → "also" or "and"
   - "it is important to note" → "importantly" or remove
   - "plays a crucial role" → "is key" or "matters"
3. Vary 2-3 sentence lengths (make some shorter, some longer)
4. Add 1-2 natural contractions if English (don't, it's, that's)
5. Remove exactly 1-2 overly formal transitions
6. Preserve ALL facts, data, and meaning exactly
7. Do NOT add new content or opinions
8. Output ONLY the rewritten text, nothing else
PROMPT;
    }

    protected function getMediumPrompt(): string
    {
        return <<<'PROMPT'
You are an expert content writer who rewrites AI-generated text to sound authentically human.

TASK: Moderately rewrite the following text to pass AI detection.

RULES FOR MEDIUM HUMANIZATION:
1. Rewrite 50-60% of sentences while preserving all meaning
2. VARY sentence structure significantly:
   - Mix short punchy sentences (5-10 words) with longer ones (20-30 words)
   - Start some sentences with conjunctions (But, And, So, Yet)
   - Use occasional em-dashes for natural pauses — like this
   - Add parenthetical asides (which makes writing feel human)
3. Replace ALL common AI patterns and clichés completely:
   - Remove: "delve into", "crucial", "pivotal", "foster", "leverage"
   - Remove: "in today's fast-paced world", "it is imperative"
4. Inject natural writing elements:
   - 1-2 rhetorical questions where appropriate
   - Use active voice over passive
   - Add specific, concrete examples to replace vague statements
   - Natural hedging: "probably", "likely", "I'd say"
5. Natural flow improvements:
   - Vary paragraph length (not all the same)
   - Break up very long sentences into 2 shorter ones
   - Combine some short choppy sentences
6. Preserve 100% of original facts, numbers, and core meaning
7. Output ONLY the rewritten text
PROMPT;
    }

    protected function getHeavyPrompt(): string
    {
        return <<<'PROMPT'
You are a skilled ghostwriter who completely rewrites content while preserving the original message. Your goal is to make the text completely indistinguishable from natural human writing.

TASK: Completely rewrite the following text so it reads as if written by a thoughtful human, while keeping all the original information.

ADVANCED HUMANIZATION RULES:

SENTENCE DIVERSITY (Critical):
- Use a wide range of sentence lengths: 3-word sentences. Then a longer sentence that builds on the idea. Short again.
- Vary sentence openers dramatically:
  * Start with adverbs: "Interestingly, ...", "Oddly enough, ..."
  * Start with gerunds: "Understanding this requires..."
  * Start with conditionals: "If you think about it..."
  * Start with contrasts: "Unlike what many expect..."

VOCABULARY HUMANIZATION:
- Replace any word that appears more than twice with synonyms
- Use idioms and informal phrases naturally
- Include domain-specific jargon where appropriate (shows expertise)
- Mix formal and informal register naturally

STRUCTURAL HUMANIZATION:
- Vary paragraph length dramatically (1 sentence to 6 sentences)
- Add a personal observation or mild opinion (e.g., "In my view...")
- Use transitional phrases that real writers use, not AI defaults
- Add natural uncertainty markers: "arguably", "in most cases", "tend to"

ANTI-DETECTION TECHNIQUES:
- Vary sentence length standard deviation > 40% (burstiness)
- Avoid starting 3+ consecutive sentences with same word type
- No two consecutive sentences of same length
- Use different types of punctuation naturally

ABSOLUTE REQUIREMENTS:
- Preserve 100% of facts, data, names, dates
- Do not add false information
- Keep the same approximate length (±15%)
- Output ONLY the rewritten text — no explanations, no preamble
PROMPT;
    }

    protected function getStyleModifier(string $style, string $language): string
    {
        $modifiers = [
            'conversational' => "\n\nSTYLE: Conversational and friendly. Write like you're explaining to a friend.",
            'academic' => "\n\nSTYLE: Academic but natural. Maintain scholarly tone while avoiding robotic patterns.",
            'professional' => "\n\nSTYLE: Professional business writing. Clear, direct, and authoritative without being robotic.",
        ];

        return $modifiers[$style] ?? $modifiers['conversational'];
    }
}
