<template>
    <AppLayout title="Terms of Service — EzyTools">
        <Head>
            <meta name="description" content="Review the Terms of Service for EzyTools. Understand the rules and guidelines for using our free online tools platform." />
            <link rel="canonical" :href="route('pages.terms')" />
        </Head>

        <div class="max-w-4xl mx-auto py-12 md:py-16">
            <!-- Header -->
            <div class="mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 text-xs font-semibold mb-4">
                    <DocumentTextIcon class="w-4 h-4" />
                    Legal
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">Terms of Service</h1>
                <p class="text-sm text-surface-500 dark:text-surface-400">Last updated: April 30, 2026</p>
            </div>

            <!-- Content -->
            <div class="space-y-6">
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-5 text-sm text-amber-800 dark:text-amber-200 leading-relaxed">
                    By accessing or using EzyTools, you agree to be bound by these Terms. If you do not agree, please do not use our services.
                </div>

                <section v-for="s in sections" :key="s.title" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 text-sm font-bold">{{ s.num }}</span>
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
import { DocumentTextIcon } from '@heroicons/vue/24/outline';

const sections = [
    {
        num: '1',
        title: 'Acceptance of Terms',
        body: `
            <p>By accessing and using EzyTools ("the Service"), you accept and agree to be bound by these Terms of Service and our Privacy Policy. These terms apply to all visitors, users, and others who access or use the Service.</p>
        `
    },
    {
        num: '2',
        title: 'Use of Our Services',
        body: `
            <p>You may use EzyTools for lawful purposes only. You agree not to:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Use the platform to generate illegal, harmful, or abusive content via AI tools.</li>
                <li>Attempt to reverse-engineer, decompile, or exploit any part of the platform.</li>
                <li>Use automated bots, scrapers, or scripts to overload or abuse the service.</li>
                <li>Impersonate another person or misrepresent your affiliation with any entity.</li>
                <li>Circumvent rate limits, subscription restrictions, or security measures.</li>
            </ul>
        `
    },
    {
        num: '3',
        title: 'Accounts',
        body: `
            <p>When you create an account, you are responsible for maintaining the security of your credentials. You agree to:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Provide accurate and complete registration information.</li>
                <li>Keep your password secure and not share it with others.</li>
                <li>Notify us immediately if you suspect unauthorized access to your account.</li>
            </ul>
            <p>We reserve the right to suspend or terminate accounts that violate these Terms.</p>
        `
    },
    {
        num: '4',
        title: 'Subscriptions & Payments',
        body: `
            <p>EzyTools offers both free and premium (Pro) tiers:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li><strong>Free Tier:</strong> Access to all core tools with daily usage limits and ads.</li>
                <li><strong>Pro Tier:</strong> Unlimited usage, ad-free experience, higher AI limits, and premium features.</li>
            </ul>
            <p>All payments are processed through UddoktaPay. Subscriptions renew automatically unless cancelled before the renewal date. Refunds are handled on a case-by-case basis within 7 days of purchase. <a href="/contact" class="text-primary-600 hover:underline">Contact us</a> for refund requests.</p>
        `
    },
    {
        num: '5',
        title: 'AI-Generated Content',
        body: `
            <p>Content generated by our AI tools is provided "as-is" without guarantees of accuracy, completeness, or fitness for any specific purpose. You are responsible for reviewing and verifying AI-generated content before use. EzyTools is not liable for any damages arising from reliance on AI-generated content.</p>
        `
    },
    {
        num: '6',
        title: 'Intellectual Property',
        body: `
            <p>The EzyTools platform, including its design, code, branding, and original content, is the property of EzyTools and protected by intellectual property laws. You retain ownership of any content you create or process using our tools.</p>
        `
    },
    {
        num: '7',
        title: 'Limitation of Liability',
        body: `
            <p>To the maximum extent permitted by law, EzyTools shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, or goodwill, resulting from:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Your use or inability to use the Service.</li>
                <li>Errors, inaccuracies, or omissions in AI-generated outputs.</li>
                <li>Unauthorized access to your data.</li>
                <li>Any third-party conduct on the Service.</li>
            </ul>
        `
    },
    {
        num: '8',
        title: 'Service Availability',
        body: `
            <p>We strive to keep EzyTools available 24/7 but do not guarantee uninterrupted access. We may temporarily suspend the service for maintenance, updates, or due to circumstances beyond our control. We will provide advance notice where possible.</p>
        `
    },
    {
        num: '9',
        title: 'Modifications to Terms',
        body: `
            <p>We reserve the right to modify these Terms at any time. We will notify registered users of significant changes via email. Your continued use of the platform after changes constitutes acceptance of the revised Terms.</p>
        `
    },
    {
        num: '10',
        title: 'Governing Law',
        body: `
            <p>These Terms shall be governed by and construed in accordance with the laws of Bangladesh, without regard to conflict of law provisions. Any disputes shall be resolved in the courts of Dhaka, Bangladesh.</p>
        `
    },
    {
        num: '11',
        title: 'Contact',
        body: `
            <p>For questions about these Terms, please contact us:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Email: <a href="mailto:support@ezytools.com" class="text-primary-600 hover:underline">support@ezytools.com</a></li>
                <li>Website: <a href="/contact" class="text-primary-600 hover:underline">Contact Page</a></li>
            </ul>
        `
    },
];
</script>
