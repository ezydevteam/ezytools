<?php

namespace Database\Seeders;

use App\Models\EmailCampaign;
use Illuminate\Database\Seeder;

class EmailCampaignSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = [
            [
                'name'            => 'ঈদ বিশেষ অফার — 30% ছাড়',
                'subject'         => '🎉 ঈদ মোবারক! EzyTools Pro-তে 30% ছাড় — শুধু আপনার জন্য',
                'preheader'       => 'সীমিত সময়ের অফার — আজই Pro নিন',
                'body_heading'    => 'ঈদ মোবারক! 🌙 বিশেষ অফার আপনার জন্য',
                'body_content'    => '<p>এই ঈদে EzyTools আপনাকে একটি বিশেষ উপহার দিচ্ছে!</p><p>সীমিত সময়ের জন্য, <strong>EzyTools Pro</strong>-তে পাচ্ছেন <strong style="color:#6366F1;font-size:18px;">30% ছাড়</strong>। Unlimited AI tools, ad-free experience, এবং আরো অনেক কিছু।</p><p>নিচের কুপন কোড ব্যবহার করে এখনই আপগ্রেড করুন:</p>',
                'cta_text'        => 'এখনই Pro নিন — 30% ছাড়সহ →',
                'cta_url'         => config('app.url') . '/subscription',
                'target_audience' => 'free',
                'status'          => 'draft',
            ],
            [
                'name'            => 'নতুন AI Tools লঞ্চ',
                'subject'         => '🤖 ৫টি নতুন AI Tools এসেছে EzyTools-এ!',
                'preheader'       => 'Bangla AI Paraphraser, Code Explainer, এবং আরো',
                'body_heading'    => 'নতুন AI Tools এসেছে! 🚀',
                'body_content'    => '<p>আমরা ৫টি নতুন AI-powered tools যোগ করেছি EzyTools-এ:</p><div style="margin:20px 0;"><div style="padding:8px 0;border-bottom:1px solid #F1F5F9;display:flex;gap:8px;"><span>🤖</span><span><strong>AI Bangla Paraphraser</strong> — বাংলা টেক্সট সুন্দরভাবে পুনর্লিখন</span></div><div style="padding:8px 0;border-bottom:1px solid #F1F5F9;display:flex;gap:8px;"><span>💻</span><span><strong>AI Code Explainer</strong> — যেকোনো কোড সহজ ভাষায় বুঝুন</span></div><div style="padding:8px 0;border-bottom:1px solid #F1F5F9;display:flex;gap:8px;"><span>📝</span><span><strong>AI Study Notes</strong> — যেকোনো বিষয়ে নোটস তৈরি</span></div><div style="padding:8px 0;border-bottom:1px solid #F1F5F9;display:flex;gap:8px;"><span>💼</span><span><strong>AI Cover Letter</strong> — professional cover letter তৈরি</span></div><div style="padding:8px 0;display:flex;gap:8px;"><span>📊</span><span><strong>AI Business Proposal</strong> — BD business proposal template</span></div></div><p>সব tools <strong>সম্পূর্ণ ফ্রি</strong> — এখনই ব্যবহার করুন!</p>',
                'cta_text'        => 'নতুন Tools দেখুন →',
                'cta_url'         => config('app.url') . '/tools/ai-tools',
                'target_audience' => 'all',
                'status'          => 'draft',
            ],
        ];

        foreach ($campaigns as $data) {
            EmailCampaign::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
            $this->command->info("✓ Campaign: {$data['name']}");
        }
    }
}
