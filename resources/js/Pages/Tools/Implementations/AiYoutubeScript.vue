<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Video Topic / Title</label><textarea v-model="inputText" rows="3" :maxlength="maxLength" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4 resize-y" placeholder="Enter your video topic..."></textarea><div class="text-right text-[10px] text-surface-400 mt-1">{{ inputText.length }} / {{ maxLength }}</div></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-surface-500 mb-1">Video Duration</label><select v-model="options.duration" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option value="Short (3-5 min)">Short (3-5 min)</option><option value="Medium (8-12 min)">Medium (8-12 min)</option><option value="Long (15-20 min)">Long (15-20 min)</option></select></div>
                        <div><label class="block text-xs font-medium text-surface-500 mb-1">Channel Niche</label><select v-model="options.niche" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="n in niches" :key="n" :value="n">{{ n }}</option></select></div>
                        <div><label class="block text-xs font-medium text-surface-500 mb-1">Style</label><select v-model="options.style" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="s in styles" :key="s" :value="s">{{ s }}</option></select></div>
                        <LanguageSelector v-model="options.language" />
                    </div>
                    <div><label class="block text-xs font-medium text-surface-500 mb-1">Key Points (optional)</label><textarea v-model="options.key_points" rows="2" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-3 resize-none" placeholder="Points to cover..."></textarea></div>
                    <div><label class="block text-xs font-medium text-surface-500 mb-1">Channel Name (optional)</label><input v-model="options.channel_name" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="Your channel name" /></div>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="options.broll" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" /><span class="text-sm text-surface-700 dark:text-surface-300">Include B-Roll Suggestions</span></label>
                    <button @click="handleGenerate" :disabled="!inputText || isLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm"><svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>{{ isLoading ? 'Writing Script...' : 'Generate Script' }}</button>
                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ error }}</div>
                </div>
                <AiOutputCard :content="result" :loading="isLoading" :language="options.language" title="YouTube Script" loadingText="Writing script..." :showCopy="true" :showDownload="true" downloadFilename="youtube-script.txt" />
            </div>
        </div>
    </AiToolWrapper>
</template>
<script setup>
import { ref, computed } from 'vue';import { usePage } from '@inertiajs/vue3';import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';import AiOutputCard from '@/Components/Ai/AiOutputCard.vue';import LanguageSelector from '@/Components/Ai/LanguageSelector.vue';import { useAiRequest } from '@/Composables/useAiRequest';
const props = defineProps({ tool: Object });const page = usePage();const isPro = computed(() => page.props.auth.user?.is_pro || false);const maxLength = computed(() => { const c = props.tool.ai_config; return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : 1000; });
const niches = ['Education / Tutorial','Entertainment / Vlog','Tech Review','Business / Finance','Cooking / Lifestyle','Islamic / Religious','News / Commentary'];
const styles = ['Energetic & Engaging','Calm & Informative','Storytelling','Interview Style'];
const inputText = ref('');const options = ref({ duration: 'Medium (8-12 min)', niche: 'Education / Tutorial', style: 'Energetic & Engaging', language: 'english_us', key_points: '', channel_name: '', broll: false });
const { isLoading, error, result, remainingRequests, generate } = useAiRequest();const handleGenerate = () => { generate(props.tool.slug, inputText.value, options.value); };
</script>
