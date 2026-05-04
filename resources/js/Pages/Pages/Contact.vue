<template>
    <AppLayout title="Contact Us — EzyTools">
        <Head>
            <meta name="description" content="Get in touch with the EzyTools team. Submit inquiries, report bugs, or request features." />
            <link rel="canonical" :href="route('pages.contact')" />
        </Head>

        <div class="max-w-5xl mx-auto py-12 md:py-16">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-300 text-xs font-semibold mb-4">
                    <ChatBubbleLeftRightIcon class="w-4 h-4" />
                    Get in Touch
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">Contact Us</h1>
                <p class="text-surface-600 dark:text-surface-400 max-w-lg mx-auto">Have a question, bug report, or feature request? We'd love to hear from you.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Contact Form -->
                <div class="lg:col-span-3 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                    <h2 class="text-lg font-bold text-surface-900 dark:text-white mb-6">Send a Message</h2>
                    <form @submit.prevent="submitForm" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Your Name</label>
                                <input v-model="form.name" type="text" required
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3"
                                    placeholder="Enter your full name" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Email Address</label>
                                <input v-model="form.email" type="email" required
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3"
                                    placeholder="you@example.com" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Subject</label>
                            <select v-model="form.subject"
                                class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3">
                                <option value="general">General Inquiry</option>
                                <option value="bug">Bug Report</option>
                                <option value="feature">Feature Request</option>
                                <option value="subscription">Subscription / Billing</option>
                                <option value="partnership">Partnership</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Message</label>
                            <textarea v-model="form.message" rows="5" required
                                class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3 resize-y"
                                placeholder="Tell us what's on your mind..."></textarea>
                        </div>
                        <button type="submit" :disabled="sending"
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <svg v-if="sending" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <PaperAirplaneIcon v-else class="w-4 h-4" />
                            {{ sending ? 'Sending...' : 'Send Message' }}
                        </button>

                        <!-- Success message -->
                        <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                            <div v-if="sent" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl p-4 text-sm flex items-center gap-2">
                                <CheckCircleIcon class="w-5 h-5 shrink-0" />
                                Thank you! Your message has been sent. We will get back to you soon.
                            </div>
                        </transition>
                    </form>
                </div>

                <!-- Contact Info Sidebar -->
                <div class="lg:col-span-2 space-y-6">
                    <div v-for="c in contactCards" :key="c.title" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="c.bg">
                                <component :is="c.icon" class="w-5 h-5" :class="c.fg" />
                            </div>
                            <div>
                                <h3 class="font-bold text-surface-900 dark:text-white text-sm mb-1">{{ c.title }}</h3>
                                <p class="text-xs text-surface-500 dark:text-surface-400 leading-relaxed" v-html="c.detail"></p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="bg-gradient-to-br from-primary-50 to-purple-50 dark:from-primary-950/30 dark:to-purple-950/20 rounded-2xl p-6 border border-primary-100 dark:border-primary-900/30">
                        <h3 class="font-bold text-surface-900 dark:text-white text-sm mb-3">Frequently Asked</h3>
                        <div class="space-y-3">
                            <div v-for="faq in faqs" :key="faq.q">
                                <p class="text-xs font-semibold text-surface-800 dark:text-surface-200 mb-0.5">{{ faq.q }}</p>
                                <p class="text-xs text-surface-500 dark:text-surface-400 leading-relaxed">{{ faq.a }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ChatBubbleLeftRightIcon, PaperAirplaneIcon, EnvelopeIcon, ClockIcon, MapPinIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const page = usePage();

const form = ref({
    name: page.props.auth.user?.name || '',
    email: page.props.auth.user?.email || '',
    subject: 'general',
    message: '',
});
const sending = ref(false);
const sent = ref(false);

const submitForm = () => {
    sending.value = true;
    // Simulate sending — in production, wire up to a real endpoint
    setTimeout(() => {
        sending.value = false;
        sent.value = true;
        form.value.message = '';
        setTimeout(() => { sent.value = false; }, 5000);
    }, 1500);
};

const contactCards = [
    { title: 'Email Support', detail: '<a href="mailto:support@ezytools.app" class="text-primary-600 hover:underline">support@ezytools.app</a><br/>We respond within 24 hours.', icon: EnvelopeIcon, bg: 'bg-blue-100 dark:bg-blue-900/30', fg: 'text-blue-600 dark:text-blue-400' },
    { title: 'Business Hours', detail: 'Sunday – Thursday<br/>10:00 AM – 6:00 PM (BST, GMT+6)', icon: ClockIcon, bg: 'bg-amber-100 dark:bg-amber-900/30', fg: 'text-amber-600 dark:text-amber-400' },
    { title: 'Location', detail: 'Dhaka, Bangladesh 🇧🇩', icon: MapPinIcon, bg: 'bg-green-100 dark:bg-green-900/30', fg: 'text-green-600 dark:text-green-400' },
];

const faqs = [
    { q: 'Is EzyTools really free?', a: 'Yes! All core tools are free. The Pro plan adds unlimited usage and removes ads.' },
    { q: 'How do I cancel Pro?', a: 'Go to your Dashboard → Subscription and click Cancel. No questions asked.' },
    { q: 'Do you store my data?', a: 'No. Files and text are processed in your browser or temporarily. We never store your content.' },
];
</script>
