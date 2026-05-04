<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Input Area -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Text to Summarize</label>
                        <div class="text-xs text-surface-500">
                            {{ inputWordCount }} words
                        </div>
                    </div>
                    
                    <div class="relative">
                        <textarea 
                            v-model="inputText"
                            rows="10"
                            :maxlength="maxLength"
                            class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-y text-base p-4"
                            placeholder="Paste the text you want to summarize here..."
                        ></textarea>
                        
                        <div class="absolute bottom-3 right-3 text-[10px] font-mono text-surface-400 bg-surface-100/50 dark:bg-surface-800/50 px-1.5 py-0.5 rounded">
                            {{ inputText.length }} / {{ maxLength }}
                        </div>
                        
                        <button v-if="inputText" @click="inputText = ''" class="absolute top-3 right-3 text-surface-400 hover:text-surface-600 dark:hover:text-surface-200 p-1 bg-surface-100 dark:bg-surface-800 rounded-lg transition-colors" title="Clear text">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Length</label>
                            <select v-model="options.length" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="Short">Short (1-2 sentences)</option>
                                <option value="Medium">Medium (1 paragraph)</option>
                                <option value="Detailed">Detailed (Bullet points)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Language</label>
                            <select v-model="options.language" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="Original">Same as input</option>
                                <option value="English (US)">English (US)</option>
                                <option value="English (British)">English (British)</option>
                                <option value="Bengali">Bangla (বাংলা)</option>
                                <option value="Hindi">Hindi (हिंदी)</option>
                                <option value="Urdu">Urdu (اردو)</option>
                                <option value="Arabic">Arabic (العربية)</option>
                            </select>
                        </div>
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
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        {{ isLoading ? 'Summarizing...' : 'Summarize' }}
                    </button>
                    
                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">
                        {{ error }}
                    </div>
                </div>

                <!-- Output Area -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Summary</label>
                        <div v-if="result" class="flex items-center gap-2">
                            <span class="text-xs text-surface-500">{{ outputWordCount }} words</span>
                            <span class="text-surface-300 dark:text-surface-600">|</span>
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
                            <p class="mt-4 text-surface-500 font-medium animate-pulse">Extracting key points...</p>
                        </div>
                        
                        <textarea 
                            v-else
                            v-model="result"
                            readonly
                            class="block w-full h-full rounded-xl border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none text-base p-4"
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
    if (!config) return isPro.value ? 10000 : 1000;
    return isPro.value ? config.max_input_length_pro : config.max_input_length_free;
});

const inputText = ref('');
const options = ref({
    length: 'Medium',
    language: 'Original',
    format: 'Paragraphs'
});
const copied = ref(false);

const { isLoading, error, result, remainingRequests, generate } = useAiRequest();

const inputWordCount = computed(() => {
    return inputText.value.trim() ? inputText.value.trim().split(/\s+/).length : 0;
});

const outputWordCount = computed(() => {
    return result.value.trim() ? result.value.trim().split(/\s+/).length : 0;
});

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
