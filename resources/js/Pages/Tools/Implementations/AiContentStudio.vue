<template>
    <AiToolWrapper :remaining="remainingRequests">
        <div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
            <!-- Tabs -->
            <div class="flex gap-1 bg-surface-100 dark:bg-surface-900 rounded-xl p-1 mb-6">
                <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
                    :class="[activeTab === tab.key ? 'bg-white dark:bg-surface-800 text-surface-900 dark:text-white shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300']"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition-all text-center">
                    {{ tab.label }}
                </button>
            </div>

            <!-- TAB 1: Content Director -->
            <div v-show="activeTab === 'director'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Topic / Title / Keyword</label>
                        <textarea v-model="directorInput" rows="3" :maxlength="maxLength" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4 resize-y" placeholder="Enter your topic, title, or keyword..."></textarea>
                        <div class="text-right text-[10px] text-surface-400 mt-1">{{ directorInput.length }} / {{ maxLength }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Content Type</label>
                            <select v-model="directorOpts.content_type" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option v-for="t in contentTypes" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Tone</label>
                            <select v-model="directorOpts.tone" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option v-for="t in tones" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Target Audience</label>
                            <select v-model="directorOpts.audience" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option v-for="a in audiences" :key="a" :value="a">{{ a }}</option>
                            </select>
                        </div>
                        <LanguageSelector v-model="directorOpts.language" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Content Length</label>
                        <div class="flex items-center gap-4">
                            <input type="range" v-model.number="directorOpts.word_count" min="200" max="1000" step="100" class="flex-1 h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700" />
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300 w-20 text-right">~{{ directorOpts.word_count }}w</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Key Points (optional)</label>
                        <textarea v-model="directorOpts.key_points" rows="2" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-3 resize-none" placeholder="Bullet points or key ideas..."></textarea>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" v-model="directorOpts.include_cta" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" />
                        <span class="text-sm text-surface-700 dark:text-surface-300">Include Call-to-Action</span>
                    </label>
                    <button @click="handleDirector" :disabled="!directorInput || isLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ isLoading ? 'Generating...' : 'Generate Content' }}
                    </button>
                    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ error }}</div>
                </div>
                <AiOutputCard :content="result" :loading="isLoading" :language="directorOpts.language" title="Generated Content" loadingText="Writing content..." :showCopy="true" :showDownload="true" downloadFilename="content.txt" :showSendToHumanizer="!!result" @send-to-humanizer="sendToHumanizer" />
            </div>

            <!-- TAB 2: Humanizer -->
            <div v-show="activeTab === 'humanizer'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Paste AI-Generated Text</label>
                        <textarea v-model="humanizerInput" rows="6" :maxlength="maxLength" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4 resize-y" placeholder="Paste text to humanize..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Humanize Level</label>
                            <select v-model="humanizerOpts.level" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="Light">Light — minor tweaks</option>
                                <option value="Medium">Medium — moderate rewrite</option>
                                <option value="Heavy">Heavy — complete rewrite</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Writing Style</label>
                            <select v-model="humanizerOpts.style" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option v-for="s in writingStyles" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </div>
                        <LanguageSelector v-model="humanizerOpts.language" />
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="humanizerOpts.first_person" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" /><span class="text-sm text-surface-700 dark:text-surface-300">Use first person (I, we, my)</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="humanizerOpts.anecdotes" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" /><span class="text-sm text-surface-700 dark:text-surface-300">Add personal examples</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="humanizerOpts.vary_sentences" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" /><span class="text-sm text-surface-700 dark:text-surface-300">Vary sentence length</span></label>
                    </div>
                    <button @click="handleHumanizer" :disabled="!humanizerInput || humanizerLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <svg v-if="humanizerLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ humanizerLoading ? 'Humanizing...' : 'Humanize Text' }}
                    </button>
                    <div v-if="humanizerError" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ humanizerError }}</div>
                    <!-- Human Score -->
                    <div v-if="humanizerResult" class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                        <p class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-2">AI Detection Risk</p>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 bg-surface-200 dark:bg-surface-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all" :class="humanScore >= 80 ? 'bg-green-500' : humanScore >= 50 ? 'bg-yellow-500' : 'bg-red-500'" :style="{width: humanScore + '%'}"></div>
                            </div>
                            <span class="text-sm font-bold" :class="humanScore >= 80 ? 'text-green-600' : humanScore >= 50 ? 'text-yellow-600' : 'text-red-600'">~{{ humanScore }}% Human-like</span>
                        </div>
                    </div>
                </div>
                <AiOutputCard :content="humanizerResult" :loading="humanizerLoading" :language="humanizerOpts.language" title="Humanized Text" loadingText="Humanizing..." :showCopy="true" :showDownload="true" downloadFilename="humanized.txt" />
            </div>

            <!-- TAB 3: One-Click -->
            <div v-show="activeTab === 'oneclick'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Topic / Title / Keyword</label>
                        <textarea v-model="oneclickInput" rows="3" :maxlength="maxLength" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-base p-4 resize-y" placeholder="Enter your topic..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Content Type</label>
                            <select v-model="directorOpts.content_type" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option v-for="t in contentTypes" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>
                        <LanguageSelector v-model="directorOpts.language" />
                    </div>
                    <button @click="handleOneClick" :disabled="!oneclickInput || oneclickLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                        <svg v-if="oneclickLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ oneclickLoading ? (oneclickStep === 1 ? 'Step 1: Generating...' : 'Step 2: Humanizing...') : 'Generate & Humanize' }}
                    </button>
                    <div v-if="oneclickError" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ oneclickError }}</div>
                </div>
                <AiOutputCard :content="oneclickResult" :loading="oneclickLoading" :language="directorOpts.language" title="Final Human-Like Content" :loadingText="oneclickStep === 1 ? 'Generating content...' : 'Humanizing...'" :showCopy="true" :showDownload="true" downloadFilename="final-content.txt" />
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
const maxLength = computed(() => {
    const c = props.tool.ai_config;
    return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : (isPro.value ? 5000 : 1000);
});

const tabs = [
    { key: 'director', label: '✍️ Content Director' },
    { key: 'humanizer', label: '🧠 Humanizer' },
    { key: 'oneclick', label: '⚡ One-Click' },
];
const activeTab = ref('director');

const contentTypes = ['Blog Post', 'Social Media Post', 'Product Description', 'Ad Copy', 'Email Newsletter', 'YouTube Script', 'Press Release', 'Website Landing Page Copy', 'WhatsApp Message'];
const tones = ['Professional', 'Casual & Friendly', 'Persuasive', 'Humorous', 'Inspirational', 'Empathetic', 'Authoritative'];
const audiences = ['General Public', 'Students', 'Business Owners', 'Tech Professionals', 'Homemakers', 'Youth (18-25)'];
const writingStyles = ['Conversational', 'Storytelling', 'Academic', 'Journalistic', 'Informal/Chatty'];

// Director
const directorInput = ref('');
const directorOpts = ref({ content_type: 'Blog Post', tone: 'Professional', audience: 'General Public', language: 'english_us', word_count: 500, key_points: '', include_cta: false });
const { isLoading, error, result, remainingRequests, generate } = useAiRequest();

const handleDirector = () => {
    generate(props.tool.slug, directorInput.value, { ...directorOpts.value, mode: 'director' });
};

const sendToHumanizer = (text) => { humanizerInput.value = text; activeTab.value = 'humanizer'; };

// Humanizer
const humanizerInput = ref('');
const humanizerOpts = ref({ level: 'Medium', style: 'Conversational', language: 'english_us', first_person: false, anecdotes: false, vary_sentences: true });
const humanizerLoading = ref(false);
const humanizerError = ref(null);
const humanizerResult = ref('');

const humanScore = computed(() => {
    if (!humanizerResult.value) return 0;
    const text = humanizerResult.value;
    const sentences = text.split(/[.!?]+/).filter(s => s.trim());
    if (sentences.length === 0) return 50;
    const lengths = sentences.map(s => s.trim().split(/\s+/).length);
    const avg = lengths.reduce((a, b) => a + b, 0) / lengths.length;
    const variance = lengths.reduce((sum, l) => sum + Math.pow(l - avg, 2), 0) / lengths.length;
    const burstiness = Math.min(Math.sqrt(variance) / avg, 1);
    return Math.min(95, Math.round(50 + burstiness * 45));
});

const handleHumanizer = async () => {
    humanizerLoading.value = true;
    humanizerError.value = null;
    humanizerResult.value = '';
    try {
        const res = await axios.post(`/api/ai/${props.tool.slug}`, { message: humanizerInput.value, options: { ...humanizerOpts.value, mode: 'humanizer' } });
        humanizerResult.value = res.data.content;
    } catch (e) { humanizerError.value = e.response?.data?.message || 'Failed to humanize'; } finally { humanizerLoading.value = false; }
};

// One-Click
const oneclickInput = ref('');
const oneclickLoading = ref(false);
const oneclickError = ref(null);
const oneclickResult = ref('');
const oneclickStep = ref(0);

const handleOneClick = async () => {
    oneclickLoading.value = true;
    oneclickError.value = null;
    oneclickResult.value = '';
    oneclickStep.value = 1;
    try {
        const gen = await axios.post(`/api/ai/${props.tool.slug}`, { message: oneclickInput.value, options: { ...directorOpts.value, mode: 'director' } });
        oneclickStep.value = 2;
        const hum = await axios.post(`/api/ai/${props.tool.slug}`, { message: gen.data.content, options: { mode: 'humanizer', level: 'Medium', style: 'Conversational', language: directorOpts.value.language } });
        oneclickResult.value = hum.data.content;
    } catch (e) { oneclickError.value = e.response?.data?.message || 'Failed'; } finally { oneclickLoading.value = false; oneclickStep.value = 0; }
};

import axios from 'axios';
</script>
