<template>
    <AppLayout title="About Us — EzyTools">
        <Head>
            <meta name="description" content="Learn about EzyTools — a free platform offering 150+ online tools, calculators, converters, and AI-powered utilities built in Bangladesh." />
            <link rel="canonical" :href="route('pages.about')" />
        </Head>

        <!-- Hero -->
        <section class="relative text-center py-16 md:py-24 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-50/80 via-transparent to-purple-50/60 dark:from-primary-950/30 dark:via-transparent dark:to-purple-950/20 pointer-events-none rounded-3xl"></div>
            <div class="relative z-10 max-w-3xl mx-auto px-4">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-xs font-semibold mb-6 tracking-wide">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    About EzyTools
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-surface-900 dark:text-white tracking-tight leading-tight mb-6">
                    Free Online Tools for <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600">Everyone</span>
                </h1>
                <p class="text-lg text-surface-600 dark:text-surface-400 leading-relaxed max-w-2xl mx-auto">
                    EzyTools is a free online platform offering a comprehensive suite of tools and utilities designed to simplify everyday tasks. From developers to students, our mission is to provide fast, reliable, and accessible solutions for everyone.
                </p>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-4">Our Mission</h2>
                    <p class="text-surface-600 dark:text-surface-400 leading-relaxed mb-4">
                        EzyTools was born from a simple idea: essential online utilities should be free, fast, and accessible to everyone — without intrusive ads, confusing UIs, or hidden paywalls.
                    </p>
                    <p class="text-surface-600 dark:text-surface-400 leading-relaxed">
                        Whether you are a student calculating your GPA, a developer generating meta tags, or a content creator writing with AI — we want to be the one platform you reach for every day.
                    </p>
                </div>
                <div class="bg-gradient-to-br from-primary-500 to-purple-600 rounded-2xl p-8 text-white">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <p class="text-4xl font-extrabold">150+</p>
                            <p class="text-sm text-white/80 mt-1">Free Tools</p>
                        </div>
                        <div class="text-center">
                            <p class="text-4xl font-extrabold">20+</p>
                            <p class="text-sm text-white/80 mt-1">AI Powered</p>
                        </div>
                        <div class="text-center">
                            <p class="text-4xl font-extrabold">20K+</p>
                            <p class="text-sm text-white/80 mt-1">Active Users</p>
                        </div>
                        <div class="text-center">
                            <p class="text-4xl font-extrabold">100K+</p>
                            <p class="text-sm text-white/80 mt-1">Files Processed</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values -->
        <section class="py-12">
            <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-8 text-center">What We Stand For</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="val in values" :key="val.title" class="bg-white dark:bg-surface-800 rounded-2xl p-6 border border-surface-200 dark:border-surface-700 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" :class="val.bg">
                        <component :is="val.icon" class="w-6 h-6" :class="val.fg" />
                    </div>
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-2">{{ val.title }}</h3>
                    <p class="text-sm text-surface-600 dark:text-surface-400 leading-relaxed">{{ val.desc }}</p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-12 text-center">
            <div class="bg-surface-100 dark:bg-surface-800 rounded-2xl p-10 md:p-16 border border-surface-200 dark:border-surface-700">
                <h2 class="text-2xl md:text-3xl font-extrabold text-surface-900 dark:text-white mb-4">Ready to Get Started?</h2>
                <p class="text-surface-600 dark:text-surface-400 mb-8 max-w-lg mx-auto">Explore our collection of free, powerful online tools — no sign-up required.</p>
                <Link :href="route('tools.index')" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    Browse All Tools
                </Link>
            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ShieldCheckIcon, BoltIcon, GlobeAltIcon, SparklesIcon, HeartIcon, LockClosedIcon } from '@heroicons/vue/24/outline';

const values = [
    { title: 'Free Forever', desc: 'All core tools will always be free. No hidden costs, no bait-and-switch. Our Pro plan only adds premium extras.', icon: HeartIcon, bg: 'bg-pink-100 dark:bg-pink-900/30', fg: 'text-pink-600 dark:text-pink-400' },
    { title: 'Lightning Fast', desc: 'Every tool is optimized for speed. Client-side processing means your data stays in your browser and results are instant.', icon: BoltIcon, bg: 'bg-amber-100 dark:bg-amber-900/30', fg: 'text-amber-600 dark:text-amber-400' },
    { title: 'Privacy First', desc: 'We do not store your files, text, or personal data. What you process stays on your device — always.', icon: LockClosedIcon, bg: 'bg-green-100 dark:bg-green-900/30', fg: 'text-green-600 dark:text-green-400' },
    { title: 'AI Powered', desc: 'From article generation to code explanation, our AI tools use the latest models to deliver high-quality results.', icon: SparklesIcon, bg: 'bg-purple-100 dark:bg-purple-900/30', fg: 'text-purple-600 dark:text-purple-400' },
    { title: 'Globally Accessible', desc: 'Designed for users worldwide, with Bangla language support, multiple locales, and mobile-responsive layouts.', icon: GlobeAltIcon, bg: 'bg-blue-100 dark:bg-blue-900/30', fg: 'text-blue-600 dark:text-blue-400' },
    { title: 'Secure & Reliable', desc: 'Enterprise-grade infrastructure, HTTPS everywhere, and regular security audits to keep your interactions safe.', icon: ShieldCheckIcon, bg: 'bg-teal-100 dark:bg-teal-900/30', fg: 'text-teal-600 dark:text-teal-400' },
];
</script>
