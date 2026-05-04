<template>
    <AppLayout title="FAQs — EzyTools">
        <Head>
            <meta name="description" content="Find answers to frequently asked questions about EzyTools — pricing, AI tools, privacy, subscriptions, and more." />
            <link rel="canonical" :href="route('pages.faq')" />
        </Head>

        <div class="max-w-4xl mx-auto py-12 md:py-16">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-4 tracking-wide">
                    <QuestionMarkCircleIcon class="w-4 h-4" />
                    Help Center
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">Frequently Asked Questions</h1>
                <p class="text-surface-600 dark:text-surface-400 max-w-lg mx-auto">Everything you need to know about EzyTools. Can't find what you're looking for? <Link :href="route('pages.contact')" class="text-primary-600 hover:underline font-medium">Contact us</Link>.</p>
            </div>

            <!-- Search -->
            <div class="relative mb-10">
                <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" />
                <input v-model="search" type="text" placeholder="Search questions..."
                    class="block w-full pl-12 pr-4 py-3.5 rounded-2xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm shadow-sm transition-shadow" />
                <button v-if="search" @click="search = ''" class="absolute right-4 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600 dark:hover:text-surface-200 transition-colors">
                    <XMarkIcon class="w-4 h-4" />
                </button>
            </div>

            <!-- Category Tabs -->
            <div class="flex flex-wrap gap-2 mb-8">
                <button v-for="cat in allCategories" :key="cat"
                    @click="activeCategory = cat"
                    :class="[
                        'px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200',
                        activeCategory === cat
                            ? 'bg-primary-600 text-white shadow-sm'
                            : 'bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400 hover:bg-surface-200 dark:hover:bg-surface-700'
                    ]">
                    {{ cat }}
                </button>
            </div>

            <!-- No results -->
            <div v-if="filteredFaqs.length === 0" class="text-center py-16">
                <div class="w-16 h-16 bg-surface-100 dark:bg-surface-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <MagnifyingGlassIcon class="w-7 h-7 text-surface-400" />
                </div>
                <p class="text-surface-500 dark:text-surface-400 font-medium mb-1">No matching questions found</p>
                <p class="text-xs text-surface-400">Try a different search term or <button @click="search = ''; activeCategory = 'All'" class="text-primary-600 hover:underline">reset filters</button>.</p>
            </div>

            <!-- FAQ Accordion -->
            <div v-else class="space-y-3">
                <div v-for="(faq, idx) in filteredFaqs" :key="idx"
                    class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden transition-shadow"
                    :class="{ 'shadow-md ring-1 ring-primary-500/20': openItems.includes(idx) }">
                    <button @click="toggle(idx)"
                        class="w-full px-6 py-5 flex items-start gap-4 text-left group">
                        <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center mt-0.5 transition-colors"
                            :class="openItems.includes(idx) ? 'bg-primary-100 dark:bg-primary-900/30' : 'bg-surface-100 dark:bg-surface-700'">
                            <ChevronDownIcon class="w-4 h-4 transition-transform duration-200"
                                :class="[
                                    openItems.includes(idx) ? 'rotate-180 text-primary-600 dark:text-primary-400' : 'text-surface-500',
                                ]" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-surface-900 dark:text-white text-sm leading-relaxed group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ faq.q }}</h3>
                            <span class="inline-block mt-1.5 text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                :class="categoryBadgeClass(faq.category)">{{ faq.category }}</span>
                        </div>
                    </button>

                    <transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-96"
                                leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
                        <div v-if="openItems.includes(idx)" class="overflow-hidden">
                            <div class="px-6 pb-6 pl-[4.5rem]">
                                <div class="text-sm text-surface-600 dark:text-surface-400 leading-relaxed" v-html="faq.a"></div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>

            <!-- CTA -->
            <div class="mt-14 text-center bg-gradient-to-br from-primary-50 to-purple-50 dark:from-primary-950/30 dark:to-purple-950/20 rounded-2xl p-10 border border-primary-100 dark:border-primary-900/30">
                <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-2">Still have questions?</h2>
                <p class="text-sm text-surface-600 dark:text-surface-400 mb-6">Our team is here to help. Reach out and we'll get back to you within 24 hours.</p>
                <Link :href="route('pages.contact')" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
                    <ChatBubbleLeftRightIcon class="w-4 h-4" />
                    Contact Support
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { QuestionMarkCircleIcon, MagnifyingGlassIcon, XMarkIcon, ChevronDownIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline';

const search = ref('');
const activeCategory = ref('All');
const openItems = ref([]);

const toggle = (idx) => {
    const i = openItems.value.indexOf(idx);
    if (i === -1) openItems.value.push(idx);
    else openItems.value.splice(i, 1);
};

const props = defineProps({
    guestLimit: String,
    registeredLimit: String,
    proPrice: [String, Number],
    proCurrencySymbol: String,
    proAiCreditLimit: String,
});

const faqs = computed(() => [
    // General
    { category: 'General', q: 'What is EzyTools?', a: 'EzyTools is a free online platform offering 150+ tools including calculators, converters, text tools, image tools, developer utilities, and AI-powered generators — all in one place.' },
    { category: 'General', q: 'Is EzyTools free to use?', a: 'Yes! All core tools are completely free with no sign-up required. We also offer a <strong>Pro plan</strong> for users who want unlimited AI usage, higher limits, and an ad-free experience.' },
    { category: 'General', q: 'Do I need to create an account?', a: 'No account is needed to use most tools. However, creating a free account unlocks higher daily limits for AI tools and lets you save favorite tools to your dashboard.' },
    { category: 'General', q: 'What languages does EzyTools support?', a: 'The platform interface is in English. Our AI tools support multiple output languages including <strong>Bangla, English (US/British), Hindi, Urdu, and Arabic</strong>.' },

    // AI Tools
    { category: 'AI Tools', q: 'How do the AI tools work?', a: 'Our AI tools use state-of-the-art models (GPT-4o, Gemini 2.5 Pro, Grok, etc.) to generate content. You provide input text and options, and the AI processes your request in real-time.' },
    { category: 'AI Tools', q: 'Is there a daily limit on AI tools?', a: `Yes. <strong>Guest users</strong> get ${props.guestLimit} requests/day, <strong>registered users</strong> get ${props.registeredLimit} requests/day, and <strong>Pro users</strong> get unlimited requests with no daily cap.` },
    { category: 'AI Tools', q: 'Why are AI responses sometimes cut off?', a: 'Each AI tool has a maximum output token limit. Free users have a lower limit than Pro users. If you need longer, more detailed responses, consider upgrading to Pro for higher token limits.' },
    { category: 'AI Tools', q: 'Which AI models does EzyTools use?', a: 'We support multiple providers: <strong>OpenAI</strong> (GPT-4o, GPT-4.5, GPT-5, o3-mini), <strong>Google Gemini</strong> (2.5 Flash, 2.5 Pro), and <strong>xAI Grok</strong> (Grok 2, Grok 3). Admins can configure which model each tool uses.' },
    { category: 'AI Tools', q: 'Does EzyTools store my AI inputs or outputs?', a: 'No. Your text is sent to the AI provider for processing and the result is returned directly to your browser. We do not store any of your AI content permanently.' },

    // Subscription
    { category: 'Subscription', q: 'What does the Pro plan include?', a: `<ul class="list-disc list-inside space-y-1"><li>${props.proAiCreditLimit == '-1' ? 'Unlimited' : props.proAiCreditLimit} AI tool usage credits</li><li>Higher output token limits for longer, richer responses</li><li>Higher input character limits</li><li>Ad-free experience across the entire platform</li><li>Priority access to new tools and features</li></ul>` },
    { category: 'Subscription', q: 'How much does Pro cost?', a: `Pro plans start at just <strong>${props.proCurrencySymbol}${props.proPrice}/month</strong>. Visit our <a href="/pricing" class="text-primary-600 hover:underline">Pricing page</a> for the latest plans and special offers.` },
    { category: 'Subscription', q: 'How do I cancel my subscription?', a: 'Go to your <strong>Dashboard → Subscription</strong> and click "Cancel Subscription". Your Pro benefits will remain active until the end of your current billing period. No questions asked.' },
    { category: 'Subscription', q: 'Can I get a refund?', a: 'Refunds are handled on a case-by-case basis within 7 days of purchase. Please <a href="/contact" class="text-primary-600 hover:underline">contact us</a> with your request and we\'ll assist you promptly.' },

    // Privacy & Security
    { category: 'Privacy & Security', q: 'Does EzyTools collect my personal data?', a: 'We collect minimal data: your email and username (if registered), and anonymous usage analytics. We never sell or share your data. See our <a href="/privacy-policy" class="text-primary-600 hover:underline">Privacy Policy</a> for details.' },
    { category: 'Privacy & Security', q: 'Are the tools processed on my device?', a: 'Most non-AI tools (calculators, converters, text tools) run entirely in your browser — your data never leaves your device. AI tools require server communication with AI providers.' },
    { category: 'Privacy & Security', q: 'Is my data encrypted?', a: 'Yes. All communication with EzyTools is encrypted via <strong>HTTPS/TLS</strong>. Passwords are hashed using industry-standard algorithms and never stored in plain text.' },

    // Technical
    { category: 'Technical', q: 'Which browsers are supported?', a: 'EzyTools works on all modern browsers: <strong>Chrome, Firefox, Safari, Edge, and Brave</strong>. We recommend keeping your browser updated for the best experience.' },
    { category: 'Technical', q: 'Does EzyTools work on mobile?', a: 'Yes! The entire platform is fully responsive and works beautifully on phones and tablets.' },
    { category: 'Technical', q: 'I found a bug. How do I report it?', a: 'Please use our <a href="/contact" class="text-primary-600 hover:underline">Contact page</a> and select "Bug Report" as the subject. Include steps to reproduce the issue and we\'ll fix it as soon as possible.' },
    { category: 'Technical', q: 'Will there be an API for developers?', a: 'We are planning a public API for select tools in the future. Stay tuned by signing up for an account — we\'ll notify registered users when it launches.' },
]);

const allCategories = computed(() => {
    const cats = [...new Set(faqs.value.map(f => f.category))];
    return ['All', ...cats];
});

const filteredFaqs = computed(() => {
    let result = faqs.value;
    if (activeCategory.value !== 'All') {
        result = result.filter(f => f.category === activeCategory.value);
    }
    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        result = result.filter(f => f.q.toLowerCase().includes(q) || f.a.toLowerCase().includes(q));
    }
    return result;
});

const categoryBadgeClass = (cat) => {
    const map = {
        'General': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'AI Tools': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
        'Subscription': 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'Privacy & Security': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'Technical': 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
    };
    return map[cat] || 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-400';
};
</script>
