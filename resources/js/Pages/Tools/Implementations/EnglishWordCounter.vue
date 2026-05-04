<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-surface-50 dark:bg-surface-900 rounded-xl p-4 text-center border border-surface-100 dark:border-surface-700">
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ stats.words }}</p>
                <p class="text-xs font-semibold text-surface-500 uppercase tracking-wider mt-1">Words</p>
            </div>
            <div class="bg-surface-50 dark:bg-surface-900 rounded-xl p-4 text-center border border-surface-100 dark:border-surface-700">
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ stats.characters }}</p>
                <p class="text-xs font-semibold text-surface-500 uppercase tracking-wider mt-1">Characters</p>
            </div>
            <div class="bg-surface-50 dark:bg-surface-900 rounded-xl p-4 text-center border border-surface-100 dark:border-surface-700">
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ stats.sentences }}</p>
                <p class="text-xs font-semibold text-surface-500 uppercase tracking-wider mt-1">Sentences</p>
            </div>
            <div class="bg-surface-50 dark:bg-surface-900 rounded-xl p-4 text-center border border-surface-100 dark:border-surface-700">
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ stats.paragraphs }}</p>
                <p class="text-xs font-semibold text-surface-500 uppercase tracking-wider mt-1">Paragraphs</p>
            </div>
        </div>

        <div class="mb-4 relative">
            <textarea 
                v-model="inputText"
                rows="8"
                class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                placeholder="Type or paste your English text here to analyze..."
            ></textarea>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="text-sm font-medium text-surface-600 dark:text-surface-400 bg-surface-100 dark:bg-surface-700 px-3 py-1.5 rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Reading Time: <span class="text-surface-900 dark:text-white font-bold">{{ stats.readingTime }} min</span>
            </div>
            
            <button @click="clearText" class="px-5 py-2.5 bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-200 font-medium rounded-xl transition-colors text-sm">
                Clear Text
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, reactive } from 'vue';

const inputText = ref('');
const stats = reactive({
    words: 0,
    characters: 0,
    sentences: 0,
    paragraphs: 0,
    readingTime: 0
});

watch(inputText, (newVal) => {
    const text = newVal;
    
    // Characters
    stats.characters = text.length;
    
    // Words
    const wordMatches = text.match(/\b[-?(\w+)?]+\b/gi);
    stats.words = wordMatches ? wordMatches.length : 0;
    
    // Sentences
    const sentenceMatches = text.match(/[\w|\)][.?!]+(\s|$)/g);
    stats.sentences = text.trim() ? (sentenceMatches ? sentenceMatches.length : 1) : 0;
    
    // Paragraphs
    stats.paragraphs = text.trim() ? text.trim().split(/\n\s*\n/).filter(p => p.trim().length > 0).length : 0;
    
    // Reading time (approx 200 words per minute)
    stats.readingTime = Math.ceil(stats.words / 200);
});

const clearText = () => {
    inputText.value = '';
};
</script>
