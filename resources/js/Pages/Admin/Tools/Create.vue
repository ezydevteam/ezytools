<template>
    <AdminLayout>
        <Head title="Create Tool" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tools.index')" class="text-surface-500 hover:text-surface-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <span>Create New Tool</span>
            </div>
        </template>

        <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div class="space-y-6 md:col-span-2">
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white border-b pb-2">Basic Information</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Name (English)</label>
                        <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Short Description</label>
                        <input type="text" v-model="form.short_description" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        <div v-if="form.errors.short_description" class="text-red-500 text-xs mt-1">{{ form.errors.short_description }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Slug</label>
                        <input type="text" v-model="form.slug" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        <div v-if="form.errors.slug" class="text-red-500 text-xs mt-1">{{ form.errors.slug }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Category</label>
                        <select v-model="form.category_id" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700">
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <div v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</div>
                    </div>



                    <!-- Technical & Limits -->
                    <div class="space-y-6 md:col-span-2 mt-4">
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white border-b pb-2">Technical & Limits</h3>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Component Name (Vue)</label>
                        <input type="text" v-model="form.component_name" placeholder="e.g. BanglaWordCounter" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Icon (Heroicon Outline Name)</label>
                        <input type="text" v-model="form.icon" placeholder="e.g. DocumentTextIcon" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Free Daily Limit</label>
                        <input type="number" v-model="form.daily_limit_free" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Pro Daily Limit (-1 for unlimited)</label>
                        <input type="number" v-model="form.daily_limit_pro" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.is_active" class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Is Active</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.is_premium" class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Is Premium (Pro Only)</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Order</label>
                        <input type="number" v-model="form.order" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                </div>

                <div class="flex items-center justify-end mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150">
                        Create Tool
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    categories: Array,
});

const form = useForm({
    category_id: '',
    name: '',
    short_description: '',
    slug: '',
    component_name: '',
    icon: '',
    is_active: true,
    is_premium: false,
    daily_limit_free: 10,
    daily_limit_pro: -1,
    meta_title: '',
    meta_description: '',
    meta_keywords: '',
    order: 0,
});

const submit = () => {
    form.post(route('admin.tools.store'));
};
</script>
