<template>
    <div>
        <div class="mb-4">
            <label for="text-input" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                Enter your text below (Bangla or English)
            </label>
            <textarea
                id="text-input"
                v-model="text"
                rows="8"
                class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-lg dark:bg-surface-800 dark:border-surface-600 dark:text-white transition-colors"
                placeholder="এখানে আপনার টেক্সট লিখুন..."
            ></textarea>
        </div>

        <div class="flex gap-2 mb-6">
            <button @click="text = ''" class="px-4 py-2 bg-surface-200 hover:bg-surface-300 dark:bg-surface-700 dark:hover:bg-surface-600 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-medium transition-colors">
                Clear Text
            </button>
            <button @click="copyText" class="px-4 py-2 bg-surface-200 hover:bg-surface-300 dark:bg-surface-700 dark:hover:bg-surface-600 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-medium transition-colors">
                Copy
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-surface-800 p-4 rounded-xl border border-surface-200 dark:border-surface-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ words }}</div>
                <div class="text-sm text-surface-500 dark:text-surface-400 mt-1 uppercase tracking-wide font-semibold">Words / শব্দ</div>
            </div>
            <div class="bg-white dark:bg-surface-800 p-4 rounded-xl border border-surface-200 dark:border-surface-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ characters }}</div>
                <div class="text-sm text-surface-500 dark:text-surface-400 mt-1 uppercase tracking-wide font-semibold">Characters / অক্ষর</div>
            </div>
            <div class="bg-white dark:bg-surface-800 p-4 rounded-xl border border-surface-200 dark:border-surface-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ charactersNoSpaces }}</div>
                <div class="text-sm text-surface-500 dark:text-surface-400 mt-1 uppercase tracking-wide font-semibold">Chars (No Spaces)</div>
            </div>
            <div class="bg-white dark:bg-surface-800 p-4 rounded-xl border border-surface-200 dark:border-surface-700 text-center shadow-sm">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ sentences }}</div>
                <div class="text-sm text-surface-500 dark:text-surface-400 mt-1 uppercase tracking-wide font-semibold">Sentences / বাক্য</div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    tool: Object,
    settings: Object,
});

const text = ref('');

const words = computed(() => {
    const trimmed = text.value.trim();
    if (!trimmed) return 0;
    return trimmed.split(/\s+/).length;
});

const characters = computed(() => text.value.length);

const charactersNoSpaces = computed(() => text.value.replace(/\s/g, '').length);

const sentences = computed(() => {
    const trimmed = text.value.trim();
    if (!trimmed) return 0;
    // Match based on sentence ending punctuation.  Add Dari (।) for Bangla.
    return (trimmed.match(/[.!?।]+/g) || []).length || (words.value > 0 ? 1 : 0);
});

const copyText = () => {
    if (!text.value) return;
    navigator.clipboard.writeText(text.value).then(() => {
        toast.success("Text copied to clipboard!");
    });
};
</script>
