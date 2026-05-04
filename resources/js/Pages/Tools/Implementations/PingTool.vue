<template>
    <div class="space-y-6">
        <!-- Input -->
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto">
                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 block">Enter host or IP</label>
                <div class="flex gap-3">
                    <input v-model="host" type="text" placeholder="google.com or 8.8.8.8" @keyup.enter="ping"
                           class="flex-1 px-4 py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    <button @click="ping" :disabled="!host.trim() || isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        📡 Ping
                    </button>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-400 font-medium">{{ error }}</div>

        <!-- Result -->
        <div v-if="result" class="space-y-4">
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Min</p>
                    <p class="text-2xl font-black text-green-500">{{ result.stats.min ?? '—' }}<span class="text-xs font-normal text-surface-400">ms</span></p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Avg</p>
                    <p class="text-2xl font-black text-primary-500">{{ result.stats.avg ?? '—' }}<span class="text-xs font-normal text-surface-400">ms</span></p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Max</p>
                    <p class="text-2xl font-black text-amber-500">{{ result.stats.max ?? '—' }}<span class="text-xs font-normal text-surface-400">ms</span></p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Loss</p>
                    <p class="text-2xl font-black" :class="result.stats.loss_percent === 0 ? 'text-green-500' : 'text-red-500'">{{ result.stats.loss_percent }}%</p>
                </div>
            </div>

            <!-- Ping results -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/50">
                    <h3 class="font-bold text-surface-900 dark:text-white">Ping <span class="font-mono text-primary-600">{{ result.host }}</span> <span class="text-surface-400 font-normal">({{ result.ip }})</span></h3>
                </div>
                <div class="p-4 space-y-2">
                    <div v-for="r in result.results" :key="r.seq"
                         class="flex items-center gap-4 px-4 py-3 rounded-lg"
                         :class="r.status === 'ok' ? 'bg-green-50 dark:bg-green-900/10' : 'bg-red-50 dark:bg-red-900/10'">
                        <span class="text-sm font-mono text-surface-500 w-8">#{{ r.seq }}</span>
                        <div v-if="r.status === 'ok'" class="flex-1 flex items-center gap-3">
                            <div class="flex-1 bg-surface-200 dark:bg-surface-700 rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all"
                                     :class="r.latency < 50 ? 'bg-green-500' : r.latency < 200 ? 'bg-amber-500' : 'bg-red-500'"
                                     :style="{ width: Math.min(100, (r.latency / 500) * 100) + '%' }"></div>
                            </div>
                            <span class="text-sm font-bold font-mono" :class="r.latency < 50 ? 'text-green-600' : r.latency < 200 ? 'text-amber-600' : 'text-red-600'">{{ r.latency }}ms</span>
                        </div>
                        <span v-else class="text-sm font-medium text-red-500">⏱ Request Timed Out</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const host = ref('');
const isLoading = ref(false);
const error = ref(null);
const result = ref(null);

const ping = async () => {
    if (!host.value.trim()) return;
    isLoading.value = true;
    error.value = null;
    result.value = null;

    try {
        const res = await axios.post('/api/web-tools/ping', { host: host.value });
        result.value = res.data;
    } catch (e) {
        error.value = e.response?.data?.message || 'Ping failed.';
    } finally {
        isLoading.value = false;
    }
};
</script>
