<template>
    <AppLayout title="Privacy Policy — EzyTools">
        <Head>
            <meta name="description" content="Read EzyTools' Privacy Policy to understand how we collect, use, and protect your personal data." />
            <link rel="canonical" :href="route('pages.privacy')" />
        </Head>

        <div class="max-w-4xl mx-auto py-12 md:py-16">
            <!-- Header -->
            <div class="mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 text-xs font-semibold mb-4">
                    <ShieldCheckIcon class="w-4 h-4" />
                    Legal
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">Privacy Policy</h1>
                <p class="text-sm text-surface-500 dark:text-surface-400">Last updated: April 30, 2026</p>
            </div>

            <!-- Content -->
            <div class="prose prose-surface dark:prose-invert max-w-none space-y-8">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 text-sm text-blue-800 dark:text-blue-200 leading-relaxed">
                    <strong>TL;DR:</strong> We collect minimal data, never sell your information, and process most tool data directly in your browser. Your privacy matters to us.
                </div>

                <section v-for="s in sections" :key="s.title" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 text-sm font-bold">{{ s.num }}</span>
                        {{ s.title }}
                    </h2>
                    <div class="text-sm text-surface-600 dark:text-surface-400 leading-relaxed space-y-3" v-html="s.body"></div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ShieldCheckIcon } from '@heroicons/vue/24/outline';

const sections = [
    {
        num: '1',
        title: 'Information We Collect',
        body: `
            <p>We collect information that you voluntarily provide when you register an account, contact us, or subscribe to our services:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li><strong>Account Data:</strong> Username, email address, and hashed password when you create an account.</li>
                <li><strong>Usage Data:</strong> Anonymous analytics such as pages visited, tools used, and session duration via privacy-friendly analytics.</li>
                <li><strong>Payment Data:</strong> Processed securely by our third-party payment provider (UddoktaPay). We never store your card or payment details.</li>
                <li><strong>Device Data:</strong> Browser type, operating system, and device type for optimizing your experience.</li>
            </ul>
            <p><strong>Importantly:</strong> Text, files, images, or content you process using our tools is handled in your browser or temporarily on our servers and is never stored permanently.</p>
        `
    },
    {
        num: '2',
        title: 'How We Use Your Information',
        body: `
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>To provide, operate, and maintain our tools and services.</li>
                <li>To process your subscription and manage your account.</li>
                <li>To communicate important updates, security alerts, and support responses.</li>
                <li>To analyze usage patterns anonymously and improve our platform.</li>
                <li>To enforce our Terms of Service and prevent abuse.</li>
            </ul>
        `
    },
    {
        num: '3',
        title: 'AI Tool Data Processing',
        body: `
            <p>When you use our AI-powered tools (e.g., AI Translator, AI Article Generator), the text you submit is sent to third-party AI providers (such as Google Gemini, OpenAI) for processing. We do <strong>not</strong> store AI inputs or outputs beyond the duration of the request. These providers have their own privacy policies governing data handling.</p>
        `
    },
    {
        num: '4',
        title: 'Cookies',
        body: `
            <p>We use essential cookies for authentication and session management. We do not use third-party tracking cookies or advertising trackers. Pro users enjoy a completely ad-free experience.</p>
        `
    },
    {
        num: '5',
        title: 'Data Sharing',
        body: `
            <p>We do <strong>not</strong> sell, rent, or share your personal information with third parties for marketing purposes. Data may be shared only in the following cases:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>With payment processors to handle subscriptions.</li>
                <li>With AI service providers to process your AI tool requests (text only, no personal identity data).</li>
                <li>When required by law or to protect the rights and safety of our users.</li>
            </ul>
        `
    },
    {
        num: '6',
        title: 'Data Retention & Deletion',
        body: `
            <p>Account data is retained as long as your account is active. You may request deletion of your account and associated data at any time by contacting us at <a href="mailto:support@ezytools.com" class="text-primary-600 hover:underline">support@ezytools.com</a>. We will process deletion requests within 30 days.</p>
        `
    },
    {
        num: '7',
        title: 'Security',
        body: `
            <p>We implement industry-standard security measures including HTTPS encryption, hashed passwords, CSRF protection, and regular security audits to protect your data.</p>
        `
    },
    {
        num: '8',
        title: 'Children\'s Privacy',
        body: `
            <p>Our services are not directed to children under 13. We do not knowingly collect personal information from children. If you believe a child has provided us with data, please contact us immediately.</p>
        `
    },
    {
        num: '9',
        title: 'Changes to This Policy',
        body: `
            <p>We may update this Privacy Policy from time to time. We will notify registered users of significant changes via email. Continued use of the platform after changes constitutes acceptance of the updated policy.</p>
        `
    },
    {
        num: '10',
        title: 'Contact Us',
        body: `
            <p>If you have questions about this Privacy Policy, please contact us:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Email: <a href="mailto:support@ezytools.com" class="text-primary-600 hover:underline">support@ezytools.com</a></li>
                <li>Website: <a href="/contact" class="text-primary-600 hover:underline">Contact Page</a></li>
            </ul>
        `
    },
];
</script>
