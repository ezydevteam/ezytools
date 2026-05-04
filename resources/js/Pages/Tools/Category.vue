<template>
    <AppLayout>
        <Head>
            <title>{{ meta.title }}</title>
            <meta name="description" :content="meta.description" />
            <meta name="keywords" :content="meta.keywords" />
            <link rel="canonical" :href="meta.canonical" />
            <meta property="og:type" :content="meta.og_type" />
            <meta property="og:title" :content="meta.og_title" />
            <meta property="og:description" :content="meta.og_description" />
            <meta property="og:url" :content="meta.og_url" />
        </Head>

        <div class="mb-10 text-center">
            <nav class="flex text-surface-500 dark:text-surface-400 text-sm font-medium mb-6 justify-center" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <Link :href="route('home')" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</Link>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="mx-2 text-surface-300 dark:text-surface-600">/</span>
                            <Link :href="route('tools.index')" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Tools</Link>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <span class="mx-2 text-surface-300 dark:text-surface-600">/</span>
                            <span class="text-surface-900 dark:text-white font-semibold">{{ category.name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="w-full max-w-2xl mx-auto flex flex-col items-center">
                <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2 flex items-center justify-center gap-3">
                    <component :is="iconComponent" class="w-8 h-8 text-primary-500" v-if="iconComponent" />
                    {{ category.name }}
                </h1>
                <p class="text-surface-500 dark:text-surface-400 text-lg font-bangla mb-8">{{ category.description }}</p>

                <div class="relative w-full max-w-md mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon class="h-5 w-5 text-surface-400" aria-hidden="true" />
                    </div>
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="block w-full pl-11 pr-4 py-3.5 border border-surface-200 dark:border-surface-700 rounded-2xl leading-5 bg-white dark:bg-surface-800 text-surface-900 dark:text-white placeholder-surface-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm shadow-sm transition-shadow"
                        placeholder="Search tools in this category..."
                    />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <ToolCard v-for="tool in filteredTools" :key="tool.id" :tool="tool" />
        </div>

        <div v-if="filteredTools.length === 0" class="text-center py-16 bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700">
            <p class="text-surface-500 dark:text-surface-400">No tools found matching your search.</p>
        </div>

    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ToolCard from '@/Components/Tools/ToolCard.vue';
import * as HeroIcons from '@heroicons/vue/24/outline';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    category: Object,
    tools: Array,
    meta: Object,
});

const searchQuery = ref('');

const filteredTools = computed(() => {
    if (!searchQuery.value) return props.tools;
    const query = searchQuery.value.toLowerCase();
    return props.tools.filter(tool =>
        tool.name.toLowerCase().includes(query) ||
        (tool.short_description && tool.short_description.includes(query)) ||
        (tool.description && tool.description.toLowerCase().includes(query)) ||
        (tool.description_bn && tool.description_bn.includes(query))
    );
});

const iconComponent = computed(() => {
    if (props.category.icon && HeroIcons[props.category.icon]) {
        return HeroIcons[props.category.icon];
    }
    return null;
});
</script>
