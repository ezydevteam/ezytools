<template>
    <AppLayout>
        <Head>
            <title>All Tools - EzyTools</title>
            <meta name="description" content="Browse our complete directory of 150+ free online tools, converters, calculators, AI tools, and utilities. Find exactly what you need at EzyTools." />
            <meta name="keywords" content="all tools, free online tools, converters, calculators, AI tools, PDF tools, image tools, video tools, EzyTools" />
            <link rel="canonical" :href="route('tools.index')" />
            <meta property="og:type" content="website" />
            <meta property="og:url" :content="route('tools.index')" />
            <meta property="og:title" content="All Tools — 150+ Free Online Tools | EzyTools" />
            <meta property="og:description" content="Browse our complete directory of 150+ free online tools, converters, calculators, AI tools, and utilities." />
        </Head>

        <!-- Page Header + Search -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white mb-1">All Tools</h1>
            <p class="text-sm text-surface-500 dark:text-surface-400 mb-5">Browse through our collection of {{ tools.length }} tools.</p>
            <div class="relative w-full sm:w-96 mx-auto">
                <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-surface-400" />
                <input type="text" v-model="searchQuery"
                       class="block w-full pl-10 pr-10 py-2.5 border border-surface-200 dark:border-surface-700 rounded-xl bg-white dark:bg-surface-800 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition-colors text-surface-900 dark:text-white"
                       placeholder="Search tools...">
                <button v-if="searchQuery" @click="clearSearch" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600">
                    <XMarkIcon class="h-4 w-4" />
                </button>
            </div>
        </div>
        <!-- Category Pills -->
        <div class="flex flex-wrap justify-center gap-1.5 mb-8">
            <button @click="activeCategory = 'all'"
                    :class="[activeCategory === 'all' ? 'bg-surface-900 dark:bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700']"
                    class="px-3 py-1.5 rounded-lg font-medium text-xs transition-colors">
                All
            </button>
            <button v-for="category in categories" :key="category.id"
                    @click="activeCategory = category.slug"
                    :class="[activeCategory === category.slug ? 'bg-surface-900 dark:bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700']"
                    class="px-3 py-1.5 rounded-lg font-medium text-xs transition-colors">
                {{ category.name }}
            </button>
        </div>

        <!-- Tools Grid -->
        <div v-if="filteredTools.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <ToolCard v-for="tool in filteredTools" :key="tool.id" :tool="tool" />
        </div>
        
        <!-- Empty State -->
        <div v-else class="text-center py-16 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
            <MagnifyingGlassIcon class="mx-auto h-10 w-10 text-surface-300 dark:text-surface-600 mb-3" />
            <h3 class="text-sm font-medium text-surface-900 dark:text-white">No tools found</h3>
            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                Try adjusting your search or filter criteria.
            </p>
            <button @click="clearSearch(); activeCategory = 'all'" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 transition-colors">
                Reset Filters
            </button>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ToolCard from '@/Components/Tools/ToolCard.vue';
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tools: Array,
    categories: Array,
    search: String,
});

const searchQuery = ref(props.search || '');
const activeCategory = ref('all');

const filteredTools = computed(() => {
    let result = props.tools;
    
    // Filter by category
    if (activeCategory.value !== 'all') {
        result = result.filter(t => t.category?.slug === activeCategory.value);
    }
    
    // Filter by search
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        result = result.filter(tool => 
            tool.name.toLowerCase().includes(q) || 
            (tool.description && tool.description.toLowerCase().includes(q)) ||
            (tool.short_description && tool.short_description.includes(searchQuery.value))
        );
    }
    
    return result;
});

const clearSearch = () => {
    searchQuery.value = '';
};
</script>
