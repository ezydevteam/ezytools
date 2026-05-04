<template>
    <div class="space-y-6">
        <!-- Input -->
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto">
                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 block">Enter URL to shorten</label>
                <div class="flex gap-3">
                    <input v-model="longUrl" type="url" placeholder="https://example.com/very/long/url/here"
                           class="flex-1 px-4 py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    <button @click="shorten" :disabled="!longUrl.trim() || isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        🔗 Shorten
                    </button>
                </div>
            </div>
        </div>

        <!-- Result -->
        <div v-if="shortUrl" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto space-y-4">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">✅ Shortened URL</h3>
                <div class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                    <a :href="shortUrl" target="_blank" class="text-primary-600 dark:text-primary-400 font-medium text-lg break-all hover:underline flex-1">{{ shortUrl }}</a>
                    <button @click="copy" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                        {{ copied ? '✓ Copied!' : '📋 Copy' }}
                    </button>
                </div>
                <div class="text-sm text-surface-500 dark:text-surface-400">
                    <p><strong>Original URL:</strong> <span class="break-all">{{ longUrl }}</span></p>
                    <p class="mt-1"><strong>Characters saved:</strong> {{ longUrl.length - shortUrl.length }}</p>
                </div>
            </div>
        </div>

        <!-- History -->
        <div v-if="history.length" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                <h3 class="font-semibold text-surface-900 dark:text-white">Recent History</h3>
                <button @click="history = []; saveHistory()" class="text-xs text-surface-400 hover:text-red-500 transition-colors">Clear</button>
            </div>
            <div class="divide-y divide-surface-200 dark:divide-surface-700 max-h-64 overflow-y-auto">
                <div v-for="(item, i) in history" :key="i" class="px-6 py-3 flex items-center gap-4 hover:bg-surface-50 dark:hover:bg-surface-900/50">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary-600 dark:text-primary-400 truncate">{{ item.short }}</p>
                        <p class="text-xs text-surface-400 truncate">{{ item.long }}</p>
                    </div>
                    <button @click="copyText(item.short)" class="text-xs text-surface-400 hover:text-primary-600 transition-colors shrink-0">Copy</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const longUrl = ref('');
const shortUrl = ref('');
const isLoading = ref(false);
const copied = ref(false);
const history = ref([]);

onMounted(() => {
    const saved = localStorage.getItem('url_shortener_history');
    if (saved) history.value = JSON.parse(saved);
});

const shorten = async () => {
    if (!longUrl.value.trim()) return;

    // Ensure URL has protocol
    let url = longUrl.value.trim();
    if (!/^https?:\/\//i.test(url)) url = 'https://' + url;

    isLoading.value = true;
    try {
        // Use is.gd free API (no key needed)
        const res = await fetch(`https://is.gd/create.php?format=json&url=${encodeURIComponent(url)}`);
        const data = await res.json();

        if (data.shorturl) {
            shortUrl.value = data.shorturl;
            history.value.unshift({ long: url, short: data.shorturl });
            if (history.value.length > 10) history.value.pop();
            saveHistory();
        } else {
            shortUrl.value = '';
            alert(data.errormessage || 'Failed to shorten URL.');
        }
    } catch (e) {
        alert('URL shortening service unavailable. Please try again.');
    } finally {
        isLoading.value = false;
    }
};

const copy = () => {
    navigator.clipboard.writeText(shortUrl.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};

const copyText = (text) => {
    navigator.clipboard.writeText(text);
};

const saveHistory = () => {
    localStorage.setItem('url_shortener_history', JSON.stringify(history.value));
};
</script>
