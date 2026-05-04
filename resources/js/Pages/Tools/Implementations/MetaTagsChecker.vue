<template>
    <div class="space-y-6">
        <!-- Input -->
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto">
                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 block">Enter a URL to check</label>
                <div class="flex gap-3">
                    <input v-model="url" type="url" placeholder="https://example.com" @keyup.enter="check"
                           class="flex-1 px-4 py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    <button @click="check" :disabled="!url.trim() || isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        🔍 Check
                    </button>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-400 font-medium">{{ error }}</div>

        <!-- Result -->
        <div v-if="result" class="space-y-4">
            <!-- Score overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-5 text-center">
                    <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Status</p>
                    <p class="text-2xl font-black" :class="result.status_code === 200 ? 'text-green-500' : 'text-amber-500'">{{ result.status_code }}</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-5 text-center">
                    <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Title Length</p>
                    <p class="text-2xl font-black" :class="titleLen >= 30 && titleLen <= 65 ? 'text-green-500' : 'text-amber-500'">{{ titleLen }}</p>
                    <p class="text-xs text-surface-400 mt-0.5">Ideal: 30–65</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-5 text-center">
                    <p class="text-xs font-semibold text-surface-400 uppercase mb-1">Description Length</p>
                    <p class="text-2xl font-black" :class="descLen >= 70 && descLen <= 160 ? 'text-green-500' : 'text-amber-500'">{{ descLen }}</p>
                    <p class="text-xs text-surface-400 mt-0.5">Ideal: 70–160</p>
                </div>
            </div>

            <!-- Tags table -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/50">
                    <h3 class="font-bold text-surface-900 dark:text-white">Meta Tags Found <span class="text-xs text-surface-400 font-normal">({{ Object.keys(result.tags).length }} tags)</span></h3>
                </div>
                <div class="divide-y divide-surface-200 dark:divide-surface-700">
                    <div v-for="(value, key) in sortedTags" :key="key" class="px-6 py-3 flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-4 hover:bg-surface-50 dark:hover:bg-surface-900/50">
                        <span class="text-xs font-bold uppercase tracking-wider sm:w-48 shrink-0" :class="tagColor(key)">{{ key }}</span>
                        <span class="text-sm text-surface-800 dark:text-surface-200 break-all">{{ value }}</span>
                    </div>
                </div>
                <div v-if="!Object.keys(result.tags).length" class="p-8 text-center text-surface-400">No meta tags found.</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const url = ref('');
const isLoading = ref(false);
const error = ref(null);
const result = ref(null);

const titleLen = computed(() => (result.value?.tags?.title || '').length);
const descLen = computed(() => (result.value?.tags?.description || '').length);

const sortedTags = computed(() => {
    if (!result.value?.tags) return {};
    const priority = ['title', 'description', 'canonical', 'og:title', 'og:description', 'og:image', 'twitter:card', 'twitter:title'];
    const sorted = {};
    for (const key of priority) {
        if (result.value.tags[key]) sorted[key] = result.value.tags[key];
    }
    for (const [key, value] of Object.entries(result.value.tags)) {
        if (!sorted[key]) sorted[key] = value;
    }
    return sorted;
});

const tagColor = (key) => {
    if (key.startsWith('og:')) return 'text-blue-500';
    if (key.startsWith('twitter:')) return 'text-sky-500';
    if (['title', 'description'].includes(key)) return 'text-green-600 dark:text-green-400';
    if (key === 'canonical') return 'text-purple-500';
    return 'text-surface-500';
};

const check = async () => {
    if (!url.value.trim()) return;
    let u = url.value.trim();
    if (!/^https?:\/\//i.test(u)) u = 'https://' + u;

    isLoading.value = true;
    error.value = null;
    result.value = null;

    try {
        const res = await axios.post('/api/web-tools/meta-tags-checker', { url: u });
        result.value = res.data;
    } catch (e) {
        error.value = e.response?.data?.message || 'Failed to fetch meta tags.';
    } finally {
        isLoading.value = false;
    }
};
</script>
