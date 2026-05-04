<template>
    <div class="space-y-6">
        <!-- Input -->
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto">
                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 block">Enter URL to check Google Cache</label>
                <div class="flex gap-3">
                    <input v-model="url" type="url" placeholder="https://example.com" @keyup.enter="check"
                           class="flex-1 px-4 py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    <button @click="check" :disabled="!url.trim() || isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        🔍 Check Cache
                    </button>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-400 font-medium">{{ error }}</div>

        <!-- Result -->
        <div v-if="result" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8">
            <div class="max-w-xl mx-auto text-center">
                <div class="w-24 h-24 mx-auto mb-5 rounded-full flex items-center justify-center text-5xl"
                     :class="result.is_cached ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
                    {{ result.is_cached ? '✅' : '❌' }}
                </div>
                <h3 class="text-2xl font-black mb-2" :class="result.is_cached ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                    {{ result.is_cached ? 'Page is Cached!' : 'Not Cached' }}
                </h3>
                <p class="text-surface-500 mb-6 text-sm break-all">{{ result.url }}</p>

                <div v-if="result.is_cached" class="space-y-3">
                    <div v-if="result.cache_date" class="bg-surface-50 dark:bg-surface-900 rounded-xl p-4">
                        <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Cached On</p>
                        <p class="text-sm font-medium text-surface-900 dark:text-white">{{ result.cache_date }}</p>
                    </div>
                    <a :href="result.cache_url" target="_blank"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors">
                        🔗 View Cached Version
                    </a>
                </div>
                <div v-else class="text-sm text-surface-500">
                    <p>This page has not been cached by Google yet, or the cache has expired.</p>
                    <p class="mt-2">Try submitting the page to <a href="https://search.google.com/search-console" target="_blank" class="text-primary-500 hover:underline">Google Search Console</a>.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const url = ref('');
const isLoading = ref(false);
const error = ref(null);
const result = ref(null);

const check = async () => {
    if (!url.value.trim()) return;
    let u = url.value.trim();
    if (!/^https?:\/\//i.test(u)) u = 'https://' + u;

    isLoading.value = true;
    error.value = null;
    result.value = null;

    try {
        const res = await axios.post('/api/web-tools/google-cache-checker', { url: u });
        result.value = res.data;
    } catch (e) {
        error.value = e.response?.data?.message || 'Cache check failed.';
    } finally {
        isLoading.value = false;
    }
};
</script>
