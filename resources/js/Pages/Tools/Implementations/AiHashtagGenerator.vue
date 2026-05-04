<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Describe Your Post / Paste Caption</label>
                        <textarea v-model="inputText" rows="4" :maxlength="maxLength" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4 resize-y" placeholder="Describe your post or paste your caption..."></textarea>
                        <div class="text-right text-[10px] text-surface-400 mt-1">{{ inputText.length }} / {{ maxLength }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Platform</label>
                            <select v-model="options.platform" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option v-for="p in platforms" :key="p" :value="p">{{ p }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Niche / Industry</label>
                            <select v-model="options.niche" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option v-for="n in niches" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Number of Hashtags</label>
                            <select v-model="options.count" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="10">10</option><option value="20">20</option><option value="30">30</option>
                            </select>
                        </div>
                        <LanguageSelector v-model="options.language" />
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" v-model="options.include_bangla" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" />
                        <span class="text-sm text-surface-700 dark:text-surface-300">Include Bangla hashtags</span>
                    </label>
                    <button @click="handleGenerate" :disabled="!inputText || isLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ isLoading ? 'Generating...' : 'Generate Hashtags' }}
                    </button>
                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ error }}</div>
                </div>
                <AiOutputCard :content="result" :loading="isLoading" :language="options.language" title="Generated Hashtags" loadingText="Generating hashtags..." :showCopy="true" />
            </div>
        </div>
    </AiToolWrapper>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';
import AiOutputCard from '@/Components/Ai/AiOutputCard.vue';
import LanguageSelector from '@/Components/Ai/LanguageSelector.vue';
import { useAiRequest } from '@/Composables/useAiRequest';

const props = defineProps({ tool: Object });
const page = usePage();
const isPro = computed(() => page.props.auth.user?.is_pro || false);
const maxLength = computed(() => { const c = props.tool.ai_config; return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : 1000; });

const platforms = ['Instagram', 'Facebook', 'LinkedIn', 'YouTube', 'Twitter/X', 'TikTok'];
const niches = ['Fashion & Beauty', 'Food & Restaurant', 'Business & Entrepreneurship', 'Education', 'Travel', 'Technology', 'Health & Fitness', 'Real Estate', 'Islamic Content', 'Entertainment'];

const inputText = ref('');
const options = ref({ platform: 'Instagram', niche: 'Technology', count: '20', language: 'english_us', include_bangla: false });
const { isLoading, error, result, remainingRequests, generate } = useAiRequest();

const handleGenerate = () => { generate(props.tool.slug, inputText.value, options.value); };
</script>
