<template>
    <AdminLayout>
        <Head title="Manage Tools" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <span>Tools Management</span>
                <Link :href="route('admin.tools.create')" class="inline-flex items-center ms-4 px-4 py-2 bg-gradient-to-r from-primary-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm">
                    + Add New Tool
                </Link>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Filters Bar -->
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search -->
                <div class="flex-1 relative">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-surface-400" />
                    <input type="text" v-model="searchQuery"
                           class="block w-full pl-10 pr-10 py-2.5 border border-surface-200 dark:border-surface-700 rounded-xl bg-white dark:bg-surface-800 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition-colors text-surface-900 dark:text-white"
                           placeholder="Search tools by name or component..." />
                    <button v-if="searchQuery" @click="searchQuery = ''" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-surface-400 hover:text-surface-600">
                        <XMarkIcon class="h-4 w-4" />
                    </button>
                </div>

                <!-- Category Filter -->
                <select v-model="activeCategory"
                        class="px-4 py-2.5 border border-surface-200 dark:border-surface-700 rounded-xl bg-white dark:bg-surface-800 text-sm text-surface-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="all">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                </select>

                <!-- Count -->
                <div class="flex items-center px-4 py-2 bg-surface-100 dark:bg-surface-800 rounded-xl text-sm text-surface-600 dark:text-surface-400 font-medium whitespace-nowrap">
                    {{ filteredTools.length }} tools
                </div>
            </div>

            <!-- Tools Table -->
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                        <thead class="bg-surface-50 dark:bg-surface-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Tool</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Status/Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Usage</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-surface-800 divide-y divide-surface-200 dark:divide-surface-700">
                            <tr v-for="tool in filteredTools" :key="tool.id" class="hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-surface-500 dark:text-surface-400">
                                    #{{ tool.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-surface-900 dark:text-white">{{ tool.name }}</div>
                                        <div class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ tool.component_name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="tool.category" class="text-xs font-medium px-2 py-1 rounded-full" :class="tool.category?.slug === 'ai-tools' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300'">
                                        {{ tool.category.name }}
                                    </span>
                                    <span v-else class="text-xs text-surface-400">—</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[tool.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400', 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full']">
                                        {{ tool.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span v-if="tool.is_premium" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                        Pro
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-surface-900 dark:text-white">
                                    {{ formatNumber(tool.usage_count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <Link v-if="tool.category?.slug === 'ai-tools'" :href="route('admin.ai.tools.config', tool.id)" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-purple-600 to-purple-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-purple-700 hover:to-purple-600 transition-all shadow-sm">Config</Link>
                                    <Link :href="route('admin.tools.seo', tool.id)" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-emerald-700 hover:to-teal-600 transition-all shadow-sm">SEO</Link>
                                    <Link :href="route('admin.tools.edit', tool.id)" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-primary-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm">Edit</Link>
                                    <button @click="deleteTool(tool)" class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-red-700 hover:to-red-600 transition-all shadow-sm">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="filteredTools.length === 0" class="p-8 text-center">
                    <MagnifyingGlassIcon class="mx-auto h-8 w-8 text-surface-300 dark:text-surface-600 mb-2" />
                    <p class="text-sm text-surface-500 dark:text-surface-400">No tools match your search criteria.</p>
                    <button @click="searchQuery = ''; activeCategory = 'all'" class="mt-2 text-xs text-primary-600 hover:text-primary-700 font-medium">Reset Filters</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tools: Array,
    categories: Array,
});

const searchQuery = ref('');
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
            (tool.component_name && tool.component_name.toLowerCase().includes(q)) ||
            (tool.slug && tool.slug.toLowerCase().includes(q))
        );
    }

    return result;
});

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num || 0);
};

const deleteTool = (tool) => {
    if (confirm(`Are you sure you want to delete ${tool.name}?`)) {
        router.delete(route('admin.tools.destroy', tool.id));
    }
};
</script>
