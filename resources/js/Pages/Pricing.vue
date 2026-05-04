<template>
    <AppLayout>
        <Head>
            <title>Pro Pricing — Upgrade to EzyTools Pro</title>
            <meta name="description" content="Unlock EzyTools Pro — remove ads, get unlimited AI credits, access premium tools, and enjoy priority processing. Plans start from ৳199/month." />
            <meta name="keywords" content="ezytools pro, premium tools, pricing plans, upgrade, ai credits, ad-free tools" />
            <link rel="canonical" :href="route('pricing')" />
            <meta property="og:type" content="website" />
            <meta property="og:title" content="Pro Pricing — Upgrade to EzyTools Pro" />
            <meta property="og:description" content="Unlock EzyTools Pro — remove ads, get unlimited AI credits, access premium tools, and enjoy priority processing." />
            <meta property="og:url" :content="route('pricing')" />
            <component v-for="(s, i) in schemas" :key="i" is="script" type="application/ld+json" v-text="JSON.stringify(s)" />
        </Head>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h1 class="text-4xl font-extrabold text-surface-900 dark:text-white tracking-tight mb-4">
                    Unlock the Full Potential of EzyTools
                </h1>
                <p class="text-xl text-surface-600 dark:text-surface-400">
                    Remove ads, unlock premium tools, and get unlimited conversions.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">

                <!-- Free Plan -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8 flex flex-col">
                    <h3 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Basic</h3>
                    <p class="text-surface-500 dark:text-surface-400 mb-6">For casual users.</p>
                    <div class="text-4xl font-extrabold text-surface-900 dark:text-white mb-6">Free</div>

                    <ul class="space-y-4 mb-8 flex-1">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-surface-600 dark:text-surface-300">Access to all basic tools</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-surface-600 dark:text-surface-300">Standard conversion speeds</span>
                        </li>
                        <li v-if="credits.enabled && credits.free > 0" class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-surface-600 dark:text-surface-300">{{ credits.free === -1 ? 'Unlimited' : credits.free }} AI credits</span>
                        </li>
                        <li class="flex items-start opacity-50">
                            <svg class="h-6 w-6 text-red-400 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-surface-600 dark:text-surface-300 line-through">Ad-free experience</span>
                        </li>
                        <li class="flex items-start opacity-50">
                            <svg class="h-6 w-6 text-red-400 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-surface-600 dark:text-surface-300 line-through">Pro tools access</span>
                        </li>
                    </ul>

                    <div v-if="$page.props.auth.user && !$page.props.auth.user.is_pro" class="mt-auto">
                        <button disabled class="w-full py-3 px-4 bg-surface-100 text-surface-500 rounded-xl font-bold text-center cursor-not-allowed">Current Plan</button>
                    </div>
                    <div v-else-if="!$page.props.auth.user" class="mt-auto">
                        <button @click="openAuth('register')" class="block w-full py-3 px-4 bg-primary-50 text-primary-700 hover:bg-primary-100 dark:bg-primary-900/20 dark:text-primary-400 dark:hover:bg-primary-900/40 rounded-xl font-bold text-center transition-colors border border-primary-200 dark:border-primary-800">Sign Up Free</button>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="bg-gradient-to-br from-primary-600 to-purple-700 rounded-2xl shadow-xl border border-primary-500 p-8 flex flex-col relative">
                    <div class="absolute top-0 right-6 transform -translate-y-1/2">
                        <span class="bg-gradient-to-r from-orange-400 to-orange-500 text-white text-xs font-bold uppercase tracking-wider py-1 px-3 rounded-full shadow-md">Most Popular</span>
                    </div>

                    <h3 class="text-2xl font-bold text-white mb-2">Pro</h3>
                    <p class="text-primary-100 mb-6">For power users & professionals.</p>

                    <div class="flex items-baseline mb-6 gap-2">
                        <div class="text-5xl font-extrabold text-white">{{ currencySymbol }} {{ isYearly ? yearlyPrice : monthlyPrice }}</div>
                        <div class="text-xl text-primary-200 font-medium">/ {{ isYearly ? 'year' : 'month' }}</div>
                    </div>

                    <div class="bg-primary-900/30 rounded-lg p-1 flex mb-6">
                        <button @click="isYearly = false" :class="!isYearly ? 'bg-primary-500 text-white shadow' : 'text-primary-200 hover:text-white'" class="flex-1 py-1.5 text-sm font-medium rounded-md transition-colors">Monthly</button>
                        <button @click="isYearly = true" :class="isYearly ? 'bg-primary-500 text-white shadow' : 'text-primary-200 hover:text-white'" class="flex-1 py-1.5 text-sm font-medium rounded-md transition-colors">
                            Yearly <span class="text-xs bg-orange-500 text-white px-1.5 py-0.5 rounded ml-1">-16%</span>
                        </button>
                    </div>

                    <ul class="space-y-4 mb-8 flex-1">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-300 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-primary-50">100% Ad-free experience</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-300 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-primary-50">Access to all Pro tools</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-300 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-primary-50">Priority processing speed</span>
                        </li>
                        <li v-if="credits.enabled && credits.pro !== 0" class="flex items-start">
                            <svg class="h-6 w-6 text-green-300 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-primary-50">{{ credits.pro === -1 ? 'Unlimited' : credits.pro }} AI credits</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-300 mr-2 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-primary-50">Early access to new tools</span>
                        </li>
                    </ul>

                    <div class="mt-auto">
                        <div v-if="!$page.props.auth.user">
                            <button @click="openAuth('register')" class="block w-full py-3 px-4 bg-white text-primary-700 hover:bg-surface-50 rounded-xl font-bold text-center transition-colors shadow-sm">Sign up to Subscribe</button>
                        </div>
                        <div v-else-if="$page.props.auth.user.is_pro">
                            <button disabled class="w-full py-3 px-4 bg-primary-500 text-white rounded-xl font-bold text-center cursor-not-allowed border border-primary-400">You are on Pro</button>
                        </div>
                        <div v-else>
                            <form @submit.prevent="checkout">
                                <button type="submit" :disabled="processing" class="w-full py-3 px-4 bg-white text-primary-700 hover:bg-surface-50 rounded-xl font-bold text-center transition-colors shadow-sm flex justify-center items-center gap-2">
                                    <svg v-if="processing" class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ processing ? 'Redirecting...' : 'Upgrade Now' }}
                                </button>
                            </form>
                            <p v-if="checkoutError" class="text-red-200 text-sm text-center mt-2">{{ checkoutError }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-16 text-center text-surface-500 dark:text-surface-400 text-sm">
                <p v-if="currency === 'BDT'">Secure payments processed via UddoktaPay (bKash, Nagad, Rocket, Cards).</p>
                <p v-else>Prices shown in {{ currency }}. Payment will be processed in BDT equivalent.</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    monthlyPrice: Number,
    yearlyPrice: Number,
    currency: String,
    currencySymbol: String,
    country: String,
    credits: Object,
    schemas: { type: Array, default: () => [] },
});

const isYearly = ref(true);
const processing = ref(false);
const checkoutError = ref('');

const openAuth = (view) => {
    window.dispatchEvent(new CustomEvent('open-auth', { detail: view }));
};

const checkoutForm = useForm({
    plan: 'yearly'
});

const checkout = () => {
    processing.value = true;
    checkoutError.value = '';

    checkoutForm.plan = isYearly.value ? 'yearly' : 'monthly';

    checkoutForm.post(route('subscription.checkout'), {
        preserveScroll: true,
        onError: (errors) => {
            processing.value = false;
            checkoutError.value = 'Failed to initiate checkout. Please try again.';
        },
        onFinish: () => {
            // Processing stays true as we expect a redirect
            // If it doesn't redirect, we should reset it eventually
            setTimeout(() => {
                processing.value = false;
            }, 5000);
        }
    });
};
</script>
