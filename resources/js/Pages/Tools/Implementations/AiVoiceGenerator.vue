<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="space-y-6">
            <!-- Language Selector -->
            <div>
                <label class="text-xs font-medium text-surface-500 mb-2 block">Select Language</label>
                <div class="flex flex-wrap gap-2">
                    <button v-for="lang in languages" :key="lang.value" @click="selectedLang = lang.value"
                            :class="selectedLang === lang.value ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border-surface-200 dark:border-surface-700 hover:border-primary-300'"
                            class="px-4 py-2 rounded-xl text-sm border font-medium transition-all flex items-center gap-1.5">
                        {{ lang.label }}
                    </button>
                </div>
            </div>

            <!-- Voice Selector -->
            <div>
                <label class="text-xs font-medium text-surface-500 mb-2 block">Select Voice</label>
                <div v-if="filteredVoices.length" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <button v-for="voice in filteredVoices" :key="voice.id" @click="selectedVoice = voice"
                            :class="selectedVoice?.id === voice.id ? 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-900/20 border-primary-200' : 'bg-white dark:bg-surface-800 border-surface-200 dark:border-surface-700 hover:border-primary-200'"
                            class="p-3 rounded-xl border text-left transition-all">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-lg">{{ voice.gender === 'female' ? '👩' : '👨' }}</span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ voice.name }}</p>
                                <p class="text-xs text-surface-400">{{ voice.accent || voice.provider }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <a v-if="voice.preview_url" :href="voice.preview_url" @click.stop target="_blank" class="text-xs text-primary-500 hover:text-primary-700">▶ Preview</a>
                            <span v-if="voice.is_pro_only" class="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-600 px-2 py-0.5 rounded-full">Pro</span>
                        </div>
                    </button>
                </div>
                <div v-else class="text-sm text-surface-400 py-4 text-center bg-surface-50 dark:bg-surface-900 rounded-xl border border-dashed border-surface-300 dark:border-surface-700">
                    No voices available for this language.
                </div>
            </div>

            <!-- Text Input -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-surface-200 dark:border-surface-700">
                    <span class="text-xs text-surface-400">
                        <span :class="isOverLimit ? 'text-red-500 font-semibold' : ''">{{ charCount }}</span> / {{ charLimit }} chars
                    </span>
                    <span v-if="['arabic', 'urdu'].includes(selectedLang)" class="text-xs text-surface-400">RTL language</span>
                </div>
                <textarea v-model="text" rows="5" :dir="['arabic', 'urdu'].includes(selectedLang) ? 'rtl' : 'ltr'"
                          :placeholder="placeholder"
                          class="w-full px-5 py-4 text-sm bg-transparent outline-none resize-y text-surface-800 dark:text-surface-200 leading-relaxed border-none focus:ring-0"></textarea>
            </div>

            <!-- Speed & Pitch Controls -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-surface-500">Speed</label>
                        <span class="text-xs font-semibold text-primary-600">{{ speedLabel }}</span>
                    </div>
                    <input type="range" v-model.number="speed" min="0.5" max="2.0" step="0.25" class="w-full accent-primary-600" />
                    <div class="flex justify-between text-xs text-surface-400 mt-1"><span>Slow</span><span>Fast</span></div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-surface-500">Pitch</label>
                        <span class="text-xs font-semibold text-primary-600">{{ pitchLabel }}</span>
                    </div>
                    <input type="range" v-model.number="pitch" min="0.5" max="2.0" step="0.25" class="w-full accent-primary-600" />
                    <div class="flex justify-between text-xs text-surface-400 mt-1"><span>Low</span><span>High</span></div>
                </div>
            </div>

            <!-- Generate Button -->
            <button @click="generate" :disabled="!text.trim() || !selectedVoice || isLoading || isOverLimit"
                    class="w-full py-3.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.414a5 5 0 001.414 1.414m-4.242-4.242a9 9 0 0112.728 0" /></svg>
                {{ isLoading ? 'Generating Voice...' : '🎙️ Generate Voice' }}
            </button>

            <!-- Upgrade notice -->
            <div v-if="isOverLimit" class="flex items-center gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 text-sm">
                <span class="text-amber-600 dark:text-amber-400 font-medium">⚠️ Character limit: {{ charLimit.toLocaleString() }}. Upgrade to Pro for {{ proCharLimit.toLocaleString() }} characters.</span>
                <a href="/pricing" class="ml-auto text-xs bg-amber-500 text-white px-3 py-1 rounded-lg hover:bg-amber-600 transition-colors">Upgrade</a>
            </div>

            <!-- Error -->
            <div v-if="error" class="rounded-xl p-4 text-sm" :class="errorUpgrade ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800'">
                <p class="font-medium" :class="errorUpgrade ? 'text-primary-700 dark:text-primary-400' : 'text-red-700 dark:text-red-400'">{{ error }}</p>
                <a v-if="errorUpgrade" href="/pricing" class="mt-2 inline-block text-xs bg-primary-600 text-white px-4 py-1.5 rounded-lg hover:bg-primary-700 transition-colors">Upgrade to Pro →</a>
            </div>

            <!-- Audio Player -->
            <div v-if="audioUrl" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-5">
                <audio ref="audioRef" :src="audioUrl" @ended="isPlaying = false" @play="isPlaying = true" @pause="isPlaying = false" preload="auto" class="hidden"></audio>
                <div class="flex items-center gap-4">
                    <button @click="togglePlay"
                            class="w-14 h-14 rounded-full bg-primary-600 hover:bg-primary-700 text-white flex items-center justify-center flex-shrink-0 transition-colors shadow-lg">
                        <svg v-if="!isPlaying" class="w-6 h-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </button>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-surface-900 dark:text-white">{{ selectedVoice?.name }}</span>
                            <span class="text-xs text-surface-400">~{{ formatDuration(duration) }}</span>
                        </div>
                        <div class="flex items-center gap-0.5 h-8">
                            <div v-for="i in 40" :key="i" class="w-1 rounded-full transition-all"
                                 :class="isPlaying ? 'bg-primary-400' : 'bg-surface-200 dark:bg-surface-700'"
                                 :style="{ height: (Math.sin(i * 0.5) * 50 + 60) + '%' }"></div>
                        </div>
                        <div class="text-xs text-surface-400 mt-1">Available for 1 hour</div>
                    </div>
                </div>
                <button @click="downloadAudio"
                        class="mt-4 w-full py-2.5 bg-surface-100 dark:bg-surface-900 hover:bg-surface-200 dark:hover:bg-surface-800 text-surface-700 dark:text-surface-300 text-sm font-medium rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download MP3
                </button>
            </div>
        </div>
    </AiToolWrapper>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';
import axios from 'axios';

const props = defineProps({
    tool: Object,
    voices: { type: Array, default: () => [] },
});

const languages = [
    { value: 'bangla', label: '🇧🇩 Bangla' },
    { value: 'english', label: '🇺🇸 English' },
    { value: 'hindi', label: '🇮🇳 Hindi' },
    { value: 'arabic', label: '🇸🇦 Arabic' },
    { value: 'urdu', label: '🇵🇰 Urdu' },
];

const text = ref('');
const selectedVoice = ref(null);
const speed = ref(1.0);
const pitch = ref(1.0);
const isLoading = ref(false);
const audioUrl = ref(null);
const duration = ref(null);
const error = ref(null);
const errorUpgrade = ref(false);
const isPlaying = ref(false);
const audioRef = ref(null);
const selectedLang = ref('bangla');
const remainingRequests = ref(null);

const page = usePage();
const isPro = computed(() => page.props.auth.user?.is_pro || false);

const charLimit = computed(() => {
    const c = props.tool?.ai_config;
    return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : (isPro.value ? 2000 : 200);
});
const proCharLimit = computed(() => props.tool?.ai_config?.max_input_length_pro ?? 2000);
const charCount = computed(() => text.value.length);
const isOverLimit = computed(() => charCount.value > charLimit.value);

const filteredVoices = computed(() =>
    (props.voices || []).filter(v => v.language === selectedLang.value && v.is_active)
);

const placeholder = computed(() => {
    const placeholders = {
        bangla: 'Type your Bangla text here...',
        arabic: 'Type your Arabic text here...',
        urdu: 'Type your Urdu text here...',
        hindi: 'Type your Hindi text here...',
        english: 'Type your text here...',
    };
    return placeholders[selectedLang.value] || 'Type your text here...';
});

const speedLabel = computed(() => {
    const labels = { 0.5: 'Very Slow', 0.75: 'Slow', 1.0: 'Normal', 1.25: 'Fast', 1.5: 'Very Fast', 2.0: 'Maximum' };
    return labels[speed.value] || `${speed.value}x`;
});

const pitchLabel = computed(() => {
    if (pitch.value === 1.0) return 'Normal';
    return pitch.value > 1.0 ? 'High' : 'Low';
});

const generate = async () => {
    if (!text.value.trim() || !selectedVoice.value || isOverLimit.value) return;
    isLoading.value = true;
    error.value = null;
    errorUpgrade.value = false;
    audioUrl.value = null;

    try {
        const res = await axios.post('/api/ai/voice-generator', {
            text: text.value,
            voice_id: selectedVoice.value.id,
            speed: speed.value,
            pitch: pitch.value,
        });
        audioUrl.value = res.data.download_url;
        duration.value = res.data.duration;

        await nextTick();
        audioRef.value?.load();
    } catch (e) {
        if (e.response?.data?.upgrade) {
            error.value = e.response.data.message;
            errorUpgrade.value = true;
        } else {
            error.value = e.response?.data?.message || 'Voice generation failed. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
};

const togglePlay = () => {
    if (!audioRef.value) return;
    if (isPlaying.value) {
        audioRef.value.pause();
    } else {
        audioRef.value.play();
    }
};

const downloadAudio = () => {
    if (!audioUrl.value) return;
    const a = document.createElement('a');
    a.href = audioUrl.value;
    a.download = 'ezytools-voice.mp3';
    a.click();
};

const formatDuration = (secs) => {
    if (!secs) return '0s';
    const m = Math.floor(secs / 60);
    const s = Math.round(secs % 60);
    return m > 0 ? `${m}m ${s}s` : `${s}s`;
};

// Auto-select first voice when language changes
watch(selectedLang, () => {
    selectedVoice.value = filteredVoices.value[0] ?? null;
}, { immediate: true });
</script>
