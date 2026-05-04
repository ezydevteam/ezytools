<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\AiProvider;
use App\Models\AiToolConfig;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class NewAiToolsSeeder extends Seeder
{
    public function run(): void
    {
        $aiCategory = ToolCategory::where('slug', 'ai-tools')->firstOrFail();

        $openAi = AiProvider::where('name', 'openai')->first();
        $gemini = AiProvider::where('name', 'gemini')->first();
        $grok   = AiProvider::where('name', 'grok')->first();

        $gptMini     = AiModel::where('name', 'gpt-4o-mini')->first();
        $geminiFlash = AiModel::where('name', 'gemini-2.5-flash')->first();
        $grokMini    = AiModel::where('name', 'grok-3-mini')->first();

        $allLanguages = ['bangla', 'english_us', 'english_british', 'hindi', 'urdu', 'arabic'];
        $bdLanguages  = ['bangla', 'english_us'];

        $tools = [
            [
                'name' => 'AI Content Studio',
                'short_description' => 'Create & humanize content in one place',
                'slug' => 'ai-content-studio',
                'description' => 'Create & humanize content in one place.',
                'description_bn' => 'Create & humanize content in one place.',
                'component_name' => 'AiContentStudio',
                'icon' => 'PencilSquareIcon',
                'is_premium' => false,
                'system_prompt' => "You are an expert content writer.\nWrite in the specified language with the given tone and audience.\nIf mode is 'director': Write a {content_type} about the topic. Length: specified word count.\nIf mode is 'humanizer': Rewrite the text to sound completely human-written. Vary sentence lengths naturally.\nOutput ONLY the content.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Hashtag Generator',
                'short_description' => 'Generate platform-optimized hashtags',
                'slug' => 'ai-hashtag-generator',
                'description' => 'Generate platform-optimized hashtags.',
                'description_bn' => 'Generate platform-optimized hashtags.',
                'component_name' => 'AiHashtagGenerator',
                'icon' => 'HashtagIcon',
                'is_premium' => false,
                'system_prompt' => "Generate hashtags for the specified platform in the given language.\nOrganize into groups: Trending, Niche, Branded, General.\nReturn numbered lists under each category.\nInclude the requested count of hashtags.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI YouTube Script Writer',
                'short_description' => 'Write complete YouTube video scripts',
                'slug' => 'ai-youtube-script',
                'description' => 'Write complete YouTube video scripts with timestamps.',
                'description_bn' => 'Write complete YouTube video scripts with timestamps.',
                'component_name' => 'AiYoutubeScript',
                'icon' => 'VideoCameraIcon',
                'is_premium' => false,
                'system_prompt' => "You are a YouTube content strategist.\nWrite a video script with sections: HOOK, INTRO, MAIN CONTENT (with timestamps), CTA, OUTRO.\nInclude a SEO-optimized video description and suggested tags.\nWrite in the specified language and style.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Ad Copy Generator',
                'short_description' => 'Create platform-specific ad copy',
                'slug' => 'ai-ad-copy',
                'description' => 'Create platform-specific ad copy that converts.',
                'description_bn' => 'Create platform-specific ad copy that converts.',
                'component_name' => 'AiAdCopy',
                'icon' => 'MegaphoneIcon',
                'is_premium' => false,
                'system_prompt' => "Generate ad copy variations for the specified platform in the given language.\nFollow platform-specific character limits strictly.\nInclude headline, primary text, description, and CTA.\nTone and objective as specified.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Story Generator',
                'short_description' => 'Generate creative stories',
                'slug' => 'ai-story-generator',
                'description' => 'Generate creative stories in any genre.',
                'description_bn' => 'Generate creative stories in any genre.',
                'component_name' => 'AiStoryGenerator',
                'icon' => 'BookOpenIcon',
                'is_premium' => false,
                'system_prompt' => "Write a creative story in the specified genre and language.\nUse proper narrative structure with a beginning, middle, and end.\nInclude the specified character, setting, and plot points.\nAdd a title. Write ONLY the story.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Poem / Kobita Writer',
                'short_description' => 'Write beautiful poems',
                'slug' => 'ai-poem-writer',
                'description' => 'Write beautiful poems in any language and style.',
                'description_bn' => 'Write beautiful poems in any language and style.',
                'component_name' => 'AiPoemWriter',
                'icon' => 'MusicalNoteIcon',
                'is_premium' => false,
                'system_prompt' => "Write a beautiful poem in the specified type, mood, and language.\nUse the given number of stanzas.\nIf a person name is provided, incorporate it naturally.\nIf translation is requested, include English translation.\nAdd a poem title. Write ONLY the poem.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Headline Generator',
                'short_description' => 'Generate catchy headlines',
                'slug' => 'ai-headline-generator',
                'description' => 'Generate catchy, click-worthy headlines.',
                'description_bn' => 'Generate catchy, click-worthy headlines.',
                'component_name' => 'AiHeadlineGenerator',
                'icon' => 'NewspaperIcon',
                'is_premium' => false,
                'system_prompt' => "Generate the specified number of headlines in the given language and style.\nFor the given content type and topic.\nIf a keyword is provided, include it.\nMark the best one as recommended.\nReturn as a numbered list.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Wish / Greeting Generator',
                'short_description' => 'Create heartfelt wishes',
                'slug' => 'ai-wish-generator',
                'description' => 'Create heartfelt wishes for any occasion.',
                'description_bn' => 'Create heartfelt wishes for any occasion.',
                'component_name' => 'AiWishGenerator',
                'icon' => 'GiftIcon',
                'is_premium' => false,
                'system_prompt' => "Write wishes for the specified occasion in the given language.\nRecipient, relationship, and tone as specified.\nInclude personal touches if provided.\nGenerate the requested number of variations.\nWrite ONLY the wishes.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI SWOT Analysis',
                'short_description' => 'Generate detailed SWOT analysis',
                'slug' => 'ai-swot-analysis',
                'description' => 'Generate detailed SWOT analysis for any business.',
                'description_bn' => 'Generate detailed SWOT analysis for any business.',
                'component_name' => 'AiSwotAnalysis',
                'icon' => 'ChartBarIcon',
                'is_premium' => false,
                'system_prompt' => "Generate a detailed SWOT analysis in the specified language.\nBusiness type and industry as specified.\nProvide 5 bullet points per quadrant.\nIf recommendations requested, include SO, WO, ST, WT strategies.\nFormat clearly with headers.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Job Description Writer',
                'short_description' => 'Write professional job descriptions',
                'slug' => 'ai-job-description',
                'description' => 'Write professional, ATS-friendly job descriptions.',
                'description_bn' => 'Write professional, ATS-friendly job descriptions.',
                'component_name' => 'AiJobDescription',
                'icon' => 'BriefcaseIcon',
                'is_premium' => false,
                'system_prompt' => "Write a professional job description in the specified language.\nInclude: About the Role, Key Responsibilities, Requirements, Nice to Have, Benefits, How to Apply.\nOptimize for ATS if specified. Use formal professional language.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Text Expander',
                'short_description' => 'Expand short notes into content',
                'slug' => 'ai-text-expander',
                'description' => 'Expand short notes into detailed content.',
                'description_bn' => 'Expand short notes into detailed content.',
                'component_name' => 'AiTextExpander',
                'icon' => 'ArrowsPointingOutIcon',
                'is_premium' => false,
                'system_prompt' => "Expand the following notes into the specified word count in the given language.\nFormat and tone as specified.\nWrite ONLY the expanded content. No explanations.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Tone Changer',
                'short_description' => 'Change tone of any text',
                'slug' => 'ai-tone-changer',
                'description' => 'Change the tone of any text instantly.',
                'description_bn' => 'Change the tone of any text instantly.',
                'component_name' => 'AiToneChanger',
                'icon' => 'AdjustmentsHorizontalIcon',
                'is_premium' => false,
                'system_prompt' => "Rewrite the following text in the specified language with the specified tone.\nPreservation level as specified.\nKeep similar length if requested.\nReturn ONLY the rewritten text.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Fake Review Detector',
                'short_description' => 'Detect fake and suspicious reviews',
                'slug' => 'ai-fake-review-detector',
                'description' => 'Detect fake and suspicious product reviews.',
                'description_bn' => 'Detect fake and suspicious product reviews.',
                'component_name' => 'AiFakeReviewDetector',
                'icon' => 'ShieldExclamationIcon',
                'is_premium' => false,
                'system_prompt' => "Analyze the following review(s) for authenticity.\nPlatform context as specified.\nFor each review provide: Authenticity score (0-100%), Verdict (Genuine/Suspicious/Fake), Red flags detected, Reasoning.\nIf sentiment analysis requested, include positive/negative/neutral.\nFormat clearly.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Study Notes Generator',
                'short_description' => 'Generate study notes',
                'slug' => 'ai-study-notes',
                'description' => 'Generate study notes from any topic or textbook.',
                'description_bn' => 'Generate study notes from any topic or textbook.',
                'component_name' => 'AiStudyNotes',
                'icon' => 'AcademicCapIcon',
                'is_premium' => false,
                'system_prompt' => "Generate study notes in the specified language for the given level and subject.\nFormat as specified (bullets, headings, Q&A, or mind map).\nInclude key terms, important facts, and memory tips.\nIf exam questions requested, add 5 possible questions.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Citation Generator',
                'short_description' => 'Generate formatted citations',
                'slug' => 'ai-citation-generator',
                'description' => 'Generate properly formatted citations in any style.',
                'description_bn' => 'Generate properly formatted citations in any style.',
                'component_name' => 'AiCitationGenerator',
                'icon' => 'BookmarkIcon',
                'is_premium' => false,
                'system_prompt' => "Generate a properly formatted citation in the specified format (APA/MLA/Chicago/Harvard/Vancouver).\nSource type and details as provided.\nReturn both the full citation and in-text citation format.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Meeting Agenda Generator',
                'short_description' => 'Create professional meeting agendas',
                'slug' => 'ai-meeting-agenda',
                'description' => 'Create professional meeting agendas instantly.',
                'description_bn' => 'Create professional meeting agendas instantly.',
                'component_name' => 'AiMeetingAgenda',
                'icon' => 'CalendarDaysIcon',
                'is_premium' => false,
                'system_prompt' => "Create a professional meeting agenda in the specified language.\nInclude: header (title, date, attendees), agenda items with time slots (distributed proportionally), discussion points, action items, and next meeting placeholder.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Performance Review Writer',
                'short_description' => 'Write professional employee performance reviews',
                'slug' => 'ai-performance-review',
                'description' => 'Write professional employee performance reviews.',
                'description_bn' => 'Write professional employee performance reviews.',
                'component_name' => 'AiPerformanceReview',
                'icon' => 'StarIcon',
                'is_premium' => false,
                'system_prompt' => "Write a professional performance review in the specified language and tone.\nEmployee, role, period, and rating as specified.\nInclude: opening summary, achievements & strengths, areas for growth, goals & expectations.\nUse professional HR language.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Joke Generator',
                'short_description' => 'Generate clean jokes and humor',
                'slug' => 'ai-joke-generator',
                'description' => 'Generate clean, family-friendly jokes and humor.',
                'description_bn' => 'Generate clean, family-friendly jokes and humor.',
                'component_name' => 'AiJokeGenerator',
                'icon' => 'FaceSmileIcon',
                'is_premium' => false,
                'system_prompt' => "Generate the specified number of clean, family-friendly jokes in the given language.\nTopic and humor style as specified.\nSTRICT RULES: No offensive, political, religious, or adult content.\nKeep it clean and family-friendly.\nReturn numbered list.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Proposal Writer',
                'short_description' => 'Write professional business proposals',
                'slug' => 'ai-proposal-writer',
                'description' => 'Write professional business and project proposals.',
                'description_bn' => 'Write professional business and project proposals.',
                'component_name' => 'AiProposalWriter',
                'icon' => 'DocumentCheckIcon',
                'is_premium' => false,
                'system_prompt' => "Write a professional proposal in the specified language.\nInclude: Executive Summary, Problem Statement, Proposed Solution, Scope of Work, Timeline, Pricing (if requested), About Us, Next Steps.\nUse formal business language.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI Assignment Helper',
                'short_description' => 'Get structured help with academic assignments',
                'slug' => 'ai-assignment-helper',
                'description' => 'Get structured help with academic assignments.',
                'description_bn' => 'Get structured help with academic assignments.',
                'component_name' => 'AiAssignmentHelper',
                'icon' => 'AcademicCapIcon',
                'is_premium' => false,
                'system_prompt' => "Help with this academic assignment in the specified language.\nLevel, subject, and assignment type as specified.\nProvide a well-structured, educational response.\nInclude key points, references if requested, and study tips.\nThis is for learning purposes only.",
                'languages' => $allLanguages,
            ],
            [
                'name' => 'AI BD Business Proposal',
                'short_description' => 'Create Bangladesh-specific business proposals',
                'slug' => 'ai-bd-business-proposal',
                'description' => 'Create Bangladesh-specific business proposals for banks, SME grants, and tenders.',
                'description_bn' => 'Create Bangladesh-specific business proposals for banks, SME grants, and tenders.',
                'component_name' => 'AiBdBusinessProposal',
                'icon' => 'BuildingOffice2Icon',
                'is_premium' => false,
                'system_prompt' => "Write a formal business proposal in the specified language (Bangla or English only).\nThis is for Bangladesh context — bank loans, SME Foundation grants, government tenders, investor pitches.\nUse government-style formal formatting appropriate for BD.\nInclude all relevant sections for the specified purpose.",
                'languages' => $bdLanguages,
            ],
        ];

        // Get the current max order for AI tools
        $maxOrder = Tool::where('category_id', $aiCategory->id)->max('order') ?? 20;

        foreach ($tools as $index => $toolData) {
            $systemPrompt = $toolData['system_prompt'];
            $languages = $toolData['languages'];
            unset($toolData['system_prompt'], $toolData['languages']);

            $tool = Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                array_merge($toolData, [
                    'category_id' => $aiCategory->id,
                    'how_to_use' => '<h2>How to use ' . $toolData['name'] . '</h2><p>' . $toolData['description'] . '</p><h3>Quick Guide:</h3><ol><li>Provide the necessary details in the input fields.</li><li>Choose your target language and desired tone.</li><li>Press "Generate" to create your content.</li><li>Review the AI-generated result and use it as needed.</li></ol>',
                    'is_active' => true,
                    'supported_languages' => $languages,
                    'default_language' => 'english_us',
                    'order' => $maxOrder + $index + 1,
                ])
            );

            AiToolConfig::updateOrCreate(
                ['tool_id' => $tool->id],
                [
                    'provider_id' => $gemini?->id,
                    'model_id' => $geminiFlash?->id,
                    'pro_provider_id' => $gemini?->id,
                    'pro_model_id' => $geminiFlash?->id,
                    'fallback_provider_id' => $gemini?->id,
                    'fallback_model_id' => $geminiFlash?->id,
                    'system_prompt' => $systemPrompt,
                    'max_tokens_free' => 2048,
                    'max_tokens_pro' => 4096,
                    'max_input_length_free' => 1000,
                    'max_input_length_pro' => 10000,
                    'temperature' => 0.70,
                    'supported_languages' => $languages,
                    'default_language' => 'english_us',
                    'output_format' => 'text',
                    'show_language_selector' => true,
                    'enable_rtl_support' => true,
                ]
            );
        }

        $this->command->info('✅ 21 new AI tools seeded successfully!');
    }
}
