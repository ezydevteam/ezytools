<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Input Area -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-2">Page Information</h3>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Page Title (Required)</label>
                    <input type="text" v-model="title" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. Best Coffee Shop in Dhaka">
                    <p class="text-xs mt-1" :class="title.length > 60 ? 'text-red-500' : 'text-surface-500'">{{ title.length }} / 60 characters recommended</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Description (Required)</label>
                    <textarea v-model="description" rows="3" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 resize-none" placeholder="e.g. Visit our coffee shop for the best artisanal blends..."></textarea>
                    <p class="text-xs mt-1" :class="description.length > 160 ? 'text-red-500' : 'text-surface-500'">{{ description.length }} / 160 characters recommended</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Keywords (Comma separated)</label>
                    <input type="text" v-model="keywords" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. coffee, dhaka, cafe, espresso">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Author</label>
                        <input type="text" v-model="author" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Viewport</label>
                        <select v-model="viewport" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="width=device-width, initial-scale=1.0">Responsive (Default)</option>
                            <option value="">No Viewport Tag</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Robots Indexing</label>
                    <select v-model="robots" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="index, follow">Index, Follow (Recommended)</option>
                        <option value="index, nofollow">Index, No Follow</option>
                        <option value="noindex, follow">No Index, Follow</option>
                        <option value="noindex, nofollow">No Index, No Follow</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Language</label>
                    <select v-model="language" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="English">English</option>
                        <option value="Bengali">Bengali</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                        <option value="Arabic">Arabic</option>
                    </select>
                </div>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Generated HTML Meta Tags</label>
                    <button @click="copyToClipboard" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600" :class="copied ? 'text-green-500' : 'text-surface-700 dark:text-surface-300'">
                        <svg v-if="copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>
                <div class="flex-1 w-full p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-[#1e1e1e] text-[#d4d4d4] overflow-auto">
                    <pre class="font-mono text-sm whitespace-pre overflow-x-auto"><code>{{ generatedTags }}</code></pre>
                </div>
                <p class="text-xs text-surface-500 mt-2 text-center">Paste these tags inside the <code>&lt;head&gt;</code> section of your HTML document.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const title = ref('');
const description = ref('');
const keywords = ref('');
const author = ref('');
const robots = ref('index, follow');
const viewport = ref('width=device-width, initial-scale=1.0');
const language = ref('English');
const copied = ref(false);

const generatedTags = computed(() => {
    let tags = [];
    
    // Core HTML
    tags.push('<!-- HTML Meta Tags -->');
    if (title.value) {
        tags.push(`<title>${title.value}</title>`);
    }
    if (description.value) {
        tags.push(`<meta name="description" content="${description.value}">`);
    }
    if (keywords.value) {
        tags.push(`<meta name="keywords" content="${keywords.value}">`);
    }
    if (author.value) {
        tags.push(`<meta name="author" content="${author.value}">`);
    }
    if (viewport.value) {
        tags.push(`<meta name="viewport" content="${viewport.value}">`);
    }
    if (robots.value) {
        tags.push(`<meta name="robots" content="${robots.value}">`);
    }
    if (language.value) {
        tags.push(`<meta name="language" content="${language.value}">`);
    }
    tags.push('<meta charset="UTF-8">');

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
