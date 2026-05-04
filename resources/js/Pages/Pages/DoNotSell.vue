<template>
    <AppLayout title="Do Not Sell My Info — EzyTools">
        <Head>
            <meta name="description" content="Exercise your right to opt out of the sale or sharing of your personal information on EzyTools, in compliance with CCPA and similar privacy laws." />
            <link rel="canonical" :href="route('pages.do-not-sell')" />
        </Head>

        <div class="max-w-4xl mx-auto py-12 md:py-16">
            <!-- Header -->
            <div class="mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-300 text-xs font-semibold mb-4">
                    <ShieldExclamationIcon class="w-4 h-4" />
                    Your Privacy Rights
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">Do Not Sell or Share My Personal Information</h1>
                <p class="text-sm text-surface-500 dark:text-surface-400">In compliance with the California Consumer Privacy Act (CCPA) and similar privacy regulations.</p>
            </div>

            <!-- Content -->
            <div class="space-y-8">
                <!-- Info card -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 text-sm text-blue-800 dark:text-blue-200 leading-relaxed">
                    <strong>Important:</strong> EzyTools does <strong>not</strong> sell your personal information to third parties. We also do not share your data for cross-context behavioral advertising. However, we provide this opt-out mechanism to honor your preferences under applicable privacy laws.
                </div>

                <!-- What this controls -->
                <section class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">What This Controls</h2>
                    <div class="text-sm text-surface-600 dark:text-surface-400 leading-relaxed space-y-3">
                        <p>By opting out, you are requesting that EzyTools:</p>
                        <ul class="list-disc list-inside space-y-1 pl-2">
                            <li>Does not share your usage data with third-party analytics or advertising platforms.</li>
                            <li>Disables any non-essential tracking cookies or identifiers.</li>
                            <li>Excludes your data from any future data-sharing arrangements.</li>
                        </ul>
                        <p>This preference is stored locally in your browser and takes effect immediately. It persists until you change it or clear your browser data.</p>
                    </div>
                </section>

                <!-- Opt-out toggle -->
                <section class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">Your Preference</h2>

                    <div class="flex items-center justify-between p-4 rounded-xl" :class="optedOut ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-surface-50 dark:bg-surface-900 border border-surface-200 dark:border-surface-700'">
                        <div>
                            <p class="font-semibold text-sm" :class="optedOut ? 'text-green-800 dark:text-green-200' : 'text-surface-900 dark:text-white'">
                                {{ optedOut ? 'You have opted out' : 'You have not opted out' }}
                            </p>
                            <p class="text-xs mt-0.5" :class="optedOut ? 'text-green-600 dark:text-green-400' : 'text-surface-500 dark:text-surface-400'">
                                {{ optedOut ? 'Your data will not be sold or shared.' : 'Toggle the switch to opt out of data selling/sharing.' }}
                            </p>
                        </div>
                        <button @click="toggleOptOut"
                            class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-surface-800"
                            :class="optedOut ? 'bg-green-600' : 'bg-surface-300 dark:bg-surface-600'"
                            role="switch" :aria-checked="optedOut">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform"
                                :class="optedOut ? 'translate-x-6' : 'translate-x-1'" />
                        </button>
                    </div>

                    <!-- Confirmation -->
                    <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                        <div v-if="showConfirm" class="mt-4 p-3 rounded-lg bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
                            <CheckCircleIcon class="w-5 h-5 shrink-0" />
                            Your preference has been saved.
                        </div>
                    </transition>
                </section>

                <!-- Additional info -->
                <section class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8">
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white mb-4">Additional Information</h2>
                    <div class="text-sm text-surface-600 dark:text-surface-400 leading-relaxed space-y-3">
                        <p>This preference is device and browser-specific. If you use multiple browsers or devices, you will need to set this preference on each one.</p>
                        <p>If you have an EzyTools account and wish to submit a formal data deletion request, please visit our <a :href="route('pages.contact')" class="text-primary-600 hover:underline">Contact page</a> or email <a href="mailto:support@ezytools.app" class="text-primary-600 hover:underline">support@ezytools.app</a>.</p>
                        <p>For more details about our data practices, see our <a :href="route('pages.privacy')" class="text-primary-600 hover:underline">Privacy Policy</a> and <a :href="route('pages.gdpr')" class="text-primary-600 hover:underline">GDPR Compliance</a> page.</p>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ShieldExclamationIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const STORAGE_KEY = 'ezytools_do_not_sell';
const optedOut = ref(false);
const showConfirm = ref(false);

onMounted(() => {
    optedOut.value = localStorage.getItem(STORAGE_KEY) === 'true';
});

const toggleOptOut = () => {
    optedOut.value = !optedOut.value;
    localStorage.setItem(STORAGE_KEY, optedOut.value.toString());
    showConfirm.value = true;
    setTimeout(() => { showConfirm.value = false; }, 3000);
};
</script>
