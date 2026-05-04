<template>
    <div class="min-h-screen bg-surface-50 dark:bg-surface-900 flex transition-colors duration-200">
        <!-- Sidebar -->
        <Sidebar :isOpen="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white dark:bg-surface-800 shadow-sm z-10 flex items-center justify-between px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-surface-500 hover:text-surface-700 dark:text-surface-400 dark:hover:text-surface-200">
                        <Bars3Icon class="w-6 h-6" />
                    </button>
                    <h1 class="text-xl font-semibold text-surface-800 dark:text-surface-100 font-bangla" v-if="$slots.header">
                        <slot name="header" />
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Visit Site -->
                    <a :href="route('home')" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-surface-600 dark:text-surface-300 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors">
                        <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                        Visit Site
                    </a>

                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDark" class="p-2 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors">
                        <SunIcon v-if="isDark" class="w-5 h-5 text-yellow-400" />
                        <MoonIcon v-else class="w-5 h-5 text-surface-600" />
                    </button>
                    
                    <!-- Admin Avatar -->
                    <div class="relative">
                        <img class="w-8 h-8 rounded-full border-2 border-surface-200 dark:border-surface-700 object-cover" 
                             :src="$page.props.auth.user.avatar ? ($page.props.auth.user.avatar.startsWith('http') ? $page.props.auth.user.avatar : '/storage/' + $page.props.auth.user.avatar) : `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=6366f1&color=fff&size=64`" 
                             :alt="$page.props.auth.user.name" />
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white dark:border-surface-800 rounded-full"></span>
                    </div>
                </div>
            </header>

            <!-- AI Spend Alert -->
            <div v-if="showSpendAlert" class="px-6 pt-4">
                <div class="p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-300 dark:border-amber-700 flex items-start gap-3">
                    <ExclamationTriangleIcon class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300">AI Spending Alert</h4>
                        <p class="text-sm text-amber-700 dark:text-amber-400 mt-0.5">
                            Today's AI spend: <strong>${{ $page.props.aiSpendAlert.spent }}</strong> of ${{ $page.props.aiSpendAlert.budget }} budget
                            (<span :class="$page.props.aiSpendAlert.percent >= 100 ? 'text-red-600 dark:text-red-400 font-bold' : ''">{{ $page.props.aiSpendAlert.percent }}%</span>).
                            Threshold was ${{ $page.props.aiSpendAlert.threshold }}.
                        </p>
                        <Link :href="route('admin.ai.settings')" class="text-xs font-medium text-amber-800 dark:text-amber-300 underline hover:no-underline mt-1 inline-block">Manage AI Budget →</Link>
                    </div>
                    <button @click="dismissSpendAlert" class="text-amber-500 hover:text-amber-700 dark:hover:text-amber-300 transition-colors p-1 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-800/30 shrink-0">
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.success || $page.props.flash?.error" class="px-6 pt-4">
                <div v-if="$page.props.flash?.success" class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm flex items-center gap-2">
                    <CheckCircleIcon class="w-5 h-5 shrink-0" />
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm flex items-center gap-2">
                    <ExclamationCircleIcon class="w-5 h-5 shrink-0" />
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Bars3Icon, SunIcon, MoonIcon, ArrowTopRightOnSquareIcon, CheckCircleIcon, ExclamationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/solid';
import Sidebar from '@/Components/Layout/Sidebar.vue';

const sidebarOpen = ref(false);
const isDark = ref(false);
const spendAlertDismissed = ref(false);

const showSpendAlert = computed(() => {
    const alert = usePage().props.aiSpendAlert;
    return alert && !spendAlertDismissed.value;
});

const dismissSpendAlert = () => {
    spendAlertDismissed.value = true;
    const alert = usePage().props.aiSpendAlert;
    if (alert) {
        sessionStorage.setItem('ai_spend_alert_dismissed', String(alert.spent));
    }
};

const toggleDark = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }

    // Check if alert was already dismissed for the current spend level
    const alert = usePage().props.aiSpendAlert;
    const dismissed = sessionStorage.getItem('ai_spend_alert_dismissed');
    if (alert && dismissed && parseFloat(dismissed) >= alert.spent) {
        spendAlertDismissed.value = true;
    }
});
</script>
