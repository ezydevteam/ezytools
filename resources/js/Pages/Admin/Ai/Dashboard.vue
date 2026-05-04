<template>
    <AdminLayout>
        <Head title="AI Dashboard" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">AI Dashboard</h2>
                <div class="flex gap-2 ms-4">
                    <Link :href="route('admin.ai.settings')" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-primary-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm">
                        Settings
                    </Link>
                    <Link :href="route('admin.ai.providers.index')" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-primary-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm">
                        Providers
                    </Link>
                    <Link :href="route('admin.ai.voices.index')" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-primary-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm">
                        Voices
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- AI Requests Today -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <ChartBarIcon class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Requests Today</p>
                            <p class="text-2xl font-semibold text-surface-900 dark:text-white">{{ formatNumber(stats.requests_today) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tokens This Month -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <DocumentTextIcon class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Tokens (Month)</p>
                            <p class="text-2xl font-semibold text-surface-900 dark:text-white">{{ formatNumber(stats.tokens_month) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cost This Month -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                            <BanknotesIcon class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Cost (Month)</p>
                            <p class="text-2xl font-semibold text-surface-900 dark:text-white">${{ stats.cost_month.toFixed(4) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Most Used Tool -->
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <SparklesIcon class="w-6 h-6" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Top AI Tool</p>
                            <p class="text-lg font-semibold text-surface-900 dark:text-white truncate" :title="stats.most_used_tool">{{ stats.most_used_tool }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Providers Overview -->
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex justify-between items-center bg-surface-50/50 dark:bg-surface-800/50">
                    <h3 class="text-lg font-semibold text-surface-900 dark:text-white">Provider Status & Spend</h3>
                    <Link :href="route('admin.ai.stats')" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View Full Stats &rarr;</Link>
                </div>
                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                    <div v-for="provider in providers" :key="provider.id" class="p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-xl" :class="getProviderColor(provider.name)">
                                {{ provider.label.charAt(0) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base font-bold text-surface-900 dark:text-white">{{ provider.label }}</h4>
                                    <span v-if="provider.is_active" class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                    <span v-else class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                                    <span v-if="provider.is_default" class="px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">Default</span>
                                </div>
                                <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                                    {{ provider.models_count }} active models
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-surface-500 dark:text-surface-400">Spent this month</p>
                            <p class="text-xl font-bold text-surface-900 dark:text-white">${{ provider.cost_this_month.toFixed(4) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    ChartBarIcon,
    DocumentTextIcon,
    BanknotesIcon,
    SparklesIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Object,
    providers: Array,
});

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num || 0);
};

const getProviderColor = (name) => {
    switch(name) {
        case 'openai': return 'bg-emerald-500';
        case 'gemini': return 'bg-blue-500';
        case 'grok': return 'bg-gray-800 dark:bg-gray-700';
        default: return 'bg-surface-500';
    }
};
</script>
