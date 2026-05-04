<template>
    <div class="space-y-3">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">{{ title }}</label>
            <div v-if="content" class="flex items-center gap-2 flex-wrap">
                <span v-if="showWordCount" class="text-xs text-surface-500">{{ wordCount }} words</span>
                <span v-if="showWordCount && showCopy" class="text-surface-300 dark:text-surface-600">|</span>
                <button v-if="showCopy" @click="copyContent" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
                    <svg v-if="copied" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                    {{ copied ? 'Copied' : 'Copy' }}
                </button>
                <button v-if="showDownload" @click="downloadContent" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Download
                </button>
                <button v-if="showRegenerate" @click="$emit('regenerate')" class="text-purple-600 hover:text-purple-700 text-sm font-medium flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Regenerate
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="relative min-h-[300px]">
            <!-- Empty state -->
            <div v-if="!content && !loading" class="absolute inset-0 border-2 border-dashed border-surface-200 dark:border-surface-700 rounded-xl flex items-center justify-center text-surface-400 dark:text-surface-500 bg-surface-50 dark:bg-surface-900/50">
                {{ placeholder || 'Result will appear here' }}
            </div>

            <!-- Loading state -->
            <div v-else-if="loading" class="absolute inset-0 border border-surface-200 dark:border-surface-700 rounded-xl flex flex-col items-center justify-center bg-surface-50 dark:bg-surface-900/50">
                <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                <p class="mt-4 text-surface-500 font-medium animate-pulse">{{ loadingText || 'Generating...' }}</p>
            </div>

            <!-- Result -->
            <div v-else class="block w-full h-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white p-4 whitespace-pre-wrap text-base overflow-auto min-h-[300px]"
                 :dir="isRtl ? 'rtl' : 'ltr'"
                 :class="isRtl ? 'text-right' : 'text-left'">
                <div v-if="renderHtml" v-html="content"></div>
                <template v-else>{{ content }}</template>
            </div>
        </div>

        <!-- Send to Humanizer -->
        <div v-if="content && showSendToHumanizer" class="flex justify-end">
            <button @click="$emit('send-to-humanizer', content)" class="inline-flex items-center gap-1 text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                Send to Humanizer
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    content: { type: String, default: '' },
    title: { type: String, default: 'Output' },
    placeholder: { type: String, default: '' },
    loadingText: { type: String, default: '' },
    loading: { type: Boolean, default: false },
    language: { type: String, default: 'english_us' },
    renderHtml: { type: Boolean, default: false },
    showCopy: { type: Boolean, default: true },
    showDownload: { type: Boolean, default: false },
    showRegenerate: { type: Boolean, default: false },
    showSendToHumanizer: { type: Boolean, default: false },
    showWordCount: { type: Boolean, default: true },
    downloadFilename: { type: String, default: 'output.txt' },
});

defineEmits(['regenerate', 'send-to-humanizer']);

const copied = ref(false);

const isRtl = computed(() => ['urdu', 'arabic'].includes(props.language));

const wordCount = computed(() => {
    return props.content?.trim() ? props.content.trim().split(/\s+/).length : 0;
});

const copyContent = () => {
    if (!props.content) return;
    navigator.clipboard.writeText(props.content);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};

const downloadContent = () => {
    if (!props.content) return;
    const blob = new Blob([props.content], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = props.downloadFilename;
    a.click();
    URL.revokeObjectURL(url);
};
</script>
