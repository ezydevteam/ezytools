<template>
    <div class="space-y-6">
        <!-- Input -->
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto">
                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 block">Enter domain name</label>
                <div class="flex gap-3">
                    <input v-model="domain" type="text" placeholder="example.com"
                           @keyup.enter="lookup"
                           class="flex-1 px-4 py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    <button @click="lookup" :disabled="!domain.trim() || isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        🔍 Lookup
                    </button>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-400 font-medium">{{ error }}</div>

        <!-- Results -->
        <div v-if="result" class="space-y-4">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between bg-surface-50/50 dark:bg-surface-800/50">
                    <h3 class="font-bold text-surface-900 dark:text-white">DNS Records for <span class="text-primary-600">{{ result.domain }}</span></h3>
                    <span class="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-600 px-2 py-1 rounded-lg font-medium">{{ result.total }} records</span>
                </div>

                <!-- Filter tabs -->
                <div class="flex gap-1 px-4 pt-4 flex-wrap">
                    <button v-for="t in recordTypes" :key="t" @click="activeFilter = t"
                            :class="activeFilter === t ? 'bg-primary-600 text-white' : 'bg-surface-100 dark:bg-surface-900 text-surface-600 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-800'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">{{ t }}</button>
                </div>

                <!-- Records table -->
                <div class="overflow-x-auto p-4">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-surface-500 border-b border-surface-200 dark:border-surface-700">
                                <th class="pb-2 pr-4 w-20">Type</th>
                                <th class="pb-2 pr-4">Name</th>
                                <th class="pb-2 pr-4">Value</th>
                                <th class="pb-2 w-20">TTL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-surface-700">
                            <tr v-for="(r, i) in filteredRecords" :key="i" class="hover:bg-surface-50 dark:hover:bg-surface-900/50">
                                <td class="py-2.5 pr-4"><span class="px-2 py-0.5 rounded text-xs font-bold" :class="typeColor(r.type)">{{ r.type }}</span></td>
                                <td class="py-2.5 pr-4 text-surface-700 dark:text-surface-300 font-mono text-xs">{{ r.name }}</td>
                                <td class="py-2.5 pr-4 text-surface-900 dark:text-white font-mono text-xs break-all">{{ r.value }}</td>
                                <td class="py-2.5 text-surface-400 text-xs">{{ r.ttl ? r.ttl + 's' : '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="!filteredRecords.length" class="text-center py-8 text-surface-400 text-sm">No {{ activeFilter }} records found.</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const domain = ref('');
const isLoading = ref(false);
const error = ref(null);
const result = ref(null);
const activeFilter = ref('All');

const recordTypes = computed(() => {
    if (!result.value?.records) return ['All'];
    const types = [...new Set(result.value.records.map(r => r.type))];
    return ['All', ...types.sort()];
});

const filteredRecords = computed(() => {
    if (!result.value?.records) return [];
    if (activeFilter.value === 'All') return result.value.records;
    return result.value.records.filter(r => r.type === activeFilter.value);
});

const typeColor = (type) => ({
    A: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    AAAA: 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
    CNAME: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    MX: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
    NS: 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
    TXT: 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400',
    SOA: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
}[type] || 'bg-surface-100 dark:bg-surface-900 text-surface-600');

const lookup = async () => {
    if (!domain.value.trim()) return;
    isLoading.value = true;
    error.value = null;
    result.value = null;
    activeFilter.value = 'All';

    try {
        const res = await axios.post('/api/web-tools/dns-lookup', { domain: domain.value });
        result.value = res.data;
    } catch (e) {
        error.value = e.response?.data?.message || 'DNS lookup failed.';
    } finally {
        isLoading.value = false;
    }
};
</script>
