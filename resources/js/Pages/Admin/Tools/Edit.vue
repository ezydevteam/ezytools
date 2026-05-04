<template>
    <AdminLayout>
        <Head title="Edit Tool" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tools.index')" class="text-surface-500 hover:text-surface-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <span>Edit Tool: {{ tool.name }}</span>
            </div>
        </template>

        <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Form fields similar to Create.vue -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Name (English)</label>
                        <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Short Description</label>
                        <input type="text" v-model="form.short_description" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Slug</label>
                        <input type="text" v-model="form.slug" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Category</label>
                        <select v-model="form.category_id" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700">
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Component Name</label>
                        <input type="text" v-model="form.component_name" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Icon</label>
                        <input type="text" v-model="form.icon" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>
                    


                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Free Daily Limit</label>
                        <input type="number" v-model="form.daily_limit_free" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Pro Daily Limit</label>
                        <input type="number" v-model="form.daily_limit_pro" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.is_active" class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Is Active</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.is_premium" class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Is Premium</span>
                        </label>
                    </div>
                </div>

                <!-- SEO Fields -->
                <div class="border-t border-surface-200 dark:border-surface-700 pt-6 mt-6">
                    <h3 class="text-sm font-semibold text-surface-900 dark:text-white uppercase tracking-wider mb-4">SEO Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Meta Title</label>
                            <input type="text" v-model="form.meta_title" placeholder="Leave empty for auto-generated title" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                            <p class="text-xs text-surface-400 mt-1">Auto: {{ tool.name }} — Free Online Tool | EzyTools</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Meta Description</label>
                            <textarea v-model="form.meta_description" rows="2" placeholder="Leave empty to use SEO about content" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700"></textarea>
                            <p class="text-xs text-surface-400 mt-1">Max 160 characters recommended. Falls back to SEO about content.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Meta Keywords</label>
                            <input type="text" v-model="form.meta_keywords" placeholder="keyword1, keyword2, keyword3" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                            <p class="text-xs text-surface-400 mt-1">Comma-separated keywords for search engines.</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition">
                        Update Tool
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tool: Object,
    categories: Array,
});

const form = useForm({
    category_id: props.tool.category_id,
    name: props.tool.name,
    short_description: props.tool.short_description,
    slug: props.tool.slug,
    component_name: props.tool.component_name,
    icon: props.tool.icon,
    is_active: props.tool.is_active,
    is_premium: props.tool.is_premium,
    daily_limit_free: props.tool.daily_limit_free,
    daily_limit_pro: props.tool.daily_limit_pro,
    meta_title: props.tool.meta_title,
    meta_description: props.tool.meta_description,
    meta_keywords: props.tool.meta_keywords,
    order: props.tool.order,
});

const submit = () => {
    form.put(route('admin.tools.update', props.tool.id));
};
</script>
