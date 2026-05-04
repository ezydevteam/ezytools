<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Input Area -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">What's the post about?</label>
                        <div class="relative">
                            <textarea 
                                v-model="inputText"
                                rows="4"
                                :maxlength="maxLength"
                                class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-y text-base p-4"
                                placeholder="What is your post about? (e.g. A new product launch, a travel experience, a daily tip)"
                            ></textarea>
                            
                            <div class="absolute bottom-3 right-3 text-[10px] font-mono text-surface-400 bg-surface-100/50 dark:bg-surface-800/50 px-1.5 py-0.5 rounded">
                                {{ inputText.length }} / {{ maxLength }}
                            </div>
                            
                            <button v-if="inputText" @click="inputText = ''" class="absolute top-3 right-3 text-surface-400 hover:text-surface-600 dark:hover:text-surface-200 p-1 bg-surface-100 dark:bg-surface-800 rounded-lg transition-colors" title="Clear text">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Platform</label>
                            <select v-model="options.platform" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="Twitter/X">Twitter / X</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Instagram">Instagram Caption</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Tone</label>
                            <select v-model="options.tone" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="Engaging">Engaging & Casual</option>
                                <option value="Professional">Professional</option>
                                <option value="Humorous">Humorous</option>
                                <option value="Inspirational">Inspirational</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Length</label>
                            <select v-model="options.length" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="Short">Short (1-2 sentences)</option>
                                <option value="Medium">Medium (1 paragraph)</option>
                                <option value="Detailed">Detailed (Long post)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Language</label>
                            <select v-model="options.language" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="English (US)">English (US)</option>
                                <option value="English (British)">English (British)</option>
                                <option value="Bengali">Bangla (বাংলা)</option>
                                <option value="Hindi">Hindi (हिंदी)</option>
                                <option value="Urdu">Urdu (اردو)</option>
                                <option value="Arabic">Arabic (العربية)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="include_hashtags" v-model="options.include_hashtags" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                        <label for="include_hashtags" class="text-sm text-surface-700 dark:text-surface-300">Include relevant hashtags and emojis</label>
                    </div>

                    <button 
                        @click="handleGenerate" 
                        :disabled="!inputText || isLoading"
                        class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm"
                    >
                        <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                        {{ isLoading ? 'Generating Post...' : 'Generate Post' }}
                    </button>
                    
                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">
                        {{ error }}
                    </div>
                </div>

                <!-- Output Area -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Generated Post</label>
                        <div v-if="result" class="flex items-center gap-2">
                            <span v-if="options.platform === 'Twitter/X'" :class="{'text-red-500 font-bold': result.length > 280}" class="text-xs text-surface-500">{{ result.length }}/280 chars</span>
                            <button @click="copyResult" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
                                <svg v-if="copied" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                {{ copied ? 'Copied' : 'Copy' }}
                            </button>
                        </div>
                    </div>
                    
                    <div class="relative h-full min-h-[300px]">
                        <div v-if="!result && !isLoading" class="absolute inset-0 border-2 border-dashed border-surface-200 dark:border-surface-700 rounded-xl flex items-center justify-center text-surface-400 dark:text-surface-500 bg-surface-50 dark:bg-surface-900/50">
                            Result will appear here
                        </div>
                        
                        <div v-else-if="isLoading" class="absolute inset-0 border border-surface-200 dark:border-surface-700 rounded-xl flex flex-col items-center justify-center bg-surface-50 dark:bg-surface-900/50">
                            <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                            <p class="mt-4 text-surface-500 font-medium animate-pulse">Drafting post...</p>
                        </div>
                        
                        <textarea 
                            v-else
                            v-model="result"
                            readonly
                            class="block w-full h-full rounded-xl border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none text-base p-4 whitespace-pre-wrap"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </AiToolWrapper>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';
import { useAiRequest } from '@/Composables/useAiRequest';

const props = defineProps({
    tool: Object,
});

const page = usePage();
const isPro = computed(() => page.props.auth.user?.is_pro || false);

const maxLength = computed(() => {
    const config = props.tool.ai_config;
    if (!config) return isPro.value ? 5000 : 1000;
    return isPro.value ? config.max_input_length_pro : config.max_input_length_free;
});

const inputText = ref('');
const options = ref({
    platform: 'Facebook',
    tone: 'Professional',
    length: 'Medium',
    language: 'English (US)',
    include_hashtags: true
});
const copied = ref(false);

const { isLoading, error, result, remainingRequests, generate } = useAiRequest();

const handleGenerate = () => {
    generate(props.tool.slug, inputText.value, options.value);
};

const copyResult = () => {
    if (!result.value) return;
    navigator.clipboard.writeText(result.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};
</script>
