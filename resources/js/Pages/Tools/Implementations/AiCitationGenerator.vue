<template>
    <AiToolWrapper :remaining="remainingRequests"><div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700"><div class="grid grid-cols-1 lg:grid-cols-2 gap-6"><div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Source Type</label><select v-model="options.source_type" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="s in sourceTypes" :key="s" :value="s">{{ s }}</option></select></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Citation Format</label><select v-model="options.format" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="f in formats" :key="f" :value="f">{{ f }}</option></select></div>
        </div>
        <div><label class="block text-xs font-medium text-surface-500 mb-1">Author(s)</label><input v-model="options.author" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="e.g. John Smith, Jane Doe" /></div>
        <div><label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Title</label><input v-model="inputText" type="text" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4" placeholder="Title of the work" /></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Year</label><input v-model="options.year" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="2024" /></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Publisher / Journal</label><input v-model="options.publisher" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="Publisher" /></div>
        </div>
        <div v-if="options.source_type === 'Website'"><label class="block text-xs font-medium text-surface-500 mb-1">URL</label><input v-model="options.url" type="url" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="https://" /></div>
        <div v-if="options.source_type === 'Journal Article'" class="grid grid-cols-3 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Volume</label><input v-model="options.volume" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" /></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Issue</label><input v-model="options.issue" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" /></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Pages</label><input v-model="options.pages" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="1-15" /></div>
        </div>
        <LanguageSelector v-model="options.language" />
        <button @click="handleGenerate" :disabled="!inputText || isLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm"><svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>{{ isLoading ? 'Generating...' : 'Generate Citation' }}</button>
        <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ error }}</div>
    </div><AiOutputCard :content="result" :loading="isLoading" :language="options.language" title="Citation" loadingText="Generating citation..." :showCopy="true" :showDownload="true" downloadFilename="citation.txt" /></div></div></AiToolWrapper>
</template>
<script setup>
import { ref, computed } from 'vue';import { usePage } from '@inertiajs/vue3';import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';import AiOutputCard from '@/Components/Ai/AiOutputCard.vue';import LanguageSelector from '@/Components/Ai/LanguageSelector.vue';import { useAiRequest } from '@/Composables/useAiRequest';
const props = defineProps({ tool: Object });const page = usePage();const isPro = computed(() => page.props.auth.user?.is_pro || false);const maxLength = computed(() => { const c = props.tool.ai_config; return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : 1000; });
const sourceTypes = ['Book','Journal Article','Website','Newspaper','YouTube Video','Research Paper'];
const formats = ['APA 7th Edition','MLA 9th Edition','Chicago 17th Edition','Harvard','Vancouver'];
const inputText = ref('');const options = ref({ source_type: 'Book', format: 'APA 7th Edition', author: '', year: '', publisher: '', url: '', volume: '', issue: '', pages: '', language: 'english_us' });
const { isLoading, error, result, remainingRequests, generate } = useAiRequest();const handleGenerate = () => { generate(props.tool.slug, inputText.value, options.value); };
</script>
