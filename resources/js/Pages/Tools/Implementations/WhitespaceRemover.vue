<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Original Text</label>
                <textarea 
                    v-model="inputText"
                    rows="8"
                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                    placeholder="Paste text with extra spaces here..."
                ></textarea>
                <div class="mt-2 text-xs text-surface-500 dark:text-surface-400">Characters: {{ inputText.length }}</div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Cleaned Text</label>
                <textarea 
                    v-model="outputText"
                    readonly
                    rows="8"
                    class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-100 dark:bg-surface-900/50 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                    placeholder="Result will appear here..."
                ></textarea>
                <div class="mt-2 text-xs text-surface-500 dark:text-surface-400">Characters: {{ outputText.length }} <span v-if="inputText.length > outputText.length" class="text-green-600 dark:text-green-400 font-bold ml-2">({{ inputText.length - outputText.length }} spaces removed)</span></div>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-4 items-center justify-between border-t border-surface-100 dark:border-surface-700 pt-6">
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="removeMultipleSpaces" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Remove Multiple Spaces</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="removeEmptyLines" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Remove Empty Lines</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="trimLines" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Trim Lines</span>
                </label>
            </div>
            
            <div class="flex gap-3">
                <button @click="clearText" class="px-5 py-2.5 bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-200 font-medium rounded-xl transition-colors text-sm">
                    Clear
                </button>
                <button @click="copyResult" :disabled="!outputText" :class="[!outputText ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-700']" class="px-5 py-2.5 bg-primary-600 text-white font-medium rounded-xl transition-colors shadow-md text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                    {{ copied ? 'Copied!' : 'Copy Result' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const inputText = ref('');
const removeMultipleSpaces = ref(true);
const removeEmptyLines = ref(true);
const trimLines = ref(true);
const copied = ref(false);

const outputText = computed(() => {
    if (!inputText.value) return '';
    
    let text = inputText.value;
    
    if (trimLines.value) {
        text = text.split('\n').map(line => line.trim()).join('\n');
    }
    
    if (removeMultipleSpaces.value) {
        text = text.replace(/[ \t]+/g, ' ');
    }
    
    if (removeEmptyLines.value) {
        text = text.split('\n').filter(line => line.trim() !== '').join('\n');
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
