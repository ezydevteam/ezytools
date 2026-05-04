<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\ToolReview;
use App\Models\ToolSeoContent;
use App\Models\ToolFaq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToolContentSeeder extends Seeder
{
    public function run(): void
    {
        $tools = Tool::all();

        foreach ($tools as $tool) {
            $name = $tool->name;

            // 1. Usage count: random 1K-20K
            $tool->update(['usage_count' => rand(1000, 20000)]);

            // 2. Ratings: 5-25 reviews with avg 4.0-5.0
            $this->seedRatings($tool);

            // 3-5. SEO Content + FAQs
            $this->seedSeoContent($tool, $name);
            $this->seedFaqs($tool, $name);

            $this->command->info("✓ Seeded: {$name}");
        }
    }

    private function seedRatings(Tool $tool): void
    {
        // Clear existing reviews for this tool
        ToolReview::where('tool_id', $tool->id)->delete();

        $count = rand(5, 25);
        for ($i = 0; $i < $count; $i++) {
            // Weight towards 4-5 stars for realistic avg
            $rand = rand(1, 100);
            if ($rand <= 45) $rating = 5;
            elseif ($rand <= 80) $rating = 4;
            elseif ($rand <= 92) $rating = 3;
            else $rating = rand(1, 2);

            ToolReview::create([
                'tool_id'    => $tool->id,
                'rating'     => $rating,
                'ip_address' => '192.168.1.' . rand(1, 254),
                'is_approved'=> true,
                'created_at' => now()->subDays(rand(1, 90)),
            ]);
        }
    }

    private function seedSeoContent(Tool $tool, string $name): void
    {
        $about = $this->generateAbout($name);
        $howTo = $this->generateHowTo($name);

        ToolSeoContent::updateOrCreate(
            ['tool_id' => $tool->id],
            [
                'about_title'       => "এই টুল সম্পর্কে",
                'about_title_en'    => "About {$name}",
                'about_content'     => $about['bn'],
                'about_content_en'  => $about['en'],
                'how_to_title'      => "কীভাবে ব্যবহার করবেন",
                'how_to_title_en'   => "How to Use {$name}",
                'how_to_content'    => $howTo['content_bn'],
                'how_to_content_en' => $howTo['content_en'],
                'how_to_steps'      => $howTo['steps'],
                'use_cases'         => $this->generateUseCases($name),
                'last_updated_at'   => now(),
            ]
        );
    }

    private function seedFaqs(Tool $tool, string $name): void
    {
        // Clear existing FAQs
        ToolFaq::where('tool_id', $tool->id)->delete();

        $faqs = $this->generateFaqs($name);
        foreach ($faqs as $i => $faq) {
            ToolFaq::create([
                'tool_id'     => $tool->id,
                'question'    => $faq['q_en'],
                'question_bn' => $faq['q_bn'],
                'answer'      => $faq['a_en'],
                'answer_bn'   => $faq['a_bn'],
                'order'       => $i,
            ]);
        }
    }

    // ─── Content Generators ──────────────────────────────────────

    private function generateAbout(string $name): array
    {
        return [
            'en' => "**{$name}** is a free, powerful online tool available on EzyTools. It helps you accomplish your task quickly and efficiently without needing to install any software.\n\nOur {$name} is designed with simplicity in mind — whether you're a student, professional, or casual user, you can get results in just a few clicks. The tool works entirely in your browser, ensuring your data stays private and secure.\n\n**Key Features:**\n- Completely free to use with no registration required\n- Works on all devices — desktop, tablet, and mobile\n- Fast processing with instant results\n- Clean, modern interface that's easy to navigate\n- Data privacy — your files are processed locally whenever possible",

            'bn' => "**{$name}** হল EzyTools-এ উপলব্ধ একটি বিনামূল্যে, শক্তিশালী অনলাইন টুল। এটি আপনাকে কোনো সফটওয়্যার ইনস্টল করা ছাড়াই দ্রুত এবং দক্ষতার সাথে কাজ সম্পন্ন করতে সাহায্য করে।\n\nআমাদের {$name} সরলতার কথা মাথায় রেখে ডিজাইন করা হয়েছে — আপনি ছাত্র, পেশাদার বা সাধারণ ব্যবহারকারী যাই হোন না কেন, মাত্র কয়েকটি ক্লিকেই ফলাফল পেতে পারেন। টুলটি সম্পূর্ণ আপনার ব্রাউজারে কাজ করে, যা আপনার ডেটার গোপনীয়তা নিশ্চিত করে।\n\n**প্রধান বৈশিষ্ট্য:**\n- কোনো রেজিস্ট্রেশন ছাড়াই সম্পূর্ণ বিনামূল্যে ব্যবহারযোগ্য\n- সব ডিভাইসে কাজ করে — ডেস্কটপ, ট্যাবলেট এবং মোবাইল\n- তাৎক্ষণিক ফলাফলসহ দ্রুত প্রসেসিং\n- পরিষ্কার, আধুনিক ইন্টারফেস\n- ডেটা গোপনীয়তা — আপনার ফাইল যতটা সম্ভব স্থানীয়ভাবে প্রসেস করা হয়",
        ];
    }

    private function generateHowTo(string $name): array
    {
        return [
            'content_en' => "Using **{$name}** is straightforward and takes just a few seconds. Follow the simple steps below to get started. No account creation or software download is needed — everything works right in your browser.",

            'content_bn' => "**{$name}** ব্যবহার করা খুবই সহজ এবং মাত্র কয়েক সেকেন্ড সময় লাগে। শুরু করতে নীচের সহজ ধাপগুলি অনুসরণ করুন। কোনো অ্যাকাউন্ট তৈরি বা সফটওয়্যার ডাউনলোড করার দরকার নেই — সবকিছু আপনার ব্রাউজারেই কাজ করে।",

            'steps' => [
                [
                    'step' => 'টুলটি খুলুন',
                    'step_en' => 'Open the Tool',
                    'description' => "EzyTools-এ {$name} পেজটি ভিজিট করুন। কোনো লগইন বা রেজিস্ট্রেশন প্রয়োজন নেই।",
                    'description_en' => "Visit the {$name} page on EzyTools. No login or registration is required.",
                ],
                [
                    'step' => 'আপনার ইনপুট দিন',
                    'step_en' => 'Provide Your Input',
                    'description' => 'প্রয়োজনীয় তথ্য, টেক্সট, বা ফাইল ইনপুট এরিয়াতে দিন বা আপলোড করুন।',
                    'description_en' => 'Enter the required data, text, or upload your file in the input area.',
                ],
                [
                    'step' => 'সেটিংস কনফিগার করুন',
                    'step_en' => 'Configure Settings',
                    'description' => 'প্রয়োজনে উপলব্ধ অপশন বা সেটিংস পরিবর্তন করুন।',
                    'description_en' => 'Adjust the available options or settings if needed for your specific use case.',
                ],
                [
                    'step' => 'ফলাফল তৈরি করুন',
                    'step_en' => 'Generate Results',
                    'description' => 'অ্যাকশন বাটনে ক্লিক করুন এবং তাৎক্ষণিক ফলাফল পান।',
                    'description_en' => 'Click the action button and get your results instantly.',
                ],
                [
                    'step' => 'ফলাফল কপি বা ডাউনলোড করুন',
                    'step_en' => 'Copy or Download',
                    'description' => 'আপনার ফলাফল কপি করুন, ডাউনলোড করুন, বা সরাসরি শেয়ার করুন।',
                    'description_en' => 'Copy your results to clipboard, download the output file, or share directly.',
                ],
            ],
        ];
    }

    private function generateUseCases(string $name): array
    {
        $generic = [
            [
                'title' => 'শিক্ষার্থীদের জন্য',
                'title_en' => 'For Students',
                'description' => "শিক্ষার্থীরা তাদের পড়াশোনা এবং প্রজেক্টের কাজে {$name} ব্যবহার করতে পারেন।",
                'description_en' => "Students can use {$name} for their studies, assignments, and project work.",
            ],
            [
                'title' => 'পেশাদারদের জন্য',
                'title_en' => 'For Professionals',
                'description' => "পেশাদাররা দৈনন্দিন কাজের দক্ষতা বাড়াতে এই টুলটি ব্যবহার করতে পারেন।",
                'description_en' => "Professionals can use this tool to boost productivity in their daily workflow.",
            ],
            [
                'title' => 'ফ্রিল্যান্সারদের জন্য',
                'title_en' => 'For Freelancers',
                'description' => "ফ্রিল্যান্সাররা তাদের ক্লায়েন্টদের কাজ দ্রুত সম্পন্ন করতে এটি ব্যবহার করতে পারেন।",
                'description_en' => "Freelancers can speed up client deliverables using {$name}.",
            ],
            [
                'title' => 'ব্যবসায়ীদের জন্য',
                'title_en' => 'For Business Owners',
                'description' => "ব্যবসায়ীরা তাদের প্রতিদিনের ব্যবসায়িক কাজে এই টুলটি কাজে লাগাতে পারেন।",
                'description_en' => "Business owners can leverage {$name} for their daily operations and tasks.",
            ],
        ];

        return $generic;
    }

    private function generateFaqs(string $name): array
    {
        return [
            [
                'q_en' => "Is {$name} free to use?",
                'q_bn' => "{$name} কি বিনামূল্যে ব্যবহার করা যায়?",
                'a_en' => "Yes! {$name} is completely free to use. You don't need to create an account or pay anything. Simply visit the tool page and start using it right away.",
                'a_bn' => "হ্যাঁ! {$name} সম্পূর্ণ বিনামূল্যে ব্যবহারযোগ্য। আপনাকে কোনো অ্যাকাউন্ট তৈরি করতে বা কোনো অর্থ প্রদান করতে হবে না। শুধু টুল পেজটি ভিজিট করুন এবং সরাসরি ব্যবহার শুরু করুন।",
            ],
            [
                'q_en' => "Does {$name} work on mobile devices?",
                'q_bn' => "{$name} কি মোবাইল ডিভাইসে কাজ করে?",
                'a_en' => "Absolutely! {$name} is fully responsive and works seamlessly on smartphones, tablets, and desktop computers. You can use it from any modern web browser.",
                'a_bn' => "অবশ্যই! {$name} সম্পূর্ণ রেসপন্সিভ এবং স্মার্টফোন, ট্যাবলেট এবং ডেস্কটপ কম্পিউটারে নিরবচ্ছিন্নভাবে কাজ করে। আপনি যেকোনো আধুনিক ওয়েব ব্রাউজার থেকে এটি ব্যবহার করতে পারেন।",
            ],
            [
                'q_en' => "Is my data safe when using {$name}?",
                'q_bn' => "{$name} ব্যবহার করার সময় আমার ডেটা কি নিরাপদ?",
                'a_en' => "Yes, your privacy is our top priority. Most processing happens directly in your browser, meaning your data never leaves your device. We do not store or share any user data.",
                'a_bn' => "হ্যাঁ, আপনার গোপনীয়তা আমাদের সর্বোচ্চ অগ্রাধিকার। বেশিরভাগ প্রসেসিং সরাসরি আপনার ব্রাউজারে হয়, অর্থাৎ আপনার ডেটা আপনার ডিভাইস ছাড়ে না। আমরা কোনো ব্যবহারকারীর ডেটা সংরক্ষণ বা শেয়ার করি না।",
            ],
            [
                'q_en' => "Do I need to install any software to use {$name}?",
                'q_bn' => "{$name} ব্যবহার করতে কি কোনো সফটওয়্যার ইনস্টল করতে হবে?",
                'a_en' => "No installation is needed. {$name} runs entirely in your web browser. Just open the tool page and you're ready to go. It works on Chrome, Firefox, Safari, Edge, and other modern browsers.",
                'a_bn' => "কোনো ইনস্টলেশনের প্রয়োজন নেই। {$name} সম্পূর্ণ আপনার ওয়েব ব্রাউজারে চলে। শুধু টুল পেজটি খুলুন এবং আপনি প্রস্তুত। এটি Chrome, Firefox, Safari, Edge এবং অন্যান্য আধুনিক ব্রাউজারে কাজ করে।",
            ],
            [
                'q_en' => "Is there a daily usage limit for {$name}?",
                'q_bn' => "{$name}-এর কি কোনো দৈনিক ব্যবহার সীমা আছে?",
                'a_en' => "Free users have a generous daily limit. If you need unlimited usage, you can upgrade to our Pro plan which removes all restrictions and provides priority processing.",
                'a_bn' => "বিনামূল্যে ব্যবহারকারীদের জন্য পর্যাপ্ত দৈনিক সীমা রয়েছে। আপনার যদি সীমাহীন ব্যবহারের প্রয়োজন হয়, তাহলে আমাদের Pro প্ল্যানে আপগ্রেড করতে পারেন যা সব সীমাবদ্ধতা সরিয়ে দেয় এবং অগ্রাধিকার প্রসেসিং প্রদান করে।",
            ],
        ];
    }
}
