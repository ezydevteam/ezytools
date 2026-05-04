<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Original Text (e.g. Title or Heading)</label>
                <input 
                    type="text"
                    v-model="inputText"
                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors"
                    placeholder="Enter text to generate a URL-friendly slug..."
                >
            </div>
            
            <div class="p-6 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Generated Slug</label>
                <div class="flex items-center gap-2">
                    <div class="text-surface-400 dark:text-surface-500 font-mono select-none hidden sm:block">https://example.com/</div>
                    <input 
                        type="text"
                        v-model="outputText"
                        readonly
                        class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-primary-600 dark:text-primary-400 focus:ring-primary-500 focus:border-primary-500 transition-colors font-mono font-semibold"
                        placeholder="your-url-friendly-slug-will-appear-here"
                    >
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-4 items-center justify-between border-t border-surface-100 dark:border-surface-700 pt-6">
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="lowercase" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Force Lowercase</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <select v-model="separator" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="-">Hyphen (-)</option>
                        <option value="_">Underscore (_)</option>
                    </select>
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Separator</span>
                </label>
            </div>
            
            <div class="flex gap-3">
                <button @click="clearText" class="px-5 py-2.5 bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-200 font-medium rounded-xl transition-colors text-sm">
                    Clear
                </button>
                <button @click="copyResult" :disabled="!outputText" :class="[!outputText ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-700']" class="px-5 py-2.5 bg-primary-600 text-white font-medium rounded-xl transition-colors shadow-md text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                    {{ copied ? 'Copied!' : 'Copy Slug' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const inputText = ref('');
const lowercase = ref(true);
const separator = ref('-');
const copied = ref(false);

const outputText = computed(() => {
    if (!inputText.value) return '';
    
    let text = inputText.value;
    
    // Replace non-alphanumeric characters (excluding spaces) with empty strings
    // In Bengali we want to keep unicode characters. Let's make it a general URL slugifier
    text = text.replace(/[^\p{L}\p{N}\s-]/gu, '');
    
    // Replace spaces with separator
    text = text.replace(/[\s-]+/g, separator.value);
    
    // Trim separator from ends
    text = text.replace(new RegExp(`^${separator.value}+|${separator.value}+$`, 'g'), '');
    
    if (lowercase.value) {
        text = text.toLowerCase();
    }
    
    return text;
});

const clearText = () => {
    inputText.value = '';
};

const copyResult = () => {
    if (!outputText.value) return;
    navigator.clipboard.writeText(outputText.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};
</script>
