<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\ToolSeoContent;
use App\Models\ToolFaq;
use App\Models\ToolReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoContentSeeder extends Seeder
{
    public function run(): void
    {
        $toolsData = [
            1 => [ // Bijoy to Unicode
                'seo' => [
                    'how_to_title' => 'কীভাবে বিজয় টু ইউনিকোড ব্যবহার করবেন',
                    'how_to_title_en' => 'How to Use Bijoy to Unicode Converter',
                    'how_to_steps' => [
                        ['step' => 'বিজয় টেক্সট পেস্ট করুন', 'step_en' => 'Paste Bijoy text', 'description' => 'আপনার বিজয় ফন্টের টেক্সট ইনপুট বক্সে পেস্ট করুন', 'description_en' => 'Paste your Bijoy font text into the input box'],
                        ['step' => 'কনভার্ট বাটনে ক্লিক করুন', 'step_en' => 'Click Convert', 'description' => 'স্বয়ংক্রিয়ভাবে ইউনিকোডে রূপান্তরিত হবে', 'description_en' => 'Text will be automatically converted to Unicode'],
                        ['step' => 'ফলাফল কপি করুন', 'step_en' => 'Copy result', 'description' => 'কপি বাটনে ক্লিক করে ইউনিকোড টেক্সট কপি করুন', 'description_en' => 'Click copy to get the Unicode text'],
                    ],
                    'about_content' => 'বিজয় টু ইউনিকোড কনভার্টার বাংলাদেশের সবচেয়ে বেশি ব্যবহৃত টেক্সট কনভার্সন টুল।',
                    'about_content_en' => 'Bijoy to Unicode Converter is the most used text conversion tool in Bangladesh.',
                ],
                'faqs' => [
                    ['question' => 'What is Bijoy encoding?', 'question_bn' => 'বিজয় এনকোডিং কী?', 'answer' => 'Bijoy is a popular Bengali keyboard layout that uses ASCII encoding for Bengali characters.', 'answer_bn' => 'বিজয় হলো একটি জনপ্রিয় বাংলা কীবোর্ড লেআউট যা ASCII এনকোডিং ব্যবহার করে।'],
                    ['question' => 'Is it free to use?', 'question_bn' => 'এটি কি বিনামূল্যে?', 'answer' => 'Yes, completely free with no limits.', 'answer_bn' => 'হ্যাঁ, সম্পূর্ণ বিনামূল্যে।'],
                ],
                'related' => [2, 3, 4],
            ],
            5 => [ // Land Converter
                'seo' => [
                    'how_to_title' => 'কীভাবে জমি পরিমাপ রূপান্তর করবেন',
                    'how_to_title_en' => 'How to Convert Land Measurements',
                    'how_to_steps' => [
                        ['step' => 'একক নির্বাচন করুন', 'step_en' => 'Select unit', 'description' => 'কাঠা, বিঘা, একর থেকে বেছে নিন', 'description_en' => 'Choose from Katha, Bigha, Acre etc.'],
                        ['step' => 'পরিমাণ লিখুন', 'step_en' => 'Enter amount', 'description' => 'যে পরিমাণ রূপান্তর করতে চান তা লিখুন', 'description_en' => 'Enter the amount you want to convert'],
                        ['step' => 'ফলাফল দেখুন', 'step_en' => 'See results', 'description' => 'সব এককে রূপান্তরিত ফলাফল দেখুন', 'description_en' => 'See converted results in all units'],
                    ],
                    'about_content' => 'বাংলাদেশের জমি পরিমাপের বিভিন্ন একক সহজে রূপান্তর করুন।',
                    'about_content_en' => 'Easily convert between various land measurement units used in Bangladesh.',
                ],
                'faqs' => [
                    ['question' => 'How many decimal in 1 Katha?', 'question_bn' => '১ কাঠায় কত শতাংশ?', 'answer' => '1 Katha equals 1.65 decimal approximately.', 'answer_bn' => '১ কাঠা প্রায় ১.৬৫ শতাংশ।'],
                    ['question' => 'What is Bigha?', 'question_bn' => 'বিঘা কী?', 'answer' => 'Bigha is a traditional land measurement unit used in South Asia.', 'answer_bn' => 'বিঘা দক্ষিণ এশিয়ায় ব্যবহৃত একটি ঐতিহ্যবাহী জমি পরিমাপের একক।'],
                ],
                'related' => [6, 7, 8],
            ],
            6 => [ // EMI Calculator
                'seo' => [
                    'how_to_title' => 'কীভাবে EMI হিসাব করবেন',
                    'how_to_title_en' => 'How to Calculate EMI',
                    'how_to_steps' => [
                        ['step' => 'ঋণের পরিমাণ লিখুন', 'step_en' => 'Enter loan amount', 'description' => 'মোট ঋণের টাকার পরিমাণ লিখুন', 'description_en' => 'Enter total loan amount'],
                        ['step' => 'সুদের হার দিন', 'step_en' => 'Set interest rate', 'description' => 'বার্ষিক সুদের হার শতকরা লিখুন', 'description_en' => 'Enter annual interest rate percentage'],
                        ['step' => 'মেয়াদ নির্ধারণ করুন', 'step_en' => 'Set tenure', 'description' => 'ঋণের মেয়াদ মাসে লিখুন', 'description_en' => 'Enter loan tenure in months'],
                    ],
                    'about_content' => 'সহজে আপনার মাসিক কিস্তি (EMI) হিসাব করুন।',
                    'about_content_en' => 'Easily calculate your Equated Monthly Installment (EMI).',
                ],
                'faqs' => [
                    ['question' => 'What is EMI?', 'question_bn' => 'EMI কী?', 'answer' => 'EMI stands for Equated Monthly Installment - a fixed payment amount made by a borrower to a lender at a specified date each month.', 'answer_bn' => 'EMI হলো সমান মাসিক কিস্তি।'],
                ],
                'related' => [5, 7, 9],
            ],
        ];

        $reviewNames = ['রাহুল আহমেদ', 'সুমাইয়া খান', 'তানভীর হাসান', 'নুসরাত জাহান', 'আরিফ হোসেন', 'মিতু দেবনাথ', 'শাকিল আহমেদ', 'রুমানা আক্তার', 'জাহিদ করিম', 'ফাতিমা বেগম'];
        $reviewTexts = [
            'অসাধারণ টুল! খুবই সহজ এবং দ্রুত কাজ করে।',
            'অনেক কাজের টুল, প্রতিদিন ব্যবহার করি।',
            'ফ্রি হওয়ায় অনেক ভালো লাগলো। ধন্যবাদ!',
            'সুন্দর ডিজাইন এবং ভালো কাজ করে।',
            'এত সহজে কাজ হয়ে যায়, আগে জানতাম না!',
        ];

        foreach ($toolsData as $toolId => $data) {
            // SEO Content
            ToolSeoContent::updateOrCreate(
                ['tool_id' => $toolId],
                [...$data['seo'], 'last_updated_at' => now()]
            );

            // FAQs
            foreach ($data['faqs'] as $i => $faq) {
                ToolFaq::updateOrCreate(
                    ['tool_id' => $toolId, 'question' => $faq['question']],
                    [...$faq, 'order' => $i, 'is_active' => true]
                );
            }

            // Related tools
            $syncData = [];
            foreach ($data['related'] as $i => $relatedId) {
                $syncData[$relatedId] = ['relation_type' => 'similar', 'order' => $i];
            }
            Tool::find($toolId)?->relatedTools()->syncWithoutDetaching($syncData);

            // Sample reviews
            for ($r = 0; $r < 5; $r++) {
                ToolReview::firstOrCreate(
                    ['tool_id' => $toolId, 'guest_name' => $reviewNames[$r]],
                    [
                        'rating' => rand(4, 5),
                        'review_text' => $reviewTexts[$r],
                        'is_approved' => true,
                        'is_featured' => $r === 0,
                        'helpful_count' => rand(0, 15),
                        'ip_address' => '127.0.0.' . ($r + 1),
                    ]
                );
            }
        }
    }
}
