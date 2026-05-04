<template>
    <AppLayout title="Payment Pending">
        <div class="min-h-[60vh] flex items-center justify-center p-4">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 p-8 max-w-md w-full text-center">

                <!-- Animated Pending Icon -->
                <div class="w-20 h-20 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 dark:bg-amber-900/30 dark:text-amber-400 relative">
                    <svg class="w-10 h-10 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Payment Processing</h1>
                <p class="text-surface-600 dark:text-surface-400 mb-4">
                    Your payment is being processed. This usually takes a few moments.
                </p>

                <!-- Subscription Details -->
                <div v-if="subscription" class="bg-surface-50 dark:bg-surface-900/50 rounded-xl p-4 mb-6 text-left border border-surface-200 dark:border-surface-700">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-surface-500 dark:text-surface-400">Plan</span>
                        <span class="font-semibold text-surface-900 dark:text-white capitalize">{{ subscription.plan }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-surface-500 dark:text-surface-400">Amount</span>
                        <span class="font-semibold text-surface-900 dark:text-white">৳{{ subscription.amount }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-surface-500 dark:text-surface-400">Status</span>
                        <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-semibold">
                            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                            Pending
                        </span>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6 text-left">
                    <p class="text-sm text-blue-700 dark:text-blue-400">
                        <strong>What happens next?</strong><br>
                        Your Pro features will be activated automatically once the payment is confirmed.
                        You'll receive an email notification when it's done.
                    </p>
                </div>

                <div class="space-y-3">
                    <button @click="checkStatus" :disabled="checking" class="block w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white rounded-xl font-bold text-center transition-colors shadow-sm disabled:opacity-50">
                        <span v-if="checking" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Checking...
                        </span>
                        <span v-else>Check Payment Status</span>
                    </button>
                    <Link :href="route('user.dashboard')" class="block w-full py-3 px-4 bg-surface-100 hover:bg-surface-200 text-surface-700 dark:bg-surface-700 dark:hover:bg-surface-600 dark:text-surface-200 rounded-xl font-bold text-center transition-colors">
                        Go to Dashboard
                    </Link>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    subscription: Object,
});

const checking = ref(false);

const checkStatus = () => {
    checking.value = true;
    router.reload({
        only: ['subscription'],
        onFinish: () => {
            checking.value = false;
        },
    });
};
</script>
