<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Input Area -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Topic or Content</label>
                        <div class="relative">
                            <textarea
                                v-model="inputText"
                                rows="6"
                                :maxlength="maxLength"
                                class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors resize-y text-base p-4"
                                placeholder="Paste your text here or type a topic (e.g. Photosynthesis, World War II, etc.)"
                            ></textarea>

                            <div class="absolute bottom-3 right-3 text-[10px] font-mono text-surface-400 bg-surface-100/50 dark:bg-surface-800/50 px-1.5 py-0.5 rounded">
                                {{ inputText.length }} / {{ maxLength }}
                            </div>

                            <button v-if="inputText" @click="inputText = ''" class="absolute top-3 right-3 text-surface-400 hover:text-surface-600 dark:hover:text-surface-200 p-1 bg-surface-100 dark:bg-surface-800 rounded-lg transition-colors" title="Clear text">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-surface-300 border-dashed rounded-xl cursor-pointer bg-surface-50 dark:hover:bg-surface-800 dark:bg-surface-900 hover:bg-surface-100 dark:border-surface-600 dark:hover:border-surface-500 transition-all">
                            <div v-if="!fileLoading" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-surface-500 dark:text-surface-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-sm text-surface-500 dark:text-surface-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-surface-500 dark:text-surface-400">PDF or Text File (Max 10MB)</p>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center pt-5 pb-6">
                                <div class="w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">Reading file...</p>
                            </div>
                            <input type="file" class="hidden" accept=".pdf,.txt" @change="handleFileUpload" />
                        </label>
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Language</label>
                            <select v-model="options.language" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="English">English</option>
                                <option value="Bengali">Bangla (বাংলা)</option>
                                <option value="Hindi">Hindi (हिंदी)</option>
                                <option value="Urdu">Urdu (اردو)</option>
                                <option value="Arabic">Arabic (العربية)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Number of Questions</label>
                            <select v-model="options.count" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="5">5 Questions</option>
                                <option value="10">10 Questions</option>
                                <option value="15">15 Questions</option>
                                <option value="20">20 Questions</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Difficulty Level</label>
                            <select v-model="options.level" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="Easy">Easy</option>
                                <option value="Medium">Medium</option>
                                <option value="Hard">Hard</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Custom Prompt (Optional)</label>
                            <input v-model="options.custom_prompt" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. Focus on biology">
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
                        <SparklesIcon v-else class="w-5 h-5" />
                        {{ isLoading ? 'Generating MCQs...' : 'Generate MCQs' }}
                    </button>

                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">
                        {{ error }}
                    </div>
                </div>

                <!-- Output Area -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Generated MCQs</label>
                        <div v-if="result" class="flex items-center gap-2">
                            <button @click="copyResult" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
                                <svg v-if="copied" class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                {{ copied ? 'Copied' : 'Copy' }}
                            </button>
                            <span class="text-surface-300 dark:text-surface-600">|</span>
                            <button @click="downloadText" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
                                <ArrowDownTrayIcon class="w-4 h-4" />
                                Download
                            </button>
                        </div>
                    </div>

                    <div class="relative h-full min-h-[500px]">
                        <div v-if="!result && !isLoading" class="absolute inset-0 border-2 border-dashed border-surface-200 dark:border-surface-700 rounded-xl flex items-center justify-center text-surface-400 dark:text-surface-500 bg-surface-50 dark:bg-surface-900/50">
                            Result will appear here
                        </div>

                        <div v-else-if="isLoading" class="absolute inset-0 border border-surface-200 dark:border-surface-700 rounded-xl flex flex-col items-center justify-center bg-surface-50 dark:bg-surface-900/50">
                            <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
                            <p class="mt-4 text-surface-500 font-medium animate-pulse text-center px-6">Creating your MCQ questions... This may take a few seconds.</p>
                        </div>

                        <div
                            v-else
                            class="block w-full h-full rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors text-base p-6 overflow-y-auto whitespace-pre-wrap font-sans"
                            v-html="formattedResult"
                        ></div>
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
import { SparklesIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';

const props = defineProps({
    tool: Object,
});

const page = usePage();
const isPro = computed(() => page.props.auth.user?.is_pro || false);

const maxLength = computed(() => {
    const config = props.tool.ai_config;
    if (!config) return isPro.value ? 10000 : 2000;
    return isPro.value ? config.max_input_length_pro : config.max_input_length_free;
});

const inputText = ref('');
const options = ref({
    language: 'English',
    count: '10',
    level: 'Medium',
    custom_prompt: ''
});
const copied = ref(false);
const fileLoading = ref(false);

const { isLoading, error, result, remainingRequests, generate } = useAiRequest();

const formattedResult = computed(() => {
    if (!result.value) return '';
    // Basic formatting: bold questions and answer lines
    return result.value
        .replace(/^(\d+\.|[১-৯]+\.|१-९+\.) (.*)/gm, '<strong class="text-black dark:text-white text-lg block mb-2">$1 $2</strong>')
        .replace(/^(\d+\.|[১-৯]+\.|१-९+\.) (Answer:|উত্তর:|الإجابة:)/gm, '<strong class="text-green-600 dark:text-green-400 mt-2 block">$1 $2</strong>')
        .replace(/^(Explanation:|ব্যাখ্যা:|विवरण:)/gm, '<em class="text-gray-600 dark:text-gray-400 mt-1 block">$1</em>');
});

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 10 * 1024 * 1024) {
        alert('File is too large. Max size is 10MB.');
        return;
    }

    fileLoading.value = true;

    if (file.type === 'application/pdf') {
        const formData = new FormData();
        formData.append('file', file);
        try {
            const response = await axios.post('/api/pdf/upload', formData);
            inputText.value = response.data.text || 'Failed to extract text from PDF.';
        } catch (e) {
            console.error(e);
            alert('Failed to read PDF file.');
        }
    } else {
        const reader = new FileReader();
        reader.onload = (e) => {
            inputText.value = e.target.result;
        };
        reader.readAsText(file);
    }

    fileLoading.value = false;
};

const handleGenerate = () => {
    generate(props.tool.slug, inputText.value, options.value);
};

const copyResult = () => {
    if (!result.value) return;
    navigator.clipboard.writeText(result.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};

const downloadText = () => {
    if (!result.value) return;
    const blob = new Blob([result.value], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `mcq-questions-${Date.now()}.txt`;
    a.click();
    window.URL.revokeObjectURL(url);
};
</script>
