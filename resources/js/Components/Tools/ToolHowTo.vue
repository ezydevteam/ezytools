<template>
    <section v-if="content" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
        <div class="p-6 md:p-8">
            <!-- Header with lang toggle -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center">
                        <BookOpenIcon class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white">
                        {{ title }}
                    </h2>
                </div>
                <!-- Language toggle -->
                <div class="flex gap-1 bg-surface-100 dark:bg-surface-700 rounded-lg p-1">
                    <button @click="emit('toggle-lang', 'en')" :class="lang === 'en' ? 'bg-white dark:bg-surface-600 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500'" class="px-3 py-1 text-sm rounded-md transition-all font-medium">
                        English
                    </button>
                    <button @click="emit('toggle-lang', 'bn')" :class="lang === 'bn' ? 'bg-white dark:bg-surface-600 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500'" class="px-3 py-1 text-sm rounded-md transition-all font-medium">
                        বাংলা
                    </button>
                </div>
            </div>

            <!-- Numbered steps -->
            <ol v-if="steps.length" class="space-y-4 mb-6">
                <li v-for="(step, i) in steps" :key="i" class="flex gap-4 p-4 bg-surface-50 dark:bg-surface-900/50 rounded-xl border border-surface-100 dark:border-surface-700 transition-all hover:border-primary-200 dark:hover:border-primary-800">
                    <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-primary-500 to-purple-500 text-white rounded-xl flex items-center justify-center text-sm font-bold shadow-sm">
                        {{ i + 1 }}
                    </div>
                    <div>
                        <p class="font-semibold text-surface-900 dark:text-white">
                            {{ lang === 'bn' ? step.step : (step.step_en || step.step) }}
                        </p>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                            {{ lang === 'bn' ? step.description : (step.description_en || step.description) }}
                        </p>
                    </div>
                </li>
            </ol>

            <!-- Markdown body -->
            <div v-if="renderedBody" class="seo-content-rich-text max-w-none text-surface-600 dark:text-surface-300" v-html="renderedBody" />

            <!-- Use Cases -->
            <div v-if="useCases.length" class="mt-8">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-4">
                    {{ lang === 'bn' ? 'ব্যবহারের ক্ষেত্র' : 'Use Cases' }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="(uc, i) in useCases" :key="i" class="p-4 bg-surface-50 dark:bg-surface-900/50 rounded-xl border border-surface-100 dark:border-surface-700">
                        <h4 class="font-semibold text-surface-900 dark:text-white text-sm">
                            {{ lang === 'bn' ? uc.title : (uc.title_en || uc.title) }}
                        </h4>
                        <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">
                            {{ lang === 'bn' ? uc.description : (uc.description_en || uc.description) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Last updated -->
            <p v-if="content.last_updated_at" class="text-xs text-surface-400 mt-6 pt-4 border-t border-surface-100 dark:border-surface-700">
                {{ lang === 'bn' ? 'সর্বশেষ আপডেট:' : 'Last updated:' }} {{ formatDate(content.last_updated_at) }}
            </p>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { BookOpenIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    content: Object,
    lang: { type: String, default: 'en' },
});

const emit = defineEmits(['toggle-lang']);

const title = computed(() =>
    props.lang === 'bn'
        ? (props.content?.how_to_title || 'How to Use')
        : (props.content?.how_to_title_en || 'How to Use')
);

const body = computed(() =>
    props.lang === 'bn'
        ? props.content?.how_to_content
        : props.content?.how_to_content_en
);

const steps = computed(() => props.content?.how_to_steps ?? []);
const useCases = computed(() => props.content?.use_cases ?? []);

const renderedBody = computed(() => {
    if (!body.value) return '';
    // Simple markdown: bold, italic, links, paragraphs
    return body.value
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-primary-600 dark:text-primary-400 hover:underline">$1</a>')
        .replace(/\n\n/g, '</p><p class="mb-4">')
        .replace(/^/, '<p class="mb-4">')
        .replace(/$/, '</p>');
});

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString(props.lang === 'bn' ? 'bn-BD' : 'en-US', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
};
</script>
