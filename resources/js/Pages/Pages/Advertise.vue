<template>
    <AppLayout title="Advertise with Us — EzyTools">
        <Head>
            <meta name="description" content="Advertise on EzyTools — reach 150K+ monthly users with premium ad placements on our free online tools platform. Affordable rates starting from $5." />
            <meta name="keywords" content="advertise, ezytools advertising, online tools ads, banner ads, ad placement, digital advertising" />
            <link rel="canonical" :href="route('pages.advertise')" />
        </Head>

        <div class="max-w-6xl mx-auto py-12 md:py-16 px-4">
            <!-- Hero -->
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-xs font-semibold mb-4">
                    <MegaphoneIcon class="w-4 h-4" />
                    Advertise with Us
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-4">
                    Reach <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-500 to-orange-600">150,000+</span> Users Monthly
                </h1>
                <p class="text-lg text-surface-600 dark:text-surface-400 max-w-2xl mx-auto">
                    Place your brand in front of students, developers, creators, and professionals using 150+ free online tools every day.
                </p>
            </div>

            <!-- Benefits -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
                <div v-for="b in benefits" :key="b.title" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 text-center">
                    <div class="w-12 h-12 rounded-xl mx-auto mb-4 flex items-center justify-center" :class="b.bg">
                        <component :is="b.icon" class="w-6 h-6" :class="b.fg" />
                    </div>
                    <h3 class="font-bold text-surface-900 dark:text-white mb-2">{{ b.title }}</h3>
                    <p class="text-xs text-surface-500 dark:text-surface-400 leading-relaxed">{{ b.desc }}</p>
                </div>
            </div>

            <!-- How It Works -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-surface-900 dark:text-white text-center mb-8">How It Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div v-for="(step, i) in steps" :key="i" class="text-center">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-600 to-purple-600 text-white font-bold text-sm flex items-center justify-center mx-auto mb-3">{{ i + 1 }}</div>
                        <h4 class="font-semibold text-surface-900 dark:text-white text-sm mb-1">{{ step.title }}</h4>
                        <p class="text-xs text-surface-500 dark:text-surface-400">{{ step.desc }}</p>
                    </div>
                </div>
            </div>

            <!-- Pricing Table -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-surface-900 dark:text-white text-center mb-2">Ad Space Pricing</h2>
                <p class="text-sm text-surface-500 dark:text-surface-400 text-center mb-8">All prices in USD. Custom packages available for longer durations.</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="border-b-2 border-surface-200 dark:border-surface-700">
                                <th class="py-3 px-4 text-left font-semibold text-surface-900 dark:text-white">Ad Position</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-900 dark:text-white hidden md:table-cell">Dimensions</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-900 dark:text-white hidden lg:table-cell">Est. Impressions</th>
                                <th class="py-3 px-4 text-center font-semibold text-surface-900 dark:text-white">3 Days</th>
                                <th class="py-3 px-4 text-center font-semibold text-surface-900 dark:text-white">7 Days</th>
                                <th class="py-3 px-4 text-center font-semibold text-amber-600 dark:text-amber-400">30 Days</th>
                                <th class="py-3 px-4 text-center font-semibold text-surface-900 dark:text-white">Select</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                            <tr v-for="ad in adSpaces" :key="ad.id" class="hover:bg-surface-50 dark:hover:bg-surface-800/50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-surface-900 dark:text-white">{{ ad.name }}</div>
                                    <div class="text-xs text-surface-400 mt-0.5 hidden sm:block">{{ ad.description }}</div>
                                </td>
                                <td class="py-3 px-4 text-xs text-surface-500 hidden md:table-cell">{{ ad.dimensions }}</td>
                                <td class="py-3 px-4 text-xs text-surface-500 hidden lg:table-cell">{{ ad.est_impressions }}</td>
                                <td class="py-3 px-4 text-center font-medium text-surface-700 dark:text-surface-300">${{ ad.price_3d }}</td>
                                <td class="py-3 px-4 text-center font-medium text-surface-700 dark:text-surface-300">${{ ad.price_7d }}</td>
                                <td class="py-3 px-4 text-center font-bold text-amber-600 dark:text-amber-400">${{ ad.price_30d }}</td>
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox" :value="ad.id" v-model="form.ad_spaces"
                                        class="w-4 h-4 rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Inquiry Form -->
            <div id="inquiry-form" class="max-w-3xl mx-auto">
                <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-10">
                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white text-center mb-2">Get Started</h2>
                    <p class="text-sm text-surface-500 dark:text-surface-400 text-center mb-8">Select ad spaces from the table above, fill in your details, and we'll get back to you within 24 hours.</p>

                    <!-- Success -->
                    <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                        <div v-if="$page.props.flash.success" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl p-4 text-sm mb-6 flex items-center gap-2">
                            <CheckCircleIcon class="w-5 h-5 shrink-0" />
                            {{ $page.props.flash.success }}
                        </div>
                    </transition>

                    <form @submit.prevent="submitForm" class="space-y-5">
                        <!-- Selected spaces indicator -->
                        <div v-if="form.ad_spaces.length > 0" class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-3">
                            <p class="text-xs text-primary-700 dark:text-primary-300 font-medium">
                                {{ form.ad_spaces.length }} ad space{{ form.ad_spaces.length > 1 ? 's' : '' }} selected
                                <span class="text-primary-500 ml-1">({{ selectedNames.join(', ') }})</span>
                            </p>
                        </div>
                        <div v-else class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3">
                            <p class="text-xs text-amber-700 dark:text-amber-300">↑ Please select at least one ad space from the pricing table above.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Your Name *</label>
                                <input v-model="form.name" type="text" required
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3"
                                    placeholder="John Doe" />
                                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Email *</label>
                                <input v-model="form.email" type="email" required
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3"
                                    placeholder="you@company.com" />
                                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Company</label>
                                <input v-model="form.company" type="text"
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3"
                                    placeholder="Acme Inc." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Website</label>
                                <input v-model="form.website" type="url"
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3"
                                    placeholder="https://your-site.com" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Duration *</label>
                                <select v-model="form.duration" required
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3">
                                    <option value="3d">3 Days</option>
                                    <option value="7d">7 Days</option>
                                    <option value="30d">30 Days</option>
                                    <option value="custom">Custom / Long-term</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Budget (USD)</label>
                                <input v-model="form.budget" type="text"
                                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3"
                                    placeholder="e.g. $50-100" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Message *</label>
                            <textarea v-model="form.message" rows="4" required
                                class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3 resize-y"
                                placeholder="Tell us about your advertising goals, target audience, and any specific requirements..."></textarea>
                            <p v-if="form.errors.message" class="text-red-500 text-xs mt-1">{{ form.errors.message }}</p>
                        </div>

                        <button type="submit" :disabled="form.processing || form.ad_spaces.length === 0"
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <PaperAirplaneIcon v-else class="w-4 h-4" />
                            {{ form.processing ? 'Submitting...' : 'Submit Inquiry' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { MegaphoneIcon, EyeIcon, CursorArrowRaysIcon, ChartBarIcon, PaperAirplaneIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    adSpaces: Array,
});

const form = useForm({
    name: '',
    email: '',
    company: '',
    website: '',
    ad_spaces: [],
    duration: '7d',
    budget: '',
    message: '',
});

const selectedNames = computed(() => {
    return form.ad_spaces.map(id => {
        const ad = props.adSpaces.find(a => a.id === id);
        return ad ? ad.name : '';
    }).filter(Boolean);
});

const submitForm = () => {
    form.post(route('advertise.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const benefits = [
    { title: 'High-Traffic Platform', desc: 'EzyTools serves 150K+ monthly active users across 150+ tools — real, engaged visitors.', icon: EyeIcon, bg: 'bg-blue-100 dark:bg-blue-900/30', fg: 'text-blue-600 dark:text-blue-400' },
    { title: 'Targeted Audience', desc: 'Our users are students, developers, content creators, and professionals — prime demographics.', icon: CursorArrowRaysIcon, bg: 'bg-purple-100 dark:bg-purple-900/30', fg: 'text-purple-600 dark:text-purple-400' },
    { title: 'Transparent Reporting', desc: 'Get detailed impression and click analytics for every ad placement. No hidden metrics.', icon: ChartBarIcon, bg: 'bg-green-100 dark:bg-green-900/30', fg: 'text-green-600 dark:text-green-400' },
];

const steps = [
    { title: 'Choose Ad Spaces', desc: 'Browse our available placements and select the ones that fit your goals.' },
    { title: 'Submit Inquiry', desc: 'Fill out the form with your details, budget, and creative requirements.' },
    { title: 'We Review & Reply', desc: 'Our team reviews your inquiry and responds within 24 hours with a proposal.' },
    { title: 'Go Live', desc: 'After approval, your ads go live immediately. Track performance in real-time.' },
];
</script>
