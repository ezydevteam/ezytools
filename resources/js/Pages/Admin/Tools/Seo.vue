<template>
    <AdminLayout>
        <Head :title="`SEO Content — ${tool.name}`" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tools.index')" class="text-surface-500 hover:text-surface-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </Link>
                <div>
                    <span class="text-surface-900 dark:text-white font-semibold">SEO Content</span>
                    <span class="text-surface-500 ml-2 text-sm">{{ tool.name }}</span>
                </div>
            </div>
        </template>

        <!-- Tabs -->
        <div class="flex gap-1 bg-surface-100 dark:bg-surface-900 rounded-lg p-1 mb-6 w-fit">
            <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="activeTab === tab.key ? 'bg-white dark:bg-surface-700 text-surface-900 dark:text-white shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'" class="px-4 py-2 text-sm rounded-md transition-colors font-medium">
                {{ tab.label }}
            </button>
        </div>

        <!-- TAB 1: How-to & About -->
        <div v-if="activeTab === 'content'" class="space-y-6">
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <h2 class="font-semibold text-surface-900 dark:text-white mb-4">How-to Section</h2>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs text-surface-500 mb-1">Title (Bangla)</label>
                        <input v-model="contentForm.how_to_title" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500" placeholder="কীভাবে ব্যবহার করবেন" />
                    </div>
                    <div>
                        <label class="block text-xs text-surface-500 mb-1">Title (English)</label>
                        <input v-model="contentForm.how_to_title_en" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500" placeholder="How to Use" />
                    </div>
                </div>

                <!-- Steps -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs text-surface-500 font-medium">Numbered Steps</label>
                        <button @click="addStep" class="text-xs text-primary-600 hover:text-primary-700 font-medium">+ Add step</button>
                    </div>
                    <div v-for="(step, i) in contentForm.how_to_steps" :key="i" class="bg-surface-50 dark:bg-surface-900 rounded-lg p-4 mb-2 border border-surface-200 dark:border-surface-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-surface-500">Step {{ i + 1 }}</span>
                            <button @click="removeStep(i)" class="text-xs text-red-500 hover:text-red-600">Remove</button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input v-model="step.step" placeholder="ধাপ (বাংলা)" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full focus:ring-primary-500" />
                            <input v-model="step.step_en" placeholder="Step (English)" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full focus:ring-primary-500" />
                            <textarea v-model="step.description" placeholder="বিবরণ (বাংলা)" rows="2" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full resize-none focus:ring-primary-500" />
                            <textarea v-model="step.description_en" placeholder="Description (English)" rows="2" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full resize-none focus:ring-primary-500" />
                        </div>
                    </div>
                </div>

                <!-- Body content -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-surface-500 mb-1">Body (Bangla, Markdown)</label>
                        <textarea v-model="contentForm.how_to_content" rows="6" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm font-mono focus:ring-primary-500 resize-none" />
                    </div>
                    <div>
                        <label class="block text-xs text-surface-500 mb-1">Body (English, Markdown)</label>
                        <textarea v-model="contentForm.how_to_content_en" rows="6" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm font-mono focus:ring-primary-500 resize-none" />
                    </div>
                </div>
            </div>

            <!-- About section -->
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <h2 class="font-semibold text-surface-900 dark:text-white mb-4">About Section</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-surface-500 mb-1">About (Bangla)</label>
                        <textarea v-model="contentForm.about_content" rows="5" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500 resize-none" />
                    </div>
                    <div>
                        <label class="block text-xs text-surface-500 mb-1">About (English)</label>
                        <textarea v-model="contentForm.about_content_en" rows="5" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500 resize-none" />
                    </div>
                </div>
            </div>

            <!-- Use Cases -->
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-surface-900 dark:text-white">Use Cases</h2>
                    <button @click="addUseCase" class="text-xs text-primary-600 hover:text-primary-700 font-medium">+ Add use case</button>
                </div>
                <div v-for="(uc, i) in contentForm.use_cases" :key="i" class="bg-surface-50 dark:bg-surface-900 rounded-lg p-4 mb-2 border border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-surface-500">Use Case {{ i + 1 }}</span>
                        <button @click="contentForm.use_cases.splice(i, 1)" class="text-xs text-red-500 hover:text-red-600">Remove</button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input v-model="uc.title" placeholder="শিরোনাম (বাংলা)" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full focus:ring-primary-500" />
                        <input v-model="uc.title_en" placeholder="Title (English)" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full focus:ring-primary-500" />
                        <textarea v-model="uc.description" placeholder="বিবরণ (বাংলা)" rows="2" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full resize-none focus:ring-primary-500" />
                        <textarea v-model="uc.description_en" placeholder="Description (English)" rows="2" class="rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm w-full resize-none focus:ring-primary-500" />
                    </div>
                </div>
            </div>

            <button @click="saveContent" :disabled="contentForm.processing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white rounded-xl text-sm font-medium transition-colors disabled:opacity-50 shadow-md">
                {{ contentForm.processing ? 'Saving...' : 'Save SEO Content' }}
            </button>
        </div>

        <!-- TAB 2: FAQs -->
        <div v-if="activeTab === 'faqs'" class="space-y-4">
            <div v-for="faq in faqs" :key="faq.id" class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-4 flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-surface-900 dark:text-white text-sm font-medium truncate">{{ faq.question }}</p>
                    <p v-if="faq.question_bn" class="text-surface-500 text-xs mt-0.5 truncate">{{ faq.question_bn }}</p>
                    <p class="text-surface-400 text-xs mt-2 line-clamp-2">{{ faq.answer }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button @click="deleteFaq(faq.id)" class="text-xs text-red-500 hover:text-red-600 font-medium">Delete</button>
                </div>
            </div>

            <!-- Add FAQ form -->
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <h3 class="font-semibold text-surface-900 dark:text-white mb-4">নতুন FAQ যোগ করুন</h3>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <input v-model="faqForm.question" placeholder="Question (English) *" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500" />
                    <input v-model="faqForm.question_bn" placeholder="প্রশ্ন (বাংলা)" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500" />
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <textarea v-model="faqForm.answer" rows="3" placeholder="Answer (English) *" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500 resize-none" />
                    <textarea v-model="faqForm.answer_bn" rows="3" placeholder="উত্তর (বাংলা)" class="rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500 resize-none" />
                </div>
                <button @click="saveFaq" :disabled="faqForm.processing" class="px-5 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white text-sm rounded-lg transition-colors disabled:opacity-50 font-medium">
                    FAQ যোগ করুন
                </button>
            </div>
        </div>

        <!-- TAB 3: Related Tools -->
        <div v-if="activeTab === 'related'" class="space-y-4">
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <h2 class="font-semibold text-surface-900 dark:text-white mb-2">Related Tools (max 6)</h2>
                <p class="text-surface-500 text-sm mb-4">Select tools to show as related on the tool page.</p>

                <!-- Currently selected -->
                <div v-if="selectedRelated.length" class="flex flex-wrap gap-2 mb-4">
                    <div v-for="rt in selectedRelated" :key="rt.id" class="flex items-center gap-2 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 text-xs px-3 py-1.5 rounded-full font-medium">
                        {{ rt.name }}
                        <button @click="removeRelated(rt.id)" class="hover:text-red-500 transition-colors">✕</button>
                    </div>
                </div>

                <!-- Tool picker -->
                <input v-model="relatedSearch" type="text" placeholder="Search tools..." class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-sm focus:ring-primary-500 mb-3" />
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-80 overflow-y-auto">
                    <button v-for="t in filteredTools" :key="t.id" @click="toggleRelated(t)" :class="isRelatedSelected(t.id) ? 'ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'bg-surface-50 dark:bg-surface-900 hover:bg-surface-100 dark:hover:bg-surface-800'" class="flex items-center gap-2 p-3 rounded-lg text-left transition-colors text-sm border border-surface-200 dark:border-surface-700" :disabled="selectedRelated.length >= 6 && !isRelatedSelected(t.id)">
                        <div class="min-w-0">
                            <p class="truncate text-xs font-medium text-surface-900 dark:text-white">{{ t.name }}</p>
                            <p class="truncate text-xs text-surface-400">{{ t.category?.name }}</p>
                        </div>
                    </button>
                </div>

                <button @click="saveRelated" :disabled="relatedForm.processing" class="mt-4 w-full py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white text-sm rounded-lg transition-colors font-medium disabled:opacity-50">
                    Save Related Tools
                </button>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tool: Object,
    seoContent: Object,
    faqs: Array,
    related: Array,
    allTools: Array,
});

const tabs = [
    { key: 'content', label: '📝 How-to & About' },
    { key: 'faqs', label: '❓ FAQs' },
    { key: 'related', label: '🔗 Related Tools' },
];
const activeTab = ref('content');

// SEO Content form
const contentForm = useForm({
    how_to_title: props.seoContent?.how_to_title ?? '',
    how_to_title_en: props.seoContent?.how_to_title_en ?? '',
    how_to_content: props.seoContent?.how_to_content ?? '',
    how_to_content_en: props.seoContent?.how_to_content_en ?? '',
    how_to_steps: props.seoContent?.how_to_steps ?? [],
    about_title: props.seoContent?.about_title ?? '',
    about_title_en: props.seoContent?.about_title_en ?? '',
    about_content: props.seoContent?.about_content ?? '',
    about_content_en: props.seoContent?.about_content_en ?? '',
    use_cases: props.seoContent?.use_cases ?? [],
});

const addStep = () => {
    contentForm.how_to_steps.push({ step: '', step_en: '', description: '', description_en: '' });
};
const removeStep = (i) => {
    contentForm.how_to_steps.splice(i, 1);
};
const addUseCase = () => {
    contentForm.use_cases.push({ title: '', title_en: '', description: '', description_en: '' });
};
const saveContent = () => {
    contentForm.post(route('admin.tools.seo.update', props.tool.id), { preserveScroll: true });
};

// FAQ form
const faqForm = useForm({
    question: '',
    question_bn: '',
    answer: '',
    answer_bn: '',
});
const saveFaq = () => {
    faqForm.post(route('admin.tools.faqs.store', props.tool.id), {
        preserveScroll: true,
        onSuccess: () => faqForm.reset(),
    });
};
const deleteFaq = (faqId) => {
    if (confirm('Delete this FAQ?')) {
        router.delete(route('admin.tools.faqs.destroy', { tool: props.tool.id, faq: faqId }), { preserveScroll: true });
    }
};

// Related tools
const selectedRelated = ref([...(props.related ?? [])]);
const relatedSearch = ref('');
const relatedForm = useForm({});

const filteredTools = computed(() => {
    if (!relatedSearch.value) return props.allTools;
    const s = relatedSearch.value.toLowerCase();
    return props.allTools.filter(t => t.name.toLowerCase().includes(s));
});

const isRelatedSelected = (id) => selectedRelated.value.some(t => t.id === id);
const toggleRelated = (tool) => {
    if (isRelatedSelected(tool.id)) {
        selectedRelated.value = selectedRelated.value.filter(t => t.id !== tool.id);
    } else if (selectedRelated.value.length < 6) {
        selectedRelated.value.push(tool);
    }
};
const removeRelated = (id) => {
    selectedRelated.value = selectedRelated.value.filter(t => t.id !== id);
};
const saveRelated = () => {
    const data = {
        related_tools: selectedRelated.value.map((t, i) => ({
            id: t.id,
            relation_type: 'similar',
        })),
    };
    router.post(route('admin.tools.related.update', props.tool.id), data, { preserveScroll: true });
};
</script>
