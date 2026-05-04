<template>
<div class="space-y-6">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">YouTube Video URL</label>
            <div class="flex gap-2">
                <input type="text" v-model="url" placeholder="https://www.youtube.com/watch?v=..." class="flex-1 rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @keyup.enter="fetchInfo" />
                <button @click="fetchInfo" :disabled="loading || !url.trim()" class="px-5 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-sm text-sm flex items-center gap-2 shrink-0">
                    <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    🔍 Get Info
                </button>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ error }}</div>

        <!-- Results -->
        <div v-if="info" class="space-y-4">
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <!-- Thumbnail -->
                <img :src="info.thumbnail" class="w-full max-h-[400px] object-cover" :alt="info.title" @error="$event.target.src=info.thumbnails?.[2] || ''" />

                <div class="p-5 space-y-4">
                    <!-- Title & Author -->
                    <h3 class="text-xl font-bold text-surface-900 dark:text-white leading-snug">{{ info.title }}</h3>
                    <div class="flex flex-wrap gap-4 text-sm text-surface-500">
                        <span class="flex items-center gap-1">👤 <strong class="text-surface-700 dark:text-surface-300">{{ info.author }}</strong></span>
                        <span v-if="info.views" class="flex items-center gap-1">👁 {{ formatNumber(info.views) }} views</span>
                        <span v-if="info.likes" class="flex items-center gap-1">👍 {{ formatNumber(info.likes) }} likes</span>
                        <span v-if="info.published" class="flex items-center gap-1">📅 {{ info.published }}</span>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="p-3 bg-surface-50 dark:bg-surface-900 rounded-xl text-center">
                            <p class="text-xs text-surface-500">Duration</p>
                            <p class="text-sm font-bold text-surface-900 dark:text-white">{{ info.duration || 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-surface-50 dark:bg-surface-900 rounded-xl text-center">
                            <p class="text-xs text-surface-500">Category</p>
                            <p class="text-sm font-bold text-surface-900 dark:text-white">{{ info.category || 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-surface-50 dark:bg-surface-900 rounded-xl text-center">
                            <p class="text-xs text-surface-500">Comments</p>
                            <p class="text-sm font-bold text-surface-900 dark:text-white">{{ info.comments ? formatNumber(info.comments) : 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-surface-50 dark:bg-surface-900 rounded-xl text-center">
                            <p class="text-xs text-surface-500">Video ID</p>
                            <p class="text-sm font-bold text-surface-900 dark:text-white font-mono">{{ info.videoId }}</p>
                        </div>
                    </div>

                    <!-- Live / Private badges -->
                    <div v-if="info.isLive || info.isPrivate" class="flex gap-2">
                        <span v-if="info.isLive" class="px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-full text-xs font-bold">🔴 LIVE</span>
                        <span v-if="info.isPrivate" class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full text-xs font-bold">🔒 PRIVATE</span>
                    </div>

                    <!-- Description -->
                    <div v-if="info.description">
                        <button @click="showDesc=!showDesc" class="text-sm text-primary-600 hover:text-primary-700 font-medium">{{ showDesc ? 'Hide Description ▲' : 'Show Full Description ▼' }}</button>
                        <div v-if="showDesc" class="mt-2 text-sm text-surface-600 dark:text-surface-400 whitespace-pre-wrap max-h-60 overflow-y-auto bg-surface-50 dark:bg-surface-900 p-4 rounded-xl border border-surface-200 dark:border-surface-700">{{ info.description }}</div>
                    </div>

                    <!-- Tags -->
                    <div v-if="info.tags?.length">
                        <p class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Tags ({{ info.tags.length }})</p>
                        <div class="flex flex-wrap gap-1.5">
                            <span v-for="tag in showAllTags ? info.tags : info.tags.slice(0,20)" :key="tag" class="px-2.5 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 text-xs rounded-full border border-primary-200 dark:border-primary-800">#{{ tag }}</span>
                            <button v-if="info.tags.length > 20 && !showAllTags" @click="showAllTags=true" class="px-2.5 py-1 bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400 text-xs rounded-full">+{{ info.tags.length - 20 }} more</button>
                        </div>
                    </div>

                    <!-- Thumbnails -->
                    <div v-if="info.thumbnails?.length">
                        <p class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Thumbnails</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <a v-for="(t,i) in info.thumbnails" :key="i" :href="t" target="_blank" download class="group rounded-lg overflow-hidden border border-surface-200 dark:border-surface-700 hover:ring-2 ring-primary-500 transition-all relative">
                                <img :src="t" class="w-full h-auto" :alt="`Thumbnail ${i+1}`" @error="$event.target.parentElement.style.display='none'" />
                                <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">⬇ Save</span>
                            </a>
                        </div>
                    </div>

                    <!-- Channel Info -->
                    <div v-if="info.channelId" class="pt-3 border-t border-surface-200 dark:border-surface-700">
                        <a :href="`https://www.youtube.com/channel/${info.channelId}`" target="_blank" rel="noopener" class="text-sm text-primary-600 hover:text-primary-700 font-medium">🔗 View Channel on YouTube</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const url = ref('');
const loading = ref(false);
const error = ref('');
const info = ref(null);
const showDesc = ref(false);
const showAllTags = ref(false);

const formatNumber = (n) => {
    if (!n && n !== 0) return '0';
    if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
    if (n >= 1000) return (n / 1000).toFixed(1) + 'K';
    return n.toLocaleString();
};

const fetchInfo = async () => {
    if (!url.value.trim()) return;
    error.value = '';
    info.value = null;
    showDesc.value = false;
    showAllTags.value = false;
    loading.value = true;

    try {
        const response = await axios.post('/api/youtube/info', { url: url.value });
        info.value = response.data;
    } catch (e) {
        error.value = e.response?.data?.error || e.response?.data?.message || 'Failed to fetch video info. Please check the URL and try again.';
    }

    loading.value = false;
};
</script>
