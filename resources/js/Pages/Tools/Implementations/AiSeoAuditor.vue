<template>
    <div class="space-y-6">
        <!-- Input Panel -->
        <div class="bg-white dark:bg-surface-800 p-5 md:p-6 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 to-indigo-500"></div>

            <h3 class="text-base md:text-lg font-bold text-surface-900 dark:text-white mb-2">
                Scan Your URL
            </h3>
            <p class="text-xs md:text-sm text-surface-500 dark:text-surface-400 mb-5 leading-relaxed">
                Enter your website or webpage URL below to conduct a full SEO audit with AI-powered enhancements.
            </p>

            <form @submit.prevent="runAudit" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-8 relative">
                        <label for="url-input" class="sr-only">URL</label>
                        <input id="url-input"
                               v-model="url"
                               type="url"
                               placeholder="https://example.com/page"
                               required
                               class="w-full px-4 py-3 bg-surface-50 dark:bg-surface-900/60 border border-surface-200 dark:border-surface-700 focus:border-primary-500 focus:ring focus:ring-primary-500/20 rounded-xl outline-none text-sm text-surface-800 dark:text-surface-100 transition duration-150" />
                    </div>

                    <div class="md:col-span-4 relative">
                        <label for="keyword-input" class="sr-only">Target Keyword</label>
                        <input id="keyword-input"
                               v-model="targetKeyword"
                               type="text"
                               placeholder="Target keyword (optional)"
                               class="w-full px-4 py-3 bg-surface-50 dark:bg-surface-900/60 border border-surface-200 dark:border-surface-700 focus:border-primary-500 focus:ring focus:ring-primary-500/20 rounded-xl outline-none text-sm text-surface-800 dark:text-surface-100 transition duration-150" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <button type="submit"
                            :disabled="isLoading || !url.trim()"
                            class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-bold rounded-xl transition duration-200 flex items-center justify-center gap-2 shadow-sm text-sm">
                        <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-if="isLoading">✨ Scanning & Analyzing...</span>
                        <span v-else>🔍 Run Full SEO Audit</span>
                    </button>

                    <button v-if="reportData"
                            type="button"
                            @click="clearAll"
                            class="w-full sm:w-auto px-5 py-3 text-sm font-medium border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-800 text-surface-600 dark:text-surface-300 rounded-xl transition duration-150 text-center">
                        Clear Results
                    </button>
                </div>
            </form>
        </div>

        <!-- Progress Indicator -->
        <div v-if="isLoading" class="p-6 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 text-center shadow-sm relative overflow-hidden">
            <div class="w-16 h-16 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-sm font-bold text-surface-900 dark:text-white mb-1 transition duration-500">
                {{ activeStepText }}
            </p>
            <p class="text-xs text-surface-400">Taking a moment to evaluate the content and structure</p>
        </div>

        <!-- Error Container -->
        <div v-if="error" class="p-4 bg-red-50/50 dark:bg-red-950/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900 rounded-xl text-xs md:text-sm leading-relaxed">
            ⚠️ {{ error }}
        </div>

        <!-- Report Data Display -->
        <div v-if="reportData" class="space-y-6">
            <!-- Share and Meta Summary -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-5 bg-gradient-to-br from-primary-500/5 to-purple-500/5 dark:from-primary-950/10 dark:to-indigo-950/10 rounded-2xl border border-primary-200/40 dark:border-primary-800/40 relative">
                <div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-surface-900 dark:text-white leading-tight mb-1">
                        SEO Scorecard
                    </h2>
                    <p class="text-xs text-surface-500 dark:text-surface-400">
                        Scan details: <span class="font-bold font-mono">{{ reportData.url }}</span> &bull; Scanned on {{ formatDate(reportData.created_at) }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="copyReportLink" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700 text-surface-700 dark:text-surface-200 rounded-xl shadow-sm transition">
                        🔗 {{ copied ? 'Copied URL!' : 'Share Report' }}
                    </button>
                </div>
            </div>

            <!-- Dashboard Summary Score Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Overall -->
                <div class="p-6 bg-white dark:bg-surface-800 rounded-2xl border-2 transition text-center flex flex-col items-center justify-center relative overflow-hidden"
                     :class="scoreBg(reportData.overall_score)">
                    <span class="absolute top-2 right-2 text-xs text-surface-400 bg-white/60 dark:bg-black/30 px-2 py-0.5 rounded-full border border-surface-200/50 dark:border-surface-700/50 font-bold">
                        Overall
                    </span>
                    <div class="text-5xl font-black mb-1 mt-4" :class="scoreColor(reportData.overall_score)">
                        {{ reportData.overall_score }}
                    </div>
                    <div class="text-xs font-bold text-surface-500 mt-1">Global SEO Index</div>
                </div>

                <!-- Technical -->
                <div class="p-5 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-between">
                    <div>
                        <span class="text-xs text-surface-600 dark:text-surface-300 font-bold tracking-wider block mb-1">Technical SEO</span>
                        <div class="text-3xl font-extrabold mb-1" :class="scoreColor(reportData.technical_score)">
                            {{ reportData.technical_score }}/100
                        </div>
                    </div>
                    <div class="h-1 w-full bg-surface-100 dark:bg-surface-700 rounded-full overflow-hidden mt-3">
                        <div class="h-full bg-indigo-500 transition-all duration-300" :style="{ width: reportData.technical_score + '%' }"></div>
                    </div>
                </div>

                <!-- On-Page -->
                <div class="p-5 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-between">
                    <div>
                        <span class="text-xs text-surface-600 dark:text-surface-300 font-bold tracking-wider block mb-1">On-Page SEO</span>
                        <div class="text-3xl font-extrabold mb-1" :class="scoreColor(reportData.onpage_score)">
                            {{ reportData.onpage_score }}/100
                        </div>
                    </div>
                    <div class="h-1 w-full bg-surface-100 dark:bg-surface-700 rounded-full overflow-hidden mt-3">
                        <div class="h-full bg-amber-500 transition-all duration-300" :style="{ width: reportData.onpage_score + '%' }"></div>
                    </div>
                </div>

                <!-- Performance -->
                <div class="p-5 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-between">
                    <div>
                        <span class="text-xs text-surface-600 dark:text-surface-300 font-bold tracking-wider block mb-1">Speed & Core Vitals</span>
                        <div class="text-3xl font-extrabold mb-1" :class="scoreColor(reportData.performance_score)">
                            {{ reportData.performance_score }}/100
                        </div>
                    </div>
                    <div class="h-1 w-full bg-surface-100 dark:bg-surface-700 rounded-full overflow-hidden mt-3">
                        <div class="h-full bg-green-500 transition-all duration-300" :style="{ width: reportData.performance_score + '%' }"></div>
                    </div>
                </div>
            </div>

            <!-- Sub Metrics Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-surface-800 rounded-xl p-3 border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-center">
                    <span class="text-xs text-surface-600 dark:text-surface-300 font-bold block mb-0.5 leading-none">AI Readiness</span>
                    <span class="text-xl font-bold" :class="scoreColor(reportData.ai_readiness_score)">{{ reportData.ai_readiness_score }}%</span>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl p-3 border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-center">
                    <span class="text-xs text-surface-600 dark:text-surface-300 font-bold block mb-0.5 leading-none">Content Words</span>
                    <span class="text-xl font-bold text-surface-800 dark:text-surface-200">{{ reportData.word_count }} words</span>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl p-3 border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-center">
                    <span class="text-xs text-surface-600 dark:text-surface-300 font-bold block mb-0.5 leading-none">Response Time</span>
                    <span class="text-xl font-bold" :class="loadTimeColor(reportData.load_time)">{{ reportData.load_time }}s</span>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl p-3 border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-center">
                    <span class="text-xs text-surface-600 dark:text-surface-300 font-bold block mb-0.5 leading-none">Issues (Critical/Warning)</span>
                    <span class="text-xl font-bold" :class="issuesColor(reportData.issues_critical, reportData.issues_warning)">{{ reportData.issues_critical }} / {{ reportData.issues_warning }}</span>
                </div>
            </div>

            <!-- Core Web Vitals Row -->
            <div v-if="reportData.audit_data && reportData.audit_data.lcp" class="grid grid-cols-3 gap-3">
                <div class="bg-white dark:bg-surface-800 rounded-xl p-3 border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-center">
                    <span class="text-xs text-surface-600 dark:text-surface-300 font-bold block mb-0.5 leading-none">LCP (Largest Content Paint)</span>
                    <span class="text-xl font-bold" :class="lcpColor(reportData.audit_data.lcp)">{{ reportData.audit_data.lcp }}</span>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl p-3 border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-center">
                    <span class="text-xs text-surface-600 dark:text-surface-300 font-bold block mb-0.5 leading-none">CLS (Layout Shift)</span>
                    <span class="text-xl font-bold" :class="clsColor(reportData.audit_data.cls)">{{ reportData.audit_data.cls }}</span>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl p-3 border border-surface-200 dark:border-surface-700 text-center flex flex-col justify-center">
                    <span class="text-xs text-surface-600 dark:text-surface-300 font-bold block mb-0.5 leading-none">INP (Next Paint)</span>
                    <span class="text-xl font-bold" :class="inpColor(reportData.audit_data.inp)">{{ reportData.audit_data.inp }}</span>
                </div>
            </div>


            <!-- Dynamic SEO Fields Info Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Head Tags Info Box -->
                <div class="bg-white dark:bg-surface-800 p-5 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-between">
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-surface-800 dark:text-white uppercase tracking-wider mb-2">
                            Header & Tag Configuration
                        </h3>
                        <div>
                            <span class="text-xs font-bold text-surface-400 uppercase tracking-wide block mb-0.5">Title Tag</span>
                            <p class="text-sm text-surface-700 dark:text-surface-300 font-medium break-words leading-snug">
                                {{ reportData.meta_title || 'N/A' }}
                            </p>
                        </div>
                        <hr class="border-surface-100 dark:border-surface-700/60" />
                        <div>
                            <span class="text-xs font-bold text-surface-400 uppercase tracking-wide block mb-0.5">Meta Description</span>
                            <p class="text-xs md:text-sm text-surface-600 dark:text-surface-400 break-words leading-relaxed">
                                {{ reportData.meta_description || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Core Elements Box -->
                <div class="bg-white dark:bg-surface-800 p-5 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-between">
                    <div class="space-y-3">
                        <h3 class="text-sm font-bold text-surface-800 dark:text-white uppercase tracking-wider mb-2">
                            Content & Canonical References
                        </h3>
                        <div>
                            <span class="text-xs font-bold text-surface-400 uppercase tracking-wide block mb-0.5">Primary H1</span>
                            <p class="text-sm text-surface-700 dark:text-surface-300 font-medium leading-snug break-words">
                                {{ reportData.h1 || 'N/A' }}
                            </p>
                        </div>
                        <hr class="border-surface-100 dark:border-surface-700/60" />
                        <div>
                            <span class="text-xs font-bold text-surface-400 uppercase tracking-wide block mb-0.5">Canonical URL</span>
                            <p class="text-xs font-mono text-surface-600 dark:text-surface-400 break-all leading-relaxed">
                                {{ reportData.canonical_url || 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories & AI Insights Wrapper -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden shadow-sm">
                <!-- Navigation Tab headers -->
                <div class="flex border-b border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-900/30 overflow-x-auto text-sm">
                    <button v-for="tab in ['all', 'technical', 'on-page', 'ai_insights']" :key="tab"
                            @click="activeTab = tab"
                            class="px-5 py-3.5 font-bold tracking-wide border-b-2 whitespace-nowrap transition duration-200"
                            :class="activeTab === tab ? 'border-primary-500 text-primary-600 dark:text-primary-400 bg-white dark:bg-surface-800' : 'border-transparent text-surface-500 hover:text-surface-700 dark:hover:text-surface-200'">
                        {{ tabLabels[tab] }}
                    </button>
                </div>

                <!-- Tab Panels -->
                <div class="p-4 md:p-6">
                    <!-- Checks Lists (all / tech / onpage) -->
                    <div v-if="['all', 'technical', 'on-page'].includes(activeTab)" class="space-y-4">
                        <div v-if="filteredChecks.length === 0" class="p-6 text-center text-xs text-surface-400">
                            No checks available for this category.
                        </div>
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <CheckItem v-for="check in filteredChecks" :key="check.id"
                                       :title="check.title"
                                       :status="check.status"
                                       :message="check.message"
                                       :value="check.value"
                                       :recommendation="check.recommendation" />
                        </div>
                    </div>

                    <!-- AI Insights Tab panel -->
                    <div v-if="activeTab === 'ai_insights'" class="space-y-5">
                        <div class="bg-indigo-50/40 dark:bg-indigo-950/20 border border-indigo-200/50 dark:border-indigo-800/50 rounded-2xl p-5 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10 select-none pointer-events-none text-4xl">🤖</div>
                            <h3 class="text-sm font-extrabold text-indigo-900 dark:text-indigo-200 tracking-wide uppercase mb-2">
                                AI Content Overview & Strategy
                            </h3>
                            <p class="text-xs md:text-sm text-indigo-800 dark:text-indigo-300 leading-relaxed font-medium">
                                {{ reportData.audit_data?.ai_summary || 'Analysis complete. Content matches quality standards.' }}
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-extrabold text-surface-800 dark:text-white uppercase tracking-wider mb-3">
                                💡 Primary Recommendations
                            </h3>
                            <ul v-if="reportData.audit_data?.ai_recommendations?.length" class="space-y-3">
                                <li v-for="(rec, i) in reportData.audit_data.ai_recommendations" :key="i"
                                    class="p-4 bg-surface-50 dark:bg-surface-900/50 border border-surface-200/60 dark:border-surface-700/60 rounded-xl flex items-start gap-3">
                                    <span class="text-base text-primary-500 font-bold select-none leading-none">0{{ i + 1 }}</span>
                                    <p class="text-xs md:text-sm text-surface-700 dark:text-surface-300 leading-relaxed break-words">
                                        {{ rec }}
                                    </p>
                                </li>
                            </ul>
                            <div v-else class="text-xs text-surface-400">
                                No primary AI recommendations.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import CheckItem from './CheckItem.vue';

const props = defineProps({
    tool: Object,
    report: { type: Object, default: null }
});

const url = ref('');
const targetKeyword = ref('');
const isLoading = ref(false);
const error = ref(null);
const reportData = ref(null);
const activeTab = ref('all');
const copied = ref(false);

const activeStepText = ref('Analyzing page metadata...');
const stepInterval = ref(null);

const tabLabels = {
    all: 'All Tests',
    technical: 'Technical SEO',
    'on-page': 'On-Page SEO',
    ai_insights: 'AI Recommendations'
};

const page = usePage();
const isPro = computed(() => page.props.auth?.user?.is_pro || false);

// Auto-load if report is provided via prop
onMounted(() => {
    if (props.report) {
        reportData.value = props.report;
        url.value = props.report.url;
        targetKeyword.value = props.report.target_keyword || '';
    }
});

const scoreColor = (score) => {
    if (score >= 80) return 'text-green-500 dark:text-green-400';
    if (score >= 50) return 'text-amber-500 dark:text-amber-400';
    return 'text-red-500 dark:text-red-400';
};

const loadTimeColor = (time) => {
    if (time <= 1.5) return 'text-green-500 dark:text-green-400';
    if (time <= 3.5) return 'text-amber-500 dark:text-amber-400';
    return 'text-red-500 dark:text-red-400';
};

const issuesColor = (critical, warning) => {
    if (critical > 0) return 'text-red-500 dark:text-red-400';
    if (warning > 0) return 'text-amber-500 dark:text-amber-400';
    return 'text-green-500 dark:text-green-400';
};

const lcpColor = (lcp) => {
    if (!lcp) return 'text-surface-800 dark:text-surface-200';
    const val = parseFloat(lcp);
    if (val <= 2.5) return 'text-green-500 dark:text-green-400';
    if (val <= 4.0) return 'text-amber-500 dark:text-amber-400';
    return 'text-red-500 dark:text-red-400';
};

const clsColor = (cls) => {
    if (!cls) return 'text-surface-800 dark:text-surface-200';
    const val = parseFloat(cls);
    if (val <= 0.1) return 'text-green-500 dark:text-green-400';
    if (val <= 0.25) return 'text-amber-500 dark:text-amber-400';
    return 'text-red-500 dark:text-red-400';
};

const inpColor = (inp) => {
    if (!inp) return 'text-surface-800 dark:text-surface-200';
    const val = parseInt(inp);
    if (val <= 200) return 'text-green-500 dark:text-green-400';
    if (val <= 500) return 'text-amber-500 dark:text-amber-400';
    return 'text-red-500 dark:text-red-400';
};

const scoreBg = (score) => {
    if (score >= 80) return 'bg-green-50/50 border-green-200 dark:bg-green-950/10 dark:border-green-800';
    if (score >= 50) return 'bg-amber-50/50 border-amber-200 dark:bg-amber-950/10 dark:border-amber-800';
    return 'bg-red-50/50 border-red-200 dark:bg-red-950/10 dark:border-red-800';
};

const filteredChecks = computed(() => {
    if (!reportData.value?.audit_data?.checks) return [];
    if (activeTab.value === 'all') return reportData.value.audit_data.checks;
    return reportData.value.audit_data.checks.filter(c => c.category === activeTab.value);
});

const runAudit = async () => {
    if (!url.value.trim()) return;
    isLoading.value = true;
    error.value = null;
    reportData.value = null;

    // Loading steps
    const messages = [
        'Fetching webpage structure...',
        'Checking robots.txt & canonical tags...',
        'Assessing on-page word counts & H1 tags...',
        'Invoking Google PageSpeed evaluation...',
        'Generating AI recommendations...'
    ];
    let i = 0;
    activeStepText.value = messages[0];
    stepInterval.value = setInterval(() => {
        i = (i + 1) % messages.length;
        activeStepText.value = messages[i];
    }, 3500);

    try {
        const res = await axios.post('/api/ai/seo-auditor', {
            url: url.value,
            target_keyword: targetKeyword.value
        });
        reportData.value = res.data.report;
    } catch (e) {
        if (e.response?.status === 429) {
            error.value = e.response.data.message || 'Daily limit reached. Sign in or upgrade to Pro for more.';
        } else {
            error.value = e.response?.data?.message || 'The SEO Audit failed. Please check the URL format and try again.';
        }
    } finally {
        clearInterval(stepInterval.value);
        isLoading.value = false;
    }
};

const clearAll = () => {
    url.value = '';
    targetKeyword.value = '';
    reportData.value = null;
    error.value = null;
};

const copyReportLink = () => {
    if (!reportData.value) return;
    const base = window.location.origin;
    const shareableUrl = `${base}/tools/ai/ai-seo-auditor/report/${reportData.value.uuid}`;
    navigator.clipboard.writeText(shareableUrl);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2500);
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>
