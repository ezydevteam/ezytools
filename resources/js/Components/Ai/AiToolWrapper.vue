<template>
    <div class="relative min-h-[400px]">
        <!-- Upgrade Modal (if hit limit) -->
        <div v-if="remaining === 0 && !isPro" class="absolute inset-0 z-20 flex flex-col items-center justify-center p-6 bg-white/80 dark:bg-surface-900/80 backdrop-blur-sm rounded-2xl">
            <div class="bg-white dark:bg-surface-800 p-8 rounded-2xl shadow-xl border border-surface-200 dark:border-surface-700 max-w-md w-full text-center">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-surface-900 dark:text-white mb-2">Daily Limit End!</h3>
                <p class="text-surface-600 dark:text-surface-400 mb-6">
                    You have exceeded today's free AI limit. Upgrade to Pro for unlimited AI access or try again tomorrow.
                </p>
                <div class="space-y-3">
                    <Link href="/pricing" class="block w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl transition-colors">
                        Upgrade to Pro
                    </Link>
                    <button v-if="!$page.props.auth.user" @click="visitLogin" class="block w-full py-3 px-4 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-900 dark:text-white font-medium rounded-xl transition-colors">
                        Login for More Limit
                    </button>
                </div>
            </div>
        </div>

        <!-- Tool Content -->
        <div :class="{'opacity-50 pointer-events-none blur-sm': remaining === 0 && !isPro}">
            <slot></slot>
        </div>

        <!-- AI Powered Badge & Limit -->
        <div class="flex flex-wrap items-center justify-between gap-4 mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400">
                    <svg class="w-3.5 h-3.5 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" /></svg>
                    AI Powered
                </span>
            </div>
            <div class="text-xs text-surface-500 dark:text-surface-400 text-right">
                <span v-if="isPro || remaining === 'unlimited'" class="font-medium text-primary-600 dark:text-primary-400">Pro (Unlimited)</span>
                <span v-else-if="remaining !== null" :class="{'text-red-500 font-bold': typeof remaining === 'number' && remaining <= 1}">
                    {{ remaining }} requests remaining today
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    remaining: {
        type: [Number, String, null],
        default: null
    }
});

const page = usePage();
const isPro = computed(() => page.props.auth.user?.is_pro || false);

const visitLogin = () => {
    router.visit('/login');
};
</script>
