<template>
    <AppLayout title="Payment Success">
        <div class="min-h-[60vh] flex items-center justify-center p-4">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 p-8 max-w-md w-full text-center">
                
                <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 dark:bg-green-900/30 dark:text-green-400">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h1 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Payment Successful!</h1>
                <p class="text-surface-600 dark:text-surface-400 mb-8">
                    Thank you for upgrading to Pro. Your account features have been unlocked.
                </p>
                
                <div class="space-y-3">
                    <Link :href="route('dashboard')" class="block w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white rounded-xl font-bold text-center transition-colors shadow-sm">
                        Go to Dashboard
                    </Link>
                    <Link :href="route('home')" class="block w-full py-3 px-4 bg-surface-100 hover:bg-surface-200 text-surface-700 dark:bg-surface-700 dark:hover:bg-surface-600 dark:text-surface-200 rounded-xl font-bold text-center transition-colors">
                        Browse Tools
                    </Link>
                </div>
                
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>
