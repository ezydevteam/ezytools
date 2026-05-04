<template>
    <AiToolWrapper :remaining="remainingRequests"><div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <!-- Disclaimer -->
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
            <p class="text-sm text-amber-700 dark:text-amber-400 font-medium font-bangla">⚠️ এই টুলটি শেখার সহায়তার জন্য। উত্তর সরাসরি জমা না দিয়ে নিজে বুঝে লেখার চেষ্টা করুন।</p>
            <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">This tool is for learning assistance. Please understand the answers and write in your own words.</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"><div class="space-y-4">
        <div><label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Assignment Question / Topic</label><textarea v-model="inputText" rows="4" :maxlength="maxLength" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4 resize-y" placeholder="Enter your assignment question or topic..."></textarea><div class="text-right text-[10px] text-surface-400 mt-1">{{ inputText.length }} / {{ maxLength }}</div></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Education Level</label><select v-model="options.level" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="l in levels" :key="l" :value="l">{{ l }}</option></select></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Subject</label><select v-model="options.subject" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="s in subjects" :key="s" :value="s">{{ s }}</option></select></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Assignment Type</label><select v-model="options.type" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="t in types" :key="t" :value="t">{{ t }}</option></select></div>
            <LanguageSelector v-model="options.language" />
        </div>
        <div><label class="block text-xs font-medium text-surface-500 mb-1">Word Limit (optional)</label><input v-model="options.word_limit" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="e.g. 500" /></div>
        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="options.references" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" /><span class="text-sm text-surface-700 dark:text-surface-300">Include references</span></label>
        <button @click="handleGenerate" :disabled="!inputText || isLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm"><svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>{{ isLoading ? 'Preparing...' : 'Get Help' }}</button>
        <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ error }}</div>
    </div><AiOutputCard :content="result" :loading="isLoading" :language="options.language" title="Assignment Help" loadingText="Preparing answer..." :showCopy="true" :showDownload="true" downloadFilename="assignment.txt" /></div></div></AiToolWrapper>
</template>
<script setup>
import { ref, computed } from 'vue';import { usePage } from '@inertiajs/vue3';import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';import AiOutputCard from '@/Components/Ai/AiOutputCard.vue';import LanguageSelector from '@/Components/Ai/LanguageSelector.vue';import { useAiRequest } from '@/Composables/useAiRequest';
const props = defineProps({ tool: Object });const page = usePage();const isPro = computed(() => page.props.auth.user?.is_pro || false);const maxLength = computed(() => { const c = props.tool.ai_config; return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : 1000; });
const levels = ['Class 6-8','SSC (Class 9-10)','HSC (Class 11-12)','Undergraduate','Graduate'];
const subjects = ['Bangla Literature','English','Mathematics','Physics / Chemistry / Biology','History / Geography','Islamic Studies','ICT / Computer Science','Business Studies'];
const types = ['Essay / রচনা','Q&A / প্রশ্নোত্তর','Summary / সারাংশ','Analysis / বিশ্লেষণ','Case Study','Lab Report'];
const inputText = ref('');const options = ref({ level: 'HSC (Class 11-12)', subject: 'English', type: 'Essay / রচনা', language: 'english_us', word_limit: '', references: false });
const { isLoading, error, result, remainingRequests, generate } = useAiRequest();const handleGenerate = () => { generate(props.tool.slug, inputText.value, options.value); };
</script>
