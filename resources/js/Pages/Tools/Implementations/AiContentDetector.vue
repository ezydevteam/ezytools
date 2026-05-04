<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="space-y-6">
            <!-- Input Area -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3 border-b border-surface-200 dark:border-surface-700">
                    <div class="flex items-center gap-3 text-sm text-surface-500">
                        <span>{{ wordCount }} words</span>
                        <span class="text-surface-300 dark:text-surface-600">&bull;</span>
                        <span :class="isOverLimit ? 'text-red-500 font-semibold' : ''">{{ charCount }} / {{ charLimit }} chars</span>
                    </div>
                    <button v-if="inputText" @click="clearAll" class="text-xs text-surface-400 hover:text-surface-600 dark:hover:text-surface-200 transition-colors">
                        Clear ✕
                    </button>
                </div>
                <textarea
                    v-model="inputText"
                    rows="12"
                    class="w-full px-5 py-4 text-sm text-surface-800 dark:text-surface-200 bg-transparent resize-y outline-none leading-relaxed border-none focus:ring-0"
                    placeholder="Paste your text here to check if it was written by AI or a human. Minimum 50 characters required."
                ></textarea>
                <div class="h-1 bg-surface-100 dark:bg-surface-900">
                    <div class="h-full transition-all duration-300"
                         :class="isOverLimit ? 'bg-red-500' : charCount > charLimit * 0.8 ? 'bg-amber-400' : 'bg-primary-500'"
                         :style="{ width: Math.min(100, (charCount / charLimit) * 100) + '%' }" />
                </div>
            </div>

            <!-- Upgrade notice -->
            <div v-if="isOverLimit" class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 text-sm">
                <span class="text-amber-600 dark:text-amber-400 font-medium">
                    ⚠️ Character limit: {{ charLimit.toLocaleString() }}. Upgrade to Pro for {{ proCharLimit.toLocaleString() }} characters.
                </span>
                <a href="/pricing" class="ml-auto text-xs bg-amber-500 text-white px-3 py-1 rounded-lg hover:bg-amber-600 transition-colors">Upgrade</a>
            </div>

            <!-- Detect button -->
            <button @click="handleDetect" :disabled="!inputText.trim() || isLoading || isOverLimit || charCount < 50"
                    class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                {{ isLoading ? 'Analyzing...' : 'Detect AI Content' }}
            </button>

            <!-- Error -->
            <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-sm border border-red-100 dark:border-red-800">
                {{ error }}
            </div>

            <!-- Results -->
            <div v-if="detectionResult" class="space-y-5">
                <!-- Main Score Card -->
                <div class="rounded-2xl border-2 p-6 text-center transition-all" :class="scoreBg(detectionResult.overall_score)">
                    <div class="inline-flex items-center justify-center w-28 h-28 rounded-full border-4 mb-4"
                         :class="detectionResult.overall_score >= 70 ? 'border-red-400 bg-red-50 dark:bg-red-900/30' : detectionResult.overall_score >= 35 ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/30' : 'border-green-400 bg-green-50 dark:bg-green-900/30'">
                        <div>
                            <div class="text-4xl font-black" :class="scoreColor(detectionResult.overall_score)">{{ Math.round(detectionResult.overall_score) }}%</div>
                            <div class="text-xs font-medium text-surface-500 mt-0.5">AI Score</div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-surface-900 dark:text-white mb-1">
                        {{ verdictLabel(detectionResult.verdict).icon }} {{ verdictLabel(detectionResult.verdict).label }}
                    </div>
                    <div class="text-sm text-surface-500">{{ verdictLabel(detectionResult.verdict).desc }}</div>
                    <div class="mt-3 inline-flex items-center gap-1.5 bg-white/60 dark:bg-black/20 px-3 py-1 rounded-full text-xs text-surface-600 dark:text-surface-400">
                        🌐 {{ detectionResult.language === 'bangla' ? 'Bangla Text' : detectionResult.language === 'mixed' ? 'Mixed Language' : 'English Text' }}
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700 text-center">
                        <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ Math.round(detectionResult.burstiness) }}%</div>
                        <div class="text-xs text-surface-400 mt-1">Burstiness</div>
                        <div class="text-xs mt-0.5" :class="detectionResult.burstiness > 50 ? 'text-green-500' : 'text-red-400'">
                            {{ detectionResult.burstiness > 50 ? 'Human-like' : 'AI-like' }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700 text-center">
                        <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ Math.round(detectionResult.perplexity) }}%</div>
                        <div class="text-xs text-surface-400 mt-1">Vocabulary</div>
                        <div class="text-xs mt-0.5" :class="detectionResult.perplexity > 60 ? 'text-green-500' : 'text-amber-400'">
                            {{ detectionResult.perplexity > 60 ? 'Diverse' : 'Repetitive' }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700 text-center">
                        <div class="text-2xl font-bold text-surface-900 dark:text-white">{{ detectionResult.word_count }}</div>
                        <div class="text-xs text-surface-400 mt-1">Total Words</div>
                        <div class="text-xs text-surface-400 mt-0.5">{{ detectionResult.char_count }} chars</div>
                    </div>
                </div>

                <!-- Sentence Analysis -->
                <div v-if="detectionResult.sentences?.length" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                        <h3 class="font-semibold text-surface-900 dark:text-white text-sm">Sentence-level Analysis</h3>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span> Human</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> Mixed</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span> AI</span>
                        </div>
                    </div>
                    <div class="p-4 space-y-2 max-h-64 overflow-y-auto">
                        <div v-for="(sentence, i) in detectionResult.sentences" :key="i"
                             class="px-3 py-2 rounded-lg text-sm leading-relaxed transition-all"
                             :class="sentenceClass(sentence.verdict)">
                            <span class="text-surface-800 dark:text-surface-200">{{ sentence.text }}</span>
                            <span class="ml-2 text-xs opacity-60">{{ Math.round(sentence.score) }}%</span>
                        </div>
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
import axios from 'axios';

const props = defineProps({ tool: Object });

const page = usePage();
const isPro = computed(() => page.props.auth.user?.is_pro || false);

const inputText = ref('');
const isLoading = ref(false);
const error = ref(null);
const detectionResult = ref(null);
const remainingRequests = ref(null);

const charLimit = computed(() => {
    const c = props.tool?.ai_config;
    return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : (isPro.value ? 5000 : 1000);
});
const proCharLimit = computed(() => props.tool?.ai_config?.max_input_length_pro ?? 5000);
const charCount = computed(() => inputText.value.length);
const wordCount = computed(() => inputText.value.trim() ? inputText.value.trim().split(/\s+/).length : 0);
const isOverLimit = computed(() => charCount.value > charLimit.value);

const scoreColor = (score) => {
    if (score >= 70) return 'text-red-500';
    if (score >= 35) return 'text-amber-500';
    return 'text-green-500';
};

const scoreBg = (score) => {
    if (score >= 70) return 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800';
    if (score >= 35) return 'bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-800';
    return 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800';
};

const verdictLabel = (verdict) => ({
    ai:    { label: 'AI Generated', desc: 'This text appears to be written by AI.', icon: '🤖' },
    mixed: { label: 'Mixed Content', desc: 'This text contains both AI and human-written elements.', icon: '⚠️' },
    human: { label: 'Human Written', desc: 'This text appears to be written by a human.', icon: '✅' },
}[verdict] || { label: 'Unknown', desc: '', icon: '❓' });

const sentenceClass = (verdict) => ({
    ai:    'bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400',
    mixed: 'bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400',
    human: 'bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400',
}[verdict] || '');

const handleDetect = async () => {
    if (!inputText.value.trim() || isOverLimit.value) return;
    isLoading.value = true;
    error.value = null;
    detectionResult.value = null;

    try {
        const res = await axios.post('/api/ai/content-detector', { text: inputText.value });
        detectionResult.value = res.data;
    } catch (e) {
        if (e.response?.data?.upgrade) {
            error.value = e.response.data.message;
        } else if (e.response?.status === 429) {
            error.value = 'Daily usage limit reached. Please try again tomorrow or upgrade to Pro.';
        } else {
            error.value = e.response?.data?.message || 'An error occurred. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};

const clearAll = () => {
    inputText.value = '';
    detectionResult.value = null;
    error.value = null;
};
</script>
