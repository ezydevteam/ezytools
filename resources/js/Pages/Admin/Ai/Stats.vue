<template>
    <AdminLayout>
        <Head title="AI Usage Stats" />
        
        <template #header>
            <div class="flex justify-between items-center w-full">
                <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">AI Usage Stats</h2>
                <div class="flex gap-2">
                    <select v-model="selectedDays" @change="updateRange" class="border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 rounded-md shadow-sm text-sm py-1.5 pl-3 pr-8">
                        <option value="7">Last 7 Days</option>
                        <option value="14">Last 14 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Provider Costs -->
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Cost by Provider</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-for="item in providerCosts" :key="item.name" class="p-4 rounded-lg bg-surface-50 dark:bg-surface-900 border border-surface-200 dark:border-surface-700">
                        <p class="text-sm font-medium text-surface-500 dark:text-surface-400">{{ item.name }}</p>
                        <p class="text-xl font-bold text-surface-900 dark:text-white mt-1">${{ item.cost.toFixed(4) }}</p>
                    </div>
                </div>
                <div v-if="providerCosts.length === 0" class="text-center py-8 text-surface-500 dark:text-surface-400">
                    No cost data available for this period.
                </div>
            </div>

            <!-- Daily Usage Chart Data placeholder -->
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Daily Requests & Cost</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                        <thead class="bg-surface-50 dark:bg-surface-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Requests</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Cost (USD)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-surface-900 divide-y divide-surface-200 dark:divide-surface-700">
                            <tr v-for="day in dailyUsage" :key="day.date" class="hover:bg-surface-50 dark:hover:bg-surface-800/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-surface-900 dark:text-white">{{ formatDate(day.date) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-500 dark:text-surface-400 text-right">{{ formatNumber(day.requests) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 dark:text-red-400 font-mono text-right">${{ parseFloat(day.cost).toFixed(4) }}</td>
                            </tr>
                            <tr v-if="dailyUsage.length === 0">
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-surface-500 dark:text-surface-400">No data found for this period.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-4">Recent 50 Requests</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                        <thead class="bg-surface-50 dark:bg-surface-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Time</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">User/IP</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Tool</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Model</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Tokens</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Cost</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-surface-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-surface-900 divide-y divide-surface-200 dark:divide-surface-700">
                            <tr v-for="req in recentRequests" :key="req.id" class="hover:bg-surface-50 dark:hover:bg-surface-800/50">
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-surface-500">{{ formatDateTime(req.created_at) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div v-if="req.user" class="text-sm font-medium text-surface-900 dark:text-white">{{ req.user.name }}</div>
                                    <div v-else class="text-sm text-surface-500 font-mono">{{ req.ip_address }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-surface-900 dark:text-surface-200">{{ req.tool?.name || 'Unknown' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-surface-100 text-surface-800 dark:bg-surface-800 dark:text-surface-300">
                                        {{ req.model?.name || 'Unknown' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-surface-500 text-right">
                                    <span class="text-blue-600 dark:text-blue-400" title="Input">{{ req.input_tokens }}</span>
                                    <span class="mx-1">/</span>
                                    <span class="text-green-600 dark:text-green-400" title="Output">{{ req.output_tokens }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-right">${{ parseFloat(req.cost_usd).toFixed(5) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span v-if="req.status === 'success'" class="inline-flex items-center p-1 rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400" title="Success">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </span>
                                    <span v-else class="inline-flex items-center p-1 rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400" :title="req.error_message">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    dailyUsage: Array,
    providerCosts: Array,
    recentRequests: Array,
    days: Number,
});

const selectedDays = ref(props.days.toString());

const updateRange = () => {
    router.get(route('admin.ai.stats'), { days: selectedDays.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num || 0);
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatDateTime = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>
