<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="space-y-6">
            <!-- Tab Switcher -->
            <div class="flex gap-1 bg-surface-100 dark:bg-surface-900 rounded-xl p-1">
                <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
                        :class="activeTab === tab.key ? 'bg-white dark:bg-surface-700 shadow text-surface-900 dark:text-white' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'"
                        class="flex-1 py-2.5 px-3 rounded-lg text-sm font-medium transition-all text-center">
                    <div>{{ tab.label }}</div>
                    <div class="text-xs opacity-60 font-normal">{{ tab.desc }}</div>
                </button>
            </div>

            <!-- Input -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-surface-200 dark:border-surface-700">
                    <div class="flex items-center gap-3 text-xs text-surface-400">
                        <span>{{ wordCount }} words</span>
                        <span class="text-surface-300 dark:text-surface-600">&bull;</span>
                        <span :class="isOverLimit ? 'text-red-500 font-semibold' : ''">{{ charCount.toLocaleString() }} / {{ charLimit.toLocaleString() }} chars</span>
                    </div>
                    <select v-model="language" class="text-xs bg-surface-100 dark:bg-surface-900 rounded-lg px-2 py-1 outline-none border-none focus:ring-0">
                        <option value="auto">Auto Detect</option>
                        <option value="bangla">Bangla</option>
                        <option value="english">English</option>
                    </select>
                </div>
                <textarea v-model="inputText" rows="10"
                          placeholder="Paste any text to detect AI and humanize it..."
                          class="w-full px-5 py-4 text-sm bg-transparent outline-none resize-y text-surface-800 dark:text-surface-200 leading-relaxed border-none focus:ring-0"></textarea>
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

            <!-- Humanizer Controls (shown when humanizer active) -->
            <div v-if="activeTab !== 'detector'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Level -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <label class="text-xs font-medium text-surface-500 mb-2 block">Humanization Level</label>
                    <div class="space-y-2">
                        <label v-for="opt in levelOptions" :key="opt.value" class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="level" :value="opt.value" class="text-primary-600 focus:ring-primary-500" />
                            <div class="flex-1">
                                <span class="text-sm font-medium text-surface-800 dark:text-surface-200">{{ opt.label }}</span>
                                <span class="text-xs text-surface-400 ml-1">{{ opt.desc }}</span>
                            </div>
                            <span v-if="opt.value === 'heavy'" class="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-600 px-1.5 py-0.5 rounded">Pro</span>
                        </label>
                    </div>
                </div>
                <!-- Style -->
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <label class="text-xs font-medium text-surface-500 mb-2 block">Writing Style</label>
                    <div class="space-y-2">
                        <label v-for="opt in styleOptions" :key="opt.value" class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="style" :value="opt.value" class="text-primary-600 focus:ring-primary-500" />
                            <div>
                                <span class="text-sm font-medium text-surface-800 dark:text-surface-200">{{ opt.label }}</span>
                                <span class="text-xs text-surface-400 ml-1">{{ opt.desc }}</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button v-if="activeTab === 'combined'" @click="detectAndHumanize" :disabled="!inputText.trim() || loadingDetect || loadingHumanize || isOverLimit"
                        class="flex-1 py-3.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                    <svg v-if="loadingDetect || loadingHumanize" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span v-if="loadingDetect">Detecting...</span>
                    <span v-else-if="loadingHumanize">Humanizing...</span>
                    <span v-else>⚡ Detect & Humanize</span>
                </button>
                <button v-if="activeTab === 'detector'" @click="detect" :disabled="!inputText.trim() || loadingDetect || isOverLimit"
                        class="flex-1 py-3.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg v-if="loadingDetect" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    {{ loadingDetect ? 'Detecting...' : '🔍 Detect AI' }}
                </button>
                <button v-if="activeTab === 'humanizer'" @click="humanize" :disabled="!inputText.trim() || loadingHumanize || isOverLimit"
                        class="flex-1 py-3.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg v-if="loadingHumanize" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    {{ loadingHumanize ? 'Humanizing...' : '✨ Humanize' }}
                </button>
            </div>

            <!-- Error -->
            <div v-if="errorMsg" class="rounded-xl p-4 text-sm" :class="errorUpgrade ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'">
                <p class="font-medium" :class="errorUpgrade ? 'text-primary-700 dark:text-primary-400' : 'text-red-700 dark:text-red-400'">{{ errorMsg }}</p>
                <a v-if="errorUpgrade" href="/pricing" class="mt-2 inline-block text-xs bg-primary-600 text-white px-4 py-1.5 rounded-lg hover:bg-primary-700 transition-colors">Upgrade to Pro →</a>
            </div>

            <!-- Detection Result (compact, shown before humanize) -->
            <div v-if="detectionResult && !humanizedText" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-5">
                <div class="flex items-center gap-4">
                    <div class="relative w-20 h-20 flex-shrink-0">
                        <svg class="w-20 h-20 -rotate-90" viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="32" fill="none" stroke="currentColor" class="text-surface-200 dark:text-surface-700" stroke-width="8"/>
                            <circle cx="40" cy="40" r="32" fill="none" :stroke="ringColor(detectionResult.overall_score)" stroke-width="8"
                                    :stroke-dasharray="`${(detectionResult.overall_score / 100) * 201} 201`" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xl font-black text-surface-900 dark:text-white">{{ Math.round(detectionResult.overall_score) }}%</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-lg font-bold text-surface-900 dark:text-white">
                            {{ detectionResult.verdict === 'ai' ? '🤖 AI Generated' : detectionResult.verdict === 'mixed' ? '⚠️ Mixed Content' : '✅ Human Written' }}
                        </div>
                        <div class="text-sm text-surface-500 mt-1">Language: {{ detectionResult.language === 'bangla' ? 'Bangla' : 'English' }}</div>
                        <div class="mt-3 space-y-1 max-h-24 overflow-y-auto">
                            <div v-for="(s, i) in detectionResult.sentences?.slice(0, 5)" :key="i" class="text-xs px-2 py-1 rounded"
                                 :class="{'bg-red-50 dark:bg-red-900/20 border-l-2 border-red-400': s.verdict === 'ai', 'bg-amber-50 dark:bg-amber-900/20 border-l-2 border-amber-400': s.verdict === 'mixed'}">
                                {{ s.text.substring(0, 80) }}{{ s.text.length > 80 ? '...' : '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Humanized Result -->
            <div v-if="humanizedText" class="space-y-4">
                <!-- Score Comparison -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 text-center border border-red-100 dark:border-red-900">
                        <div class="text-2xl font-bold text-red-500">{{ Math.round(originalScore) }}%</div>
                        <div class="text-xs text-surface-500 mt-1">AI Score (Before)</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center border border-green-100 dark:border-green-900 flex flex-col items-center justify-center">
                        <div class="text-3xl font-black text-green-500">-{{ Math.round(improvement) }}%</div>
                        <div class="text-xs text-surface-500 mt-1">Improvement</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-center border border-green-100 dark:border-green-900">
                        <div class="text-2xl font-bold text-green-500">{{ Math.round(humanizedScore) }}%</div>
                        <div class="text-xs text-surface-500 mt-1">AI Score (After)</div>
                    </div>
                </div>

                <!-- Humanized Text -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3 border-b border-surface-200 dark:border-surface-700">
                        <span class="text-sm font-medium text-surface-900 dark:text-white">✨ Humanized Text</span>
                        <div class="flex gap-2">
                            <button @click="copyHumanized" class="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-600 px-3 py-1.5 rounded-lg hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-colors">
                                {{ copied ? 'Copied!' : 'Copy' }}
                            </button>
                            <button @click="useHumanized" class="text-xs bg-surface-100 dark:bg-surface-900 text-surface-600 dark:text-surface-300 px-3 py-1.5 rounded-lg hover:bg-surface-200 dark:hover:bg-surface-800 transition-colors">
                                Re-detect
                            </button>
                        </div>
                    </div>
                    <div class="px-5 py-4 text-sm text-surface-800 dark:text-surface-200 leading-relaxed whitespace-pre-wrap">{{ humanizedText }}</div>
                </div>

                <!-- Humanize Again -->
                <button @click="humanize" :disabled="loadingHumanize"
                        class="w-full py-3 border-2 border-dashed border-primary-200 dark:border-primary-800 text-primary-600 dark:text-primary-400 text-sm font-medium rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                    🔄 Humanize Again
                </button>
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

const tabs = [
    { key: 'combined', label: '⚡ Combined', desc: 'Detect + Humanize' },
    { key: 'detector', label: '🔍 Detector', desc: 'Detect only' },
    { key: 'humanizer', label: '✨ Humanizer', desc: 'Humanize only' },
];

const levelOptions = [
    { value: 'light', label: '💡 Light', desc: 'Minor tweaks' },
    { value: 'medium', label: '⚡ Medium', desc: 'Moderate rewrite' },
    { value: 'heavy', label: '🔥 Heavy', desc: 'Full rewrite (Pro)' },
];

const styleOptions = [
    { value: 'conversational', label: '😊 Conversational', desc: 'Casual & natural' },
    { value: 'professional', label: '💼 Professional', desc: 'Business writing' },
    { value: 'academic', label: '📚 Academic', desc: 'Scholarly tone' },
];

const activeTab = ref('combined');
const inputText = ref('');
const detectionResult = ref(null);
const humanizedText = ref('');
const humanizedScore = ref(null);
const originalScore = ref(null);
const improvement = ref(null);
const level = ref('medium');
const style = ref('conversational');
const language = ref('auto');
const loadingDetect = ref(false);
const loadingHumanize = ref(false);
const errorMsg = ref(null);
const errorUpgrade = ref(false);
const copied = ref(false);
const remainingRequests = ref(null);

const charCount = computed(() => inputText.value.length);
const charLimit = computed(() => {
    const c = props.tool?.ai_config;
    return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : (isPro.value ? 5000 : 1000);
});
const proCharLimit = computed(() => props.tool?.ai_config?.max_input_length_pro ?? 5000);
const isOverLimit = computed(() => charCount.value > charLimit.value);
const wordCount = computed(() => inputText.value.trim() ? inputText.value.trim().split(/\s+/).length : 0);

const ringColor = (score) => score >= 70 ? '#EF4444' : score >= 35 ? '#F59E0B' : '#10B981';

const detect = async () => {
    if (!inputText.value.trim()) return;
    loadingDetect.value = true;
    errorMsg.value = null;
    errorUpgrade.value = false;

    try {
        const res = await axios.post('/api/ai/detector-humanizer', { action: 'detect', text: inputText.value });
        detectionResult.value = res.data;
        originalScore.value = res.data.overall_score;
    } catch (e) {
        handleError(e);
    } finally {
        loadingDetect.value = false;
    }
};

const humanize = async () => {
    if (!inputText.value.trim()) return;
    loadingHumanize.value = true;
    humanizedText.value = '';
    humanizedScore.value = null;
    errorMsg.value = null;
    errorUpgrade.value = false;

    try {
        const res = await axios.post('/api/ai/detector-humanizer', {
            action: 'humanize',
            text: inputText.value,
            level: level.value,
            style: style.value,
            language: language.value,
        });
        humanizedText.value = res.data.humanized_text;
        humanizedScore.value = res.data.humanized_score;
        originalScore.value = res.data.original_score;
        improvement.value = res.data.improvement;
    } catch (e) {
        handleError(e);
    } finally {
        loadingHumanize.value = false;
    }
};

const detectAndHumanize = async () => {
    await detect();
    if (detectionResult.value?.overall_score >= 35) {
        await humanize();
    }
};

const handleError = (e) => {
    if (e.response?.data?.upgrade) {
        errorMsg.value = e.response.data.message;
        errorUpgrade.value = true;
    } else if (e.response?.status === 429) {
        errorMsg.value = 'Daily usage limit reached.';
    } else {
        errorMsg.value = e.response?.data?.message || 'An error occurred.';
    }
};

const copyHumanized = () => {
    navigator.clipboard.writeText(humanizedText.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};

const useHumanized = () => {
    inputText.value = humanizedText.value;
    humanizedText.value = '';
    detectionResult.value = null;
    detect();
};
</script>
