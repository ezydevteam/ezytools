<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <template #header>
            <div class="flex items-center gap-3">
                <span>Dashboard</span>
                <span class="text-xs font-medium bg-surface-100 dark:bg-surface-700 text-surface-500 dark:text-surface-400 px-2 py-0.5 rounded-full">Overview</span>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Welcome Banner + Period Filter -->
            <div class="bg-gradient-to-r from-primary-600 to-purple-700 rounded-2xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
                <div class="absolute bottom-0 left-1/2 w-48 h-48 bg-white/5 rounded-full translate-y-1/2"></div>
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold mb-1">Welcome back, {{ $page.props.auth.user.name }}! 👋</h2>
                        <p class="text-primary-100 text-sm">Showing statistics for <strong>{{ periodLabel }}</strong>.</p>
                    </div>
                    <select
                        v-model="selectedPeriod"
                        @change="changePeriod"
                        class="mt-0.5 bg-white/15 border border-white/20 text-white text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-white/30 focus:border-white/40 cursor-pointer backdrop-blur-sm min-w-[180px] [&>option]:text-surface-900 [&>optgroup]:text-surface-500 [&>optgroup]:font-semibold"
                    >
                        <option v-for="opt in periodOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Users -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-5 group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <UsersIcon class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div v-if="stats.new_users > 0" class="flex items-center gap-1 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full">
                            <ArrowUpIcon class="w-3 h-3" />
                            +{{ formatNumber(stats.new_users) }}
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ formatNumber(stats.total_users) }}</p>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Total Users</p>
                </div>

                <!-- Active Tools -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-5 group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <WrenchScrewdriverIcon class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-xs text-surface-400">of {{ stats.total_tools }}</span>
                    </div>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ stats.active_tools }}</p>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Active Tools</p>
                </div>

                <!-- Usages -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-5 group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <ChartBarIcon class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ formatNumber(stats.total_usages) }}</p>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Total Usages</p>
                </div>

                <!-- Revenue -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-5 group hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                            <CurrencyBangladeshiIcon class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-xs text-surface-400">{{ stats.active_subscriptions }} subs</span>
                    </div>
                    <p class="text-2xl font-bold text-surface-900 dark:text-white">৳{{ formatNumber(stats.revenue) }}</p>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-0.5">Revenue</p>
                </div>
            </div>

            <!-- Secondary Stats Row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 px-5 py-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <SparklesIcon class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <p class="text-lg font-bold text-surface-900 dark:text-white">{{ stats.pro_users }}</p>
                        <p class="text-xs text-surface-500 dark:text-surface-400">Pro Users</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 px-5 py-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center shrink-0">
                        <Squares2X2Icon class="w-4.5 h-4.5 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div>
                        <p class="text-lg font-bold text-surface-900 dark:text-white">{{ stats.total_categories }}</p>
                        <p class="text-xs text-surface-500 dark:text-surface-400">Categories</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 px-5 py-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
                        <UserPlusIcon class="w-4.5 h-4.5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div>
                        <p class="text-lg font-bold text-surface-900 dark:text-white">{{ formatNumber(stats.new_users) }}</p>
                        <p class="text-xs text-surface-500 dark:text-surface-400">New Users</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 px-5 py-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0">
                        <FireIcon class="w-4.5 h-4.5 text-rose-600 dark:text-rose-400" />
                    </div>
                    <div>
                        <p class="text-lg font-bold text-surface-900 dark:text-white">{{ formatNumber(stats.total_usages) }}</p>
                        <p class="text-xs text-surface-500 dark:text-surface-400">Total Usages</p>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Usage Trend -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <h3 class="text-sm font-semibold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                        <ChartBarIcon class="w-4 h-4 text-primary-500" />
                        Usage Trend
                        <span class="text-xs font-normal text-surface-400 ml-auto">{{ periodLabel }}</span>
                    </h3>
                    <div class="flex items-end gap-1 h-32" v-if="usageTrend.length">
                        <div v-for="(day, i) in usageTrend" :key="i" class="flex-1 flex flex-col items-center gap-1 min-w-0">
                            <span class="text-[10px] font-semibold text-surface-900 dark:text-white truncate">{{ day.count }}</span>
                            <div class="w-full bg-primary-500/20 rounded-t-md relative overflow-hidden"
                                 :style="{ height: getBarHeight(day.count, usageTrend) + '%', minHeight: '4px' }">
                                <div class="absolute inset-0 bg-gradient-to-t from-primary-600 to-purple-500 rounded-t-md"></div>
                            </div>
                            <span class="text-[9px] text-surface-400 whitespace-nowrap truncate max-w-full">{{ day.label }}</span>
                        </div>
                    </div>
                    <div v-else class="h-32 flex items-center justify-center text-surface-400 text-sm">No data</div>
                </div>

                <!-- Signup Trend -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                    <h3 class="text-sm font-semibold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                        <UserPlusIcon class="w-4 h-4 text-green-500" />
                        Signup Trend
                        <span class="text-xs font-normal text-surface-400 ml-auto">{{ periodLabel }}</span>
                    </h3>
                    <div class="flex items-end gap-1 h-32" v-if="signupTrend.length">
                        <div v-for="(day, i) in signupTrend" :key="i" class="flex-1 flex flex-col items-center gap-1 min-w-0">
                            <span class="text-[10px] font-semibold text-surface-900 dark:text-white truncate">{{ day.count }}</span>
                            <div class="w-full bg-primary-500/20 rounded-t-md relative overflow-hidden"
                                 :style="{ height: getBarHeight(day.count, signupTrend) + '%', minHeight: '4px' }">
                                <div class="absolute inset-0 bg-gradient-to-t from-primary-600 to-purple-500 rounded-t-md"></div>
                            </div>
                            <span class="text-[9px] text-surface-400 whitespace-nowrap truncate max-w-full">{{ day.label }}</span>
                        </div>
                    </div>
                    <div v-else class="h-32 flex items-center justify-center text-surface-400 text-sm">No data</div>
                </div>
            </div>

            <!-- Line Chart Row -->
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                    <CurrencyBangladeshiIcon class="w-4 h-4 text-purple-500" />
                    Pro Revenue vs AI Cost
                    <span class="text-xs font-normal text-surface-400 ml-auto">{{ periodLabel }}</span>
                </h3>
                <div class="h-64" v-if="revenueTrend.length || aiCostTrend.length">
                    <LineChart :data="lineChartData" :options="lineChartOptions" />
                </div>
                <div v-else class="h-64 flex items-center justify-center text-surface-400 text-sm">No data</div>
            </div>

            <!-- Tables Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Top Tools -->
                <div class="lg:col-span-2 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-surface-900 dark:text-white flex items-center gap-2">
                            <FireIcon class="w-4 h-4 text-orange-500" />
                            Most Popular Tools
                        </h3>
                        <Link :href="route('admin.tools.index')" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View All →</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-surface-400 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-surface-400 uppercase tracking-wider">Tool</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-surface-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-right text-[11px] font-semibold text-surface-400 uppercase tracking-wider">Usages</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100 dark:divide-surface-700/50">
                                <tr v-for="(tool, index) in topTools" :key="tool.id" class="hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <span class="text-xs font-bold" :class="index < 3 ? 'text-amber-500' : 'text-surface-400'">{{ index + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-surface-900 dark:text-white">{{ tool.name }}</span>
                                            <span v-if="tool.is_premium" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">PRO</span>
                                            <span v-if="!tool.is_active" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">OFF</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-xs text-surface-500 dark:text-surface-400">
                                        {{ tool.category?.name || '—' }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right">
                                        <span class="text-sm font-semibold text-surface-900 dark:text-white">{{ formatNumber(tool.usage_count) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Signups -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-surface-900 dark:text-white flex items-center gap-2">
                            <UsersIcon class="w-4 h-4 text-blue-500" />
                            Recent Signups
                        </h3>
                        <Link :href="route('admin.users.index')" class="text-xs text-primary-600 hover:text-primary-700 font-medium">View All →</Link>
                    </div>
                    <ul class="divide-y divide-surface-100 dark:divide-surface-700/50">
                        <li v-for="user in recentSignups" :key="user.id" class="px-6 py-3.5 flex items-center gap-3 hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors">
                            <img class="h-9 w-9 rounded-full object-cover ring-2 ring-surface-100 dark:ring-surface-700 shrink-0"
                                 :src="user.avatar ? (user.avatar.startsWith('http') ? user.avatar : '/storage/' + user.avatar) : `https://ui-avatars.com/api/?name=${user.name}&background=6366f1&color=fff&size=64`"
                                 :alt="user.name" />
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ user.name }}</p>
                                    <span v-if="user.subscription_type === 'pro'" class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400">PRO</span>
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400 truncate">{{ user.email }}</p>
                            </div>
                            <span class="text-[10px] text-surface-400 whitespace-nowrap shrink-0">{{ timeAgo(user.created_at) }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Geographic & Source Data -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Countries -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden p-6">
                    <h3 class="text-sm font-semibold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                        Top Country Visitors
                    </h3>
                    <ul class="space-y-4">
                        <li v-for="country in topCountries" :key="country.code">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-surface-900 dark:text-white flex items-center gap-2">
                                    <img :src="`https://flagcdn.com/20x15/${country.code.toLowerCase()}.png`" :alt="country.country" class="rounded-sm" />
                                    {{ country.country }}
                                </span>
                                <span class="text-surface-500 dark:text-surface-400">{{ formatNumber(country.visitors) }}</span>
                            </div>
                            <div class="w-full bg-surface-100 dark:bg-surface-700 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-primary-600 to-purple-500 h-1.5 rounded-full" :style="{ width: getBarHeight(country.visitors, topCountries) + '%' }"></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Visitor Sources -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden p-6">
                    <h3 class="text-sm font-semibold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                        Visitor Sources
                    </h3>
                    <ul class="space-y-4">
                        <li v-for="source in visitorSources" :key="source.source">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-surface-900 dark:text-white">{{ source.source }}</span>
                                <div class="flex items-center gap-2 text-surface-500 dark:text-surface-400">
                                    <span>{{ source.percentage }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-surface-100 dark:bg-surface-700 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-primary-600 to-purple-500 h-1.5 rounded-full" :style="{ width: source.percentage + '%' }"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import {
    UsersIcon,
    WrenchScrewdriverIcon,
    ChartBarIcon,
    ArrowUpIcon,
    SparklesIcon,
    Squares2X2Icon,
    UserPlusIcon,
    FireIcon,
} from '@heroicons/vue/24/outline';
import { BanknotesIcon as CurrencyBangladeshiIcon } from '@heroicons/vue/24/outline';

import { Line as LineChart } from 'vue-chartjs';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

const props = defineProps({
    stats: Object,
    recentSignups: Array,
    topTools: Array,
    usageTrend: Array,
    signupTrend: Array,
    revenueTrend: Array,
    aiCostTrend: Array,
    topCountries: Array,
    visitorSources: Array,
    period: String,
    periodLabel: String,
    periodOptions: Array,
});

const selectedPeriod = ref(props.period);

const changePeriod = () => {
    router.get(route('admin.dashboard'), { period: selectedPeriod.value }, {
        preserveState: true,
        preserveScroll: true,
        only: ['stats', 'usageTrend', 'signupTrend', 'revenueTrend', 'aiCostTrend', 'topCountries', 'visitorSources', 'period', 'periodLabel'],
    });
};

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num || 0);
};

const timeAgo = (dateStr) => {
    const now = new Date();
    const date = new Date(dateStr);
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
};

const getBarHeight = (value, data) => {
    let max = 0;
    if (data && data.length > 0) {
        if (data[0].count !== undefined) max = Math.max(...data.map(d => d.count));
        else if (data[0].visitors !== undefined) max = Math.max(...data.map(d => d.visitors));
    }
    max = Math.max(max, 1);
    return (value / max) * 100;
};

const lineChartData = computed(() => {
    return {
        labels: props.revenueTrend?.map(t => t.date) || [],
        datasets: [
            {
                label: 'Pro Revenue',
                backgroundColor: 'rgba(79, 70, 229, 0.1)', // primary-600
                borderColor: '#4f46e5',
                data: props.revenueTrend?.map(t => t.count) || [],
                fill: true,
                tension: 0.4,
                pointRadius: 2,
            },
            {
                label: 'AI Cost ($)',
                backgroundColor: 'rgba(147, 51, 234, 0.1)', // purple-600
                borderColor: '#9333ea',
                data: props.aiCostTrend?.map(t => t.count) || [],
                fill: true,
                tension: 0.4,
                pointRadius: 2,
            }
        ]
    }
});

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(156, 163, 175, 0.1)' }
        },
        x: {
            grid: { display: false }
        }
    }
};
</script>
