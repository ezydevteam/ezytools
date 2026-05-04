<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\AiProvider;
use App\Models\AiToolConfig;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Seeder;

class AiToolSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create AI category
        $aiCategory = ToolCategory::updateOrCreate(
            ['slug' => 'ai-tools'],
            [
                'name' => 'AI Studio',
                'description' => 'Browse our collection of AI tools and utilities.',
                'icon' => 'SparklesIcon',
                'is_active' => true,
                'order' => 9,
            ]
        );

        // Fetch providers and models for default config
        $openAi = AiProvider::where('name', 'openai')->first();
        $gemini = AiProvider::where('name', 'gemini')->first();

        $gptMini = AiModel::where('name', 'gpt-4o-mini')->first();
        $gptPro = AiModel::where('name', 'gpt-4o')->first();
        $geminiFlash = AiModel::where('name', 'gemini-2.5-flash')->first();

        $tools = [
            // First 5
            [
                'name' => 'AI Bangla Paraphraser',
                'short_description' => 'Rewrite and paraphrase text',
                'slug' => 'ai-paraphraser',
                'description' => 'Rewrite and paraphrase text in Bangla and English.',
                'description_bn' => 'Rewrite and paraphrase text in Bangla and English.',
                'component_name' => 'AiParaphraser',
                'icon' => 'DocumentTextIcon',
                'system_prompt' => "You are a professional language expert in Bangla and English.\nParaphrase the given text according to the user's settings.\nPreserve the original meaning but improve flow and vocabulary.\nOutput ONLY the paraphrased text, nothing else.",
            ],
            [
                'name' => 'AI Text Summarizer',
                'short_description' => 'Summarize long articles easily',
                'slug' => 'ai-summarizer',
                'description' => 'Quickly summarize long articles and documents.',
                'description_bn' => 'Quickly summarize long articles and documents.',
                'component_name' => 'AiSummarizer',
                'icon' => 'ArrowsPointingInIcon',
                'system_prompt' => "You are an expert summarizer.\nSummarize the provided text based on the user's settings (length, format, language).\nFocus on the main points and key takeaways.\nOutput ONLY the summary text.",
            ],
            [
                'name' => 'AI Grammar Checker',
                'short_description' => 'Check and correct grammar',
                'slug' => 'ai-grammar-checker',
                'description' => 'Check and correct grammar mistakes instantly.',
                'description_bn' => 'Check and correct grammar mistakes instantly.',
                'component_name' => 'AiGrammarChecker',
                'icon' => 'CheckBadgeIcon',
                'system_prompt' => "You are a grammar and writing expert.\nCheck the provided text for spelling, grammar, and punctuation errors.\nCorrect the text while maintaining the original tone and meaning.\nIf the text is in Bangla, ensure natural phrasing.\nReturn ONLY the corrected text.",
            ],
            [
                'name' => 'AI Translator',
                'short_description' => 'Translate text naturally',
                'slug' => 'ai-translator',
                'description' => 'Translate text naturally between Bangla and English.',
                'description_bn' => 'Translate text naturally between Bangla and English.',
                'component_name' => 'AiTranslator',
                'icon' => 'LanguageIcon',
                'system_prompt' => "You are a professional translator specializing in English and Bengali.\nTranslate the given text into the target language.\nEnsure the translation sounds natural and culturally appropriate.\nOutput ONLY the translated text.",
            ],
            [
                'name' => 'AI Email Writer',
                'short_description' => 'Draft professional emails',
                'slug' => 'ai-email-writer',
                'description' => 'Draft professional emails in seconds.',
                'description_bn' => 'Draft professional emails in seconds.',
                'component_name' => 'AiEmailWriter',
                'icon' => 'EnvelopeIcon',
                'system_prompt' => "You are an expert email copywriter.\nDraft an email based on the user's instructions.\nFormat the output clearly with 'Subject: [Subject line]' followed by the 'Body: [Email body]'.\nEnsure the tone matches the user's request.",
            ],
            // Next 15 Tools
            [
                'name' => 'AI Article Generator',
                'short_description' => 'Generate high-quality articles',
                'slug' => 'ai-article-generator',
                'description' => 'Generate high-quality articles from a topic.',
                'description_bn' => 'Generate high-quality articles from a topic.',
                'component_name' => 'AiArticleGenerator',
                'icon' => 'DocumentTextIcon',
                'system_prompt' => "You are an expert SEO article writer.\nWrite a comprehensive, engaging article on the topic provided.\nUse proper headings, formatting, and the requested language/tone.\nOutput ONLY the article.",
            ],
            [
                'name' => 'AI Title Generator',
                'short_description' => 'Create catchy titles',
                'slug' => 'ai-title-generator',
                'description' => 'Create catchy titles and headlines.',
                'description_bn' => 'Create catchy titles and headlines.',
                'component_name' => 'AiTitleGenerator',
                'icon' => 'SparklesIcon',
                'system_prompt' => "You are a professional copywriter.\nGenerate a list of catchy, clickable, and SEO-friendly titles based on the user's prompt.\nReturn the titles as a numbered list.",
            ],
            [
                'name' => 'AI Meta Description',
                'short_description' => 'Generate SEO meta descriptions',
                'slug' => 'ai-meta-description-generator',
                'description' => 'Generate SEO-friendly meta descriptions.',
                'description_bn' => 'Generate SEO-friendly meta descriptions.',
                'component_name' => 'AiMetaDescriptionGenerator',
                'icon' => 'GlobeAltIcon',
                'system_prompt' => "You are an SEO expert.\nWrite a compelling meta description (under 160 characters) for the given page content or title.\nEnsure it encourages clicks.",
            ],
            [
                'name' => 'AI YouTube Description',
                'short_description' => 'Write YouTube video descriptions',
                'slug' => 'ai-youtube-description-generator',
                'description' => 'Write engaging YouTube video descriptions.',
                'description_bn' => 'Write engaging YouTube video descriptions.',
                'component_name' => 'AiYoutubeDescriptionGenerator',
                'icon' => 'VideoCameraIcon',
                'system_prompt' => "You are a YouTube growth expert.\nWrite an engaging video description based on the provided video topic/details.\nInclude a strong hook, a summary, timestamps (if requested), and call-to-actions.",
            ],
            [
                'name' => 'AI Social Media Post',
                'short_description' => 'Generate social media posts',
                'slug' => 'ai-social-media-post-generator',
                'description' => 'Generate posts for Facebook, Twitter, LinkedIn.',
                'description_bn' => 'Generate posts for Facebook, Twitter, LinkedIn.',
                'component_name' => 'AiSocialMediaPostGenerator',
                'icon' => 'ShareIcon',
                'system_prompt' => "You are a social media manager.\nWrite an engaging social media post based on the given topic.\nTailor the formatting and tone to the selected platform.\nInclude relevant hashtags and emojis.",
            ],
            [
                'name' => 'AI Code Explainer',
                'short_description' => 'Explain complex code snippets',
                'slug' => 'ai-code-explainer',
                'description' => 'Explain complex code snippets simply.',
                'description_bn' => 'Explain complex code snippets simply.',
                'component_name' => 'AiCodeExplainer',
                'icon' => 'CodeBracketIcon',
                'system_prompt' => "You are an expert senior software engineer.\nExplain the provided code snippet clearly and concisely.\nBreak down the logic step-by-step so a beginner can understand it.",
            ],
            [
                'name' => 'AI Cover Letter Writer',
                'short_description' => 'Generate personalized cover letters',
                'slug' => 'ai-cover-letter-generator',
                'description' => 'Generate personalized cover letters for jobs.',
                'description_bn' => 'Generate personalized cover letters for jobs.',
                'component_name' => 'AiCoverLetterGenerator',
                'icon' => 'DocumentIcon',
                'system_prompt' => "You are an expert career coach.\nWrite a professional, compelling cover letter based on the provided job title, company, and applicant skills.\nFormat it properly as a formal letter.",
            ],
            [
                'name' => 'AI Resume Summary',
                'short_description' => 'Write impactful professional summaries',
                'slug' => 'ai-resume-summary-generator',
                'description' => 'Write impactful professional summaries for resumes.',
                'description_bn' => 'Write impactful professional summaries for resumes.',
                'component_name' => 'AiResumeSummaryGenerator',
                'icon' => 'BriefcaseIcon',
                'system_prompt' => "You are a professional resume writer.\nWrite a strong, concise professional summary (3-4 sentences) based on the provided role and experience.",
            ],
            [
                'name' => 'AI Interview Questions',
                'short_description' => 'Generate practice interview questions',
                'slug' => 'ai-interview-questions-generator',
                'description' => 'Generate practice interview questions for any role.',
                'description_bn' => 'Generate practice interview questions for any role.',
                'component_name' => 'AiInterviewQuestionsGenerator',
                'icon' => 'UserGroupIcon',
                'system_prompt' => "You are an expert HR recruiter.\nGenerate a list of relevant, challenging interview questions based on the provided job title and experience level.",
            ],
            [
                'name' => 'AI Tagline Generator',
                'short_description' => 'Create memorable taglines',
                'slug' => 'ai-tagline-generator',
                'description' => 'Create memorable taglines and slogans.',
                'description_bn' => 'Create memorable taglines and slogans.',
                'component_name' => 'AiTaglineGenerator',
                'icon' => 'ChatBubbleBottomCenterTextIcon',
                'system_prompt' => "You are a brilliant brand strategist.\nGenerate 5-10 memorable, catchy taglines or slogans based on the provided business or product description.",
            ],
            [
                'name' => 'AI Essay Outline',
                'short_description' => 'Structure your essay with an outline',
                'slug' => 'ai-essay-outline-generator',
                'description' => 'Structure your essay with a detailed outline.',
                'description_bn' => 'Structure your essay with a detailed outline.',
                'component_name' => 'AiEssayOutlineGenerator',
                'icon' => 'ListBulletIcon',
                'system_prompt' => "You are an academic writing tutor.\nCreate a structured, detailed outline for an essay based on the provided topic.\nInclude an introduction, main body paragraphs with key points, and a conclusion.",
            ],
            [
                'name' => 'AI Reply Generator',
                'short_description' => 'Generate smart replies for messages',
                'slug' => 'ai-reply-generator',
                'description' => 'Generate smart replies for messages and emails.',
                'description_bn' => 'Generate smart replies for messages and emails.',
                'component_name' => 'AiReplyGenerator',
                'icon' => 'ChatBubbleLeftRightIcon',
                'system_prompt' => "You are an expert communicator.\nGenerate a polite, appropriate reply to the provided message/email based on the user's preferred stance (e.g., agree, decline, thankful).",
            ],
            [
                'name' => 'AI Product Description',
                'short_description' => 'Write e-commerce product descriptions',
                'slug' => 'ai-product-description-generator',
                'description' => 'Write high-converting e-commerce product descriptions.',
                'description_bn' => 'Write high-converting e-commerce product descriptions.',
                'component_name' => 'AiProductDescriptionGenerator',
                'icon' => 'ShoppingBagIcon',
                'system_prompt' => "You are an expert e-commerce copywriter.\nWrite a persuasive, engaging product description based on the provided product features.\nHighlight the benefits, not just features.",
            ],
            [
                'name' => 'AI Startup Idea Generator',
                'short_description' => 'Brainstorm business and startup ideas',
                'slug' => 'ai-startup-idea-generator',
                'description' => 'Brainstorm innovative business and startup ideas.',
                'description_bn' => 'Brainstorm innovative business and startup ideas.',
                'component_name' => 'AiStartupIdeaGenerator',
                'icon' => 'LightBulbIcon',
                'system_prompt' => "You are a successful serial entrepreneur.\nGenerate innovative, viable startup ideas based on the provided industry or interest.\nProvide a brief value proposition for each idea.",
            ],
            [
                'name' => 'AI Sentence Expander',
                'short_description' => 'Expand short sentences into paragraphs',
                'slug' => 'ai-sentence-expander',
                'component_name' => 'AiSentenceExpander',
                'icon' => 'ArrowsRightLeftIcon',
                'system_prompt' => "You are a creative writer.\nExpand the provided short sentence or thought into a well-written, descriptive paragraph.\nMaintain the core idea but add detail and flair.",
            ],
            [
                'name' => 'AI Prompt Expert',
                'short_description' => 'Generate expert prompts for other AI models',
                'slug' => 'ai-prompt-expert',
                'component_name' => 'AiPromptExpert',
                'icon' => 'CpuChipIcon',
                'system_prompt' => "You are a Master Prompt Engineer.\nBased on the user's raw idea and settings (Target AI, Purpose, Language), generate a highly optimized, detailed, and effective prompt that they can copy-paste into the selected AI.\nMake the prompt clear, structured with necessary constraints, context, and role definitions.\nReturn ONLY the generated prompt.",
            ],
        ];

        foreach ($tools as $index => $toolData) {
            $tool = Tool::updateOrCreate(
                ['slug' => $toolData['slug']],
                [
                    'category_id' => $aiCategory->id,
                    'name' => $toolData['name'],
                    'short_description' => $toolData['short_description'],
                    'component_name' => $toolData['component_name'],
                    'icon' => $toolData['icon'],
                    'is_active' => true,
                    'is_premium' => false,
                    'order' => $index + 1,
                ]
            );

            AiToolConfig::updateOrCreate(
                ['tool_id' => $tool->id],
                [
                    'provider_id' => $openAi->id ?? null,
                    'model_id' => $gptMini->id ?? null,
                    'pro_provider_id' => $openAi->id ?? null,
                    'pro_model_id' => $gptPro->id ?? null,
                    'fallback_provider_id' => $gemini->id ?? null,
                    'fallback_model_id' => $geminiFlash->id ?? null,
                    'system_prompt' => $toolData['system_prompt'],
                    'max_tokens_free' => 2048,
                    'max_tokens_pro' => 4096,
                    'max_input_length_free' => 1000,
                    'max_input_length_pro' => 10000,
                    'temperature' => 0.70,
                ]
            );
        }
    }
}
