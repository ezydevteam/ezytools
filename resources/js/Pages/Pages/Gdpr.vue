<template>
    <AppLayout title="GDPR Compliance — EzyTools">
        <Head>
            <meta name="description" content="EzyTools GDPR Compliance — learn how we protect your data rights under the General Data Protection Regulation, including data access, portability, and erasure." />
            <link rel="canonical" :href="route('pages.gdpr')" />
        </Head>

        <div class="max-w-4xl mx-auto py-12 md:py-16">
            <!-- Header -->
            <div class="mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-semibold mb-4">
                    <ShieldCheckIcon class="w-4 h-4" />
                    Data Protection
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">GDPR Compliance</h1>
                <p class="text-sm text-surface-500 dark:text-surface-400">Last updated: May 3, 2026</p>
            </div>

            <!-- Content -->
            <div class="prose prose-surface dark:prose-invert max-w-none space-y-8">
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-5 text-sm text-indigo-800 dark:text-indigo-200 leading-relaxed">
                    <strong>Summary:</strong> EzyTools is committed to protecting your privacy under the EU General Data Protection Regulation (GDPR). This page explains your rights and how we comply with GDPR requirements — even though we are based in Bangladesh, we respect GDPR for all users globally.
                </div>

                <section v-for="s in sections" :key="s.title" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-sm font-bold">{{ s.num }}</span>
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
        title: 'Data Controller',
        body: `
            <p>EzyTools operates as the data controller for personal information collected through our platform. For GDPR-related inquiries, contact us at:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Email: <a href="mailto:support@ezytools.app" class="text-primary-600 hover:underline">support@ezytools.app</a></li>
                <li>Location: Dhaka, Bangladesh</li>
            </ul>
        `
    },
    {
        num: '2',
        title: 'Legal Basis for Processing',
        body: `
            <p>We process personal data based on the following legal grounds under GDPR Article 6:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li><strong>Consent:</strong> When you create an account or subscribe to our services, you provide explicit consent for data processing.</li>
                <li><strong>Contractual Necessity:</strong> We process data necessary to provide our tools and services to you.</li>
                <li><strong>Legitimate Interests:</strong> We may process data for security monitoring, fraud prevention, and platform improvement, provided it doesn't override your rights.</li>
                <li><strong>Legal Obligations:</strong> We process data when required by applicable laws or regulations.</li>
            </ul>
        `
    },
    {
        num: '3',
        title: 'Your Rights Under GDPR',
        body: `
            <p>As a data subject, you have the following rights under GDPR:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li><strong>Right of Access (Art. 15):</strong> Request a copy of all personal data we hold about you.</li>
                <li><strong>Right to Rectification (Art. 16):</strong> Request correction of inaccurate or incomplete personal data.</li>
                <li><strong>Right to Erasure (Art. 17):</strong> Request deletion of your personal data ("right to be forgotten").</li>
                <li><strong>Right to Restrict Processing (Art. 18):</strong> Request limitation of how we use your data.</li>
                <li><strong>Right to Data Portability (Art. 20):</strong> Receive your personal data in a structured, machine-readable format.</li>
                <li><strong>Right to Object (Art. 21):</strong> Object to processing of your data for legitimate interests or direct marketing.</li>
                <li><strong>Right to Withdraw Consent (Art. 7):</strong> Withdraw previously given consent at any time without affecting prior processing.</li>
            </ul>
            <p>To exercise any of these rights, contact us at <a href="mailto:support@ezytools.app" class="text-primary-600 hover:underline">support@ezytools.app</a>. We will respond within 30 days.</p>
        `
    },
    {
        num: '4',
        title: 'Data We Collect & Purposes',
        body: `
            <table class="w-full text-left text-xs border-collapse mt-2">
                <thead>
                    <tr class="border-b border-surface-200 dark:border-surface-700">
                        <th class="py-2 pr-4 font-semibold text-surface-900 dark:text-white">Data Type</th>
                        <th class="py-2 pr-4 font-semibold text-surface-900 dark:text-white">Purpose</th>
                        <th class="py-2 font-semibold text-surface-900 dark:text-white">Retention</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100 dark:divide-surface-700">
                    <tr><td class="py-2 pr-4">Email & Username</td><td class="py-2 pr-4">Account creation & communication</td><td class="py-2">Until account deletion</td></tr>
                    <tr><td class="py-2 pr-4">Hashed Password</td><td class="py-2 pr-4">Authentication</td><td class="py-2">Until account deletion</td></tr>
                    <tr><td class="py-2 pr-4">IP Address</td><td class="py-2 pr-4">Security & geo-pricing</td><td class="py-2">Session only, not stored</td></tr>
                    <tr><td class="py-2 pr-4">Tool Usage</td><td class="py-2 pr-4">Analytics & credit tracking</td><td class="py-2">Aggregated, anonymized</td></tr>
                    <tr><td class="py-2 pr-4">AI Inputs/Outputs</td><td class="py-2 pr-4">AI tool processing</td><td class="py-2">Request duration only</td></tr>
                    <tr><td class="py-2 pr-4">Payment Info</td><td class="py-2 pr-4">Subscription management</td><td class="py-2">Handled by UddoktaPay</td></tr>
                </tbody>
            </table>
        `
    },
    {
        num: '5',
        title: 'International Data Transfers',
        body: `
            <p>EzyTools is based in Bangladesh. When you use our platform, your data may be transferred to and processed in countries outside the EU/EEA, including:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li><strong>Server Infrastructure:</strong> Our servers may be located outside the EU. We use HTTPS encryption for all data transfers.</li>
                <li><strong>AI Service Providers:</strong> AI tool requests may be processed by Google (USA) or other providers with their own GDPR-compliant Data Processing Agreements.</li>
                <li><strong>Payment Processors:</strong> UddoktaPay processes payments in Bangladesh with industry-standard security measures.</li>
            </ul>
            <p>We ensure adequate safeguards are in place, including encryption in transit and at rest, to protect your data during international transfers.</p>
        `
    },
    {
        num: '6',
        title: 'Cookies & Tracking',
        body: `
            <p>We use minimal, essential cookies only:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li><strong>Session Cookies:</strong> Required for authentication and CSRF protection. These are strictly necessary and exempt from consent requirements.</li>
                <li><strong>Preference Cookies:</strong> Dark mode and language settings stored locally in your browser.</li>
            </ul>
            <p>We do <strong>not</strong> use third-party tracking cookies, advertising pixels, or behavioral analytics tools. Pro users enjoy a completely ad-free, tracker-free experience.</p>
        `
    },
    {
        num: '7',
        title: 'Data Breach Notification',
        body: `
            <p>In the event of a personal data breach that poses a risk to your rights and freedoms, we will:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Notify the relevant supervisory authority within <strong>72 hours</strong> of becoming aware of the breach (as required by GDPR Article 33).</li>
                <li>Notify affected users <strong>without undue delay</strong> if the breach is likely to result in a high risk to their rights (GDPR Article 34).</li>
                <li>Document the breach, its effects, and remedial actions taken.</li>
            </ul>
        `
    },
    {
        num: '8',
        title: 'Data Protection for Minors',
        body: `
            <p>EzyTools does not knowingly collect or process data from children under 16 years of age (the GDPR age of consent). If we discover that we have collected data from a minor without verifiable parental consent, we will delete it immediately.</p>
        `
    },
    {
        num: '9',
        title: 'Automated Decision-Making',
        body: `
            <p>EzyTools does not engage in automated decision-making or profiling that produces legal effects or similarly significantly affects you. AI tools process your input on-demand and return results without profiling your identity.</p>
        `
    },
    {
        num: '10',
        title: 'Contact & Supervisory Authority',
        body: `
            <p>If you have concerns about how we handle your data, you may:</p>
            <ul class="list-disc list-inside space-y-1 pl-2">
                <li>Contact our Data Protection team: <a href="mailto:support@ezytools.app" class="text-primary-600 hover:underline">support@ezytools.app</a></li>
                <li>File a complaint with your local Data Protection Authority (DPA) — for example, the <a href="https://www.cnil.fr/en" target="_blank" rel="noopener" class="text-primary-600 hover:underline">CNIL (France)</a> or the <a href="https://ico.org.uk" target="_blank" rel="noopener" class="text-primary-600 hover:underline">ICO (UK)</a>.</li>
            </ul>
            <p>We are committed to resolving any data protection concerns promptly and transparently.</p>
        `
    },
];
</script>
