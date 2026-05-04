<template>
    <AiToolWrapper :remaining="remainingRequests"><div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700"><div class="grid grid-cols-1 lg:grid-cols-2 gap-6"><div class="space-y-4">
        <div><label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Product / Service Name</label><input v-model="inputText" type="text" :maxlength="maxLength" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4" placeholder="Enter product or service name..." /></div>
        <div><label class="block text-xs font-medium text-surface-500 mb-1">Product Description & Key Benefits</label><textarea v-model="options.description" rows="3" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-3 resize-y" placeholder="Describe your product and its key benefits..."></textarea></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Platform</label><select v-model="options.platform" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="p in platforms" :key="p" :value="p">{{ p }}</option></select></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Objective</label><select v-model="options.objective" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="o in objectives" :key="o" :value="o">{{ o }}</option></select></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Tone</label><select v-model="options.tone" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="t in tones" :key="t" :value="t">{{ t }}</option></select></div>
            <LanguageSelector v-model="options.language" />
        </div>
        <div><label class="block text-xs font-medium text-surface-500 mb-1">Target Audience</label><input v-model="options.audience" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="e.g. Young professionals aged 25-35" /></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">CTA Preference</label><select v-model="options.cta" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="c in ctas" :key="c" :value="c">{{ c }}</option></select></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Variations</label><select v-model="options.count" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option value="1">1</option><option value="3">3</option><option value="5">5</option></select></div>
        </div>
        <button @click="handleGenerate" :disabled="!inputText || isLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm"><svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>{{ isLoading ? 'Generating...' : 'Generate Ad Copy' }}</button>
        <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ error }}</div>
    </div>
    <AiOutputCard :content="result" :loading="isLoading" :language="options.language" title="Ad Copy" loadingText="Creating ad copy..." :showCopy="true" :showDownload="true" downloadFilename="ad-copy.txt" /></div></div></AiToolWrapper>
</template>
<script setup>
import { ref, computed } from 'vue';import { usePage } from '@inertiajs/vue3';import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';import AiOutputCard from '@/Components/Ai/AiOutputCard.vue';import LanguageSelector from '@/Components/Ai/LanguageSelector.vue';import { useAiRequest } from '@/Composables/useAiRequest';
const props = defineProps({ tool: Object });const page = usePage();const isPro = computed(() => page.props.auth.user?.is_pro || false);const maxLength = computed(() => { const c = props.tool.ai_config; return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : 1000; });
const platforms = ['Facebook & Instagram Ads','Google Search Ads','Google Display Ads','YouTube Ads','LinkedIn Ads'];
const objectives = ['Awareness','Traffic','Leads','Sales','App Installs'];
const tones = ['Urgent','Emotional','Logical','Humorous','Inspirational'];
const ctas = ['Shop Now','Learn More','Sign Up','Contact Us','Download','Custom'];
const inputText = ref('');const options = ref({ description: '', platform: 'Facebook & Instagram Ads', objective: 'Sales', tone: 'Emotional', language: 'english_us', audience: '', cta: 'Shop Now', count: '3' });
const { isLoading, error, result, remainingRequests, generate } = useAiRequest();const handleGenerate = () => { generate(props.tool.slug, inputText.value, options.value); };
</script>
