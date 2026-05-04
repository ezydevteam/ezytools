<template>
    <div class="space-y-6">
        <!-- Input -->
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto">
                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 block">Enter domain name</label>
                <div class="flex gap-3">
                    <input v-model="domain" type="text" placeholder="example.com" @keyup.enter="lookup"
                           class="flex-1 px-4 py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    <button @click="lookup" :disabled="!domain.trim() || isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        🔍 WHOIS
                    </button>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-400 font-medium">{{ error }}</div>

        <!-- Parsed Results -->
        <div v-if="result" class="space-y-4">
            <div v-if="Object.keys(result.parsed).length" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/50">
                    <h3 class="font-bold text-surface-900 dark:text-white">WHOIS Info: <span class="text-primary-600">{{ result.domain }}</span></h3>
                </div>
                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                    <div v-for="(value, key) in result.parsed" :key="key" class="px-6 py-3 flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4">
                        <span class="text-xs font-semibold text-surface-400 uppercase tracking-wider sm:w-40 shrink-0">{{ formatKey(key) }}</span>
                        <div class="text-sm text-surface-900 dark:text-white font-mono break-all">
                            <template v-if="Array.isArray(value)">
                                <div v-for="(v, i) in value" :key="i">{{ v }}</div>
                            </template>
                            <template v-else>{{ value }}</template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Raw WHOIS Toggle -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <button @click="showRaw = !showRaw" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Raw WHOIS Data</span>
                    <span class="text-surface-400 text-xs">{{ showRaw ? '▲ Hide' : '▼ Show' }}</span>
                </button>
                <pre v-if="showRaw" class="px-6 pb-6 text-xs text-surface-600 dark:text-surface-400 font-mono whitespace-pre-wrap max-h-96 overflow-y-auto leading-relaxed">{{ result.raw }}</pre>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const domain = ref('');
const isLoading = ref(false);
const error = ref(null);
const result = ref(null);
const showRaw = ref(false);

const formatKey = (key) => key.replace(/_/g, ' ');

const lookup = async () => {
    if (!domain.value.trim()) return;
    isLoading.value = true;
    error.value = null;
    result.value = null;
    showRaw.value = false;

    try {
        const res = await axios.post('/api/web-tools/whois-lookup', { domain: domain.value });
        result.value = res.data;
    } catch (e) {
        error.value = e.response?.data?.message || 'WHOIS lookup failed.';
    } finally {
        isLoading.value = false;
    }
};
</script>
