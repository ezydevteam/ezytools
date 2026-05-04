<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Input Area -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-2">Open Graph Information</h3>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Title</label>
                    <input type="text" v-model="title" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. 10 Best Coffee Shops in Dhaka">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Description</label>
                    <textarea v-model="description" rows="3" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 resize-none" placeholder="e.g. A comprehensive guide to finding the best coffee..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Image URL</label>
                    <input type="text" v-model="image" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="https://example.com/image.jpg">
                    <p class="text-xs text-surface-500 mt-1">Recommended size: 1200x630 pixels</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Page URL</label>
                    <input type="text" v-model="url" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="https://example.com/blog/coffee-shops">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Type</label>
                        <select v-model="type" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="website">Website</option>
                            <option value="article">Article</option>
                            <option value="profile">Profile</option>
                            <option value="video.movie">Video</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Site Name</label>
                        <input type="text" v-model="siteName" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. My Coffee Blog">
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-md font-bold text-surface-900 dark:text-white mb-2 pt-4 border-t border-surface-200 dark:border-surface-700">Twitter Card specific</h3>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Card Type</label>
                        <select v-model="twitterCard" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="summary_large_image">Summary Large Image (Recommended)</option>
                            <option value="summary">Summary (Small Image)</option>
                            <option value="app">App</option>
                            <option value="player">Player</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Generated Open Graph Tags</label>
                    <button @click="copyToClipboard" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600" :class="copied ? 'text-green-500' : 'text-surface-700 dark:text-surface-300'">
                        <svg v-if="copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>
                <div class="flex-1 w-full p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-[#1e1e1e] text-[#d4d4d4] overflow-auto">
                    <pre class="font-mono text-sm whitespace-pre overflow-x-auto"><code>{{ generatedTags }}</code></pre>
                </div>
                
                <!-- Preview -->
                <div class="mt-4 border border-surface-200 dark:border-surface-700 rounded-xl overflow-hidden bg-white dark:bg-surface-800 shadow-sm" v-if="title || image">
                    <div class="h-48 bg-surface-100 dark:bg-surface-900 flex items-center justify-center overflow-hidden border-b border-surface-200 dark:border-surface-700 relative">
                        <img v-if="image" :src="image" class="w-full h-full object-cover" @error="imageError = true" v-show="!imageError" alt="Preview">
                        <span v-if="!image || imageError" class="text-surface-400">Image Preview</span>
                    </div>
                    <div class="p-4 bg-surface-50 dark:bg-surface-900">
                        <div class="text-xs text-surface-500 uppercase tracking-wide mb-1">{{ domain }}</div>
                        <h4 class="font-bold text-surface-900 dark:text-white leading-tight mb-1 truncate">{{ title || 'Your Title Here' }}</h4>
                        <p class="text-sm text-surface-600 dark:text-surface-400 line-clamp-2">{{ description || 'Your description will appear here...' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const title = ref('');
const description = ref('');
const image = ref('');
const url = ref('');
const type = ref('website');
const siteName = ref('');
const twitterCard = ref('summary_large_image');
const copied = ref(false);
const imageError = ref(false);

const domain = computed(() => {
    if (!url.value) return 'example.com';
    try {
        return new URL(url.value).hostname.replace('www.', '');
    } catch(e) {
        return url.value.split('/')[0];
    }
});

const generatedTags = computed(() => {
    let tags = [];
    
    // Open Graph
    tags.push('<!-- Open Graph / Facebook -->');
    tags.push(`<meta property="og:type" content="${type.value}">`);
    if (url.value) tags.push(`<meta property="og:url" content="${url.value}">`);
    if (title.value) tags.push(`<meta property="og:title" content="${title.value}">`);
    if (description.value) tags.push(`<meta property="og:description" content="${description.value}">`);
    if (image.value) tags.push(`<meta property="og:image" content="${image.value}">`);
    if (siteName.value) tags.push(`<meta property="og:site_name" content="${siteName.value}">`);
    
    tags.push('');
    // Twitter
    tags.push('<!-- Twitter -->');
    tags.push(`<meta property="twitter:card" content="${twitterCard.value}">`);
    if (url.value) tags.push(`<meta property="twitter:url" content="${url.value}">`);
    if (title.value) tags.push(`<meta property="twitter:title" content="${title.value}">`);
    if (description.value) tags.push(`<meta property="twitter:description" content="${description.value}">`);
    if (image.value) tags.push(`<meta property="twitter:image" content="${image.value}">`);

    return tags.join('\n');
});

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(generatedTags.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};
</script>
