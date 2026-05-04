<template>
    <section v-if="hasContent" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
        <div class="p-6 md:p-8">
            <!-- Header with lang toggle -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                        <InformationCircleIcon class="w-5 h-5" />
                    </div>
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white">
                        {{ title }}
                    </h2>
                </div>
                <!-- Language toggle -->
                <div class="flex gap-1 bg-surface-100 dark:bg-surface-700 rounded-lg p-1">
                    <button @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-white dark:bg-surface-600 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500'" class="px-3 py-1 text-sm rounded-md transition-all font-medium">
                        English
                    </button>
                    <button @click="activeLang = 'bn'" :class="activeLang === 'bn' ? 'bg-white dark:bg-surface-600 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500'" class="px-3 py-1 text-sm rounded-md transition-all font-medium">
                        বাংলা
                    </button>
                </div>
            </div>

            <!-- About body -->
            <div class="seo-content-rich-text max-w-none text-surface-600 dark:text-surface-300 leading-relaxed" v-html="renderedBody" />
        </div>
    </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { InformationCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    content: Object,
    lang: { type: String, default: 'en' },
});

const activeLang = ref(props.lang === 'bn' ? 'bn' : 'en');

const hasContent = computed(() => {
    return props.content?.about_content || props.content?.about_content_en;
});

const title = computed(() =>
    activeLang.value === 'bn'
        ? (props.content?.about_title || 'About This Tool')
        : (props.content?.about_title_en || 'About This Tool')
);

const body = computed(() =>
    activeLang.value === 'bn'
        ? (props.content?.about_content || props.content?.about_content_en)
        : (props.content?.about_content_en || props.content?.about_content)
);

const renderedBody = computed(() => {
    if (!body.value) return '';
    return body.value
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-primary-600 dark:text-primary-400 hover:underline">$1</a>')
        .replace(/\n\n/g, '</p><p class="mb-4">')
        .replace(/^/, '<p class="mb-4">')
        .replace(/$/, '</p>');
});
</script>
