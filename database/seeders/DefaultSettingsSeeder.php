<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'EzyTools', 'type' => 'text', 'group' => 'general', 'label' => 'Site Name'],
            ['key' => 'site_tagline', 'value' => 'Free Online AI Tools Collection', 'type' => 'text', 'group' => 'general', 'label' => 'Tagline'],
            ['key' => 'site_logo', 'value' => '', 'type' => 'image', 'group' => 'general', 'label' => 'Site Logo'],
            ['key' => 'site_favicon', 'value' => '', 'type' => 'image', 'group' => 'general', 'label' => 'Favicon'],
            ['key' => 'contact_email', 'value' => 'hello@ezytools.com', 'type' => 'text', 'group' => 'general', 'label' => 'Contact Email'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'general', 'label' => 'Maintenance Mode'],
            ['key' => 'default_language', 'value' => 'en', 'type' => 'text', 'group' => 'general', 'label' => 'Default Language'],

            // SEO
            ['key' => 'meta_title', 'value' => 'EzyTools - Free Online AI Tools Collection', 'type' => 'text', 'group' => 'seo', 'label' => 'Default Meta Title'],
            ['key' => 'meta_description', 'value' => 'Collection of 60+ free AI online tools including converters, calculators, and more.', 'type' => 'textarea', 'group' => 'seo', 'label' => 'Default Meta Description'],
            ['key' => 'meta_keywords', 'value' => 'tools, free online tools, converters, calculators', 'type' => 'textarea', 'group' => 'seo', 'label' => 'Default Meta Keywords'],
            ['key' => 'google_analytics_id', 'value' => '', 'type' => 'text', 'group' => 'seo', 'label' => 'Google Analytics ID'],
            ['key' => 'google_search_console_code', 'value' => '', 'type' => 'text', 'group' => 'seo', 'label' => 'Google Search Console Verification'],
            ['key' => 'facebook_pixel_id', 'value' => '', 'type' => 'text', 'group' => 'seo', 'label' => 'Facebook Pixel ID'],
            ['key' => 'og_default_image', 'value' => '', 'type' => 'image', 'group' => 'seo', 'label' => 'Default OG Image'],
            ['key' => 'header_scripts', 'value' => '', 'type' => 'textarea', 'group' => 'seo', 'label' => 'Header Scripts'],
            ['key' => 'footer_scripts', 'value' => '', 'type' => 'textarea', 'group' => 'seo', 'label' => 'Footer Scripts'],

            // Subscription — BDT (Bangladesh)
            ['key' => 'pro_price_monthly', 'value' => '299', 'type' => 'number', 'group' => 'subscriptions', 'label' => 'Pro Monthly (BDT)'],
            ['key' => 'pro_price_yearly', 'value' => '2499', 'type' => 'number', 'group' => 'subscriptions', 'label' => 'Pro Yearly (BDT)'],
            // Subscription — INR (India)
            ['key' => 'pro_price_monthly_inr', 'value' => '249', 'type' => 'number', 'group' => 'subscriptions', 'label' => 'Pro Monthly (INR)'],
            ['key' => 'pro_price_yearly_inr', 'value' => '1999', 'type' => 'number', 'group' => 'subscriptions', 'label' => 'Pro Yearly (INR)'],
            // Subscription — USD (International)
            ['key' => 'pro_price_monthly_usd', 'value' => '5', 'type' => 'number', 'group' => 'subscriptions', 'label' => 'Pro Monthly (USD)'],
            ['key' => 'pro_price_yearly_usd', 'value' => '49', 'type' => 'number', 'group' => 'subscriptions', 'label' => 'Pro Yearly (USD)'],

            // Social
            ['key' => 'social_facebook', 'value' => 'https://facebook.com', 'type' => 'text', 'group' => 'social', 'label' => 'Facebook URL'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com', 'type' => 'text', 'group' => 'social', 'label' => 'YouTube URL'],
            ['key' => 'social_reddit', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Reddit URL'],
            ['key' => 'social_telegram', 'value' => '', 'type' => 'text', 'group' => 'social', 'label' => 'Telegram URL'],

            // API Credentials
            ['key' => 'google_client_id', 'value' => '', 'type' => 'text', 'group' => 'api', 'label' => 'Google OAuth Client ID'],
            ['key' => 'google_client_secret', 'value' => '', 'type' => 'text', 'group' => 'api', 'label' => 'Google OAuth Client Secret'],
            ['key' => 'sms_sid', 'value' => '', 'type' => 'text', 'group' => 'api', 'label' => 'SMS SID (Twilio)'],
            ['key' => 'sms_token', 'value' => '', 'type' => 'text', 'group' => 'api', 'label' => 'SMS Token/API Key'],
            ['key' => 'sms_from', 'value' => '', 'type' => 'text', 'group' => 'api', 'label' => 'SMS From Number/ID'],
            ['key' => 'google_pagespeed_api_key', 'value' => '', 'type' => 'text', 'group' => 'api', 'label' => 'Google PageSpeed API Key'],
            ['key' => 'analytics_property_id', 'value' => '', 'type' => 'text', 'group' => 'api', 'label' => 'GA4 Property ID (For Dashboard Stats)'],
            ['key' => 'analytics_credentials_json', 'value' => '', 'type' => 'textarea', 'group' => 'api', 'label' => 'Google Service Account JSON (For Dashboard Stats)'],

            // Mail Settings
            ['key' => 'mail_host', 'value' => '127.0.0.1', 'type' => 'text', 'group' => 'mail', 'label' => 'SMTP Host'],
            ['key' => 'mail_port', 'value' => '1025', 'type' => 'number', 'group' => 'mail', 'label' => 'SMTP Port'],
            ['key' => 'mail_username', 'value' => '', 'type' => 'text', 'group' => 'mail', 'label' => 'SMTP Username'],
            ['key' => 'mail_password', 'value' => '', 'type' => 'text', 'group' => 'mail', 'label' => 'SMTP Password'],
            ['key' => 'mail_encryption', 'value' => 'null', 'type' => 'text', 'group' => 'mail', 'label' => 'SMTP Encryption'],
            ['key' => 'mail_from_address', 'value' => 'hello@ezytools.com', 'type' => 'text', 'group' => 'mail', 'label' => 'Mail From Address'],
            ['key' => 'mail_from_name', 'value' => 'EzyTools', 'type' => 'text', 'group' => 'mail', 'label' => 'Mail From Name'],

            // Payment Settings
            ['key' => 'uddoktapay_api_key', 'value' => '', 'type' => 'text', 'group' => 'payment_gateways', 'label' => 'UddoktaPay API Key'],
            ['key' => 'uddoktapay_base_url', 'value' => 'https://sandbox.uddoktapay.com/api/checkout-v2', 'type' => 'text', 'group' => 'payment_gateways', 'label' => 'UddoktaPay Base URL'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
