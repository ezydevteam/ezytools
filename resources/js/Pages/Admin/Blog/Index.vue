<template>
    <AdminLayout>
        <Head title="Blog Management" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <span>Blog Posts</span>
                <Link :href="route('admin.blog.create')" class="inline-flex items-center ms-4 px-4 py-2 bg-gradient-to-r from-primary-600 to-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm">
                    + New Post
                </Link>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ posts.total }}</p>
                    <p class="text-xs text-surface-500">Total Posts</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ publishedCount }}</p>
                    <p class="text-xs text-surface-500">Published</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ draftCount }}</p>
                    <p class="text-xs text-surface-500">Drafts</p>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4 text-center">
                    <p class="text-2xl font-bold text-primary-600">{{ posts.data.length }}</p>
                    <p class="text-xs text-surface-500">This Page</p>
                </div>
            </div>

            <!-- Posts Table -->
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                        <thead class="bg-surface-50 dark:bg-surface-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Post</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-surface-700">
                            <tr v-for="post in posts.data" :key="post.id" class="hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors">
                                <td class="px-6 py-4 text-xs text-surface-400 font-mono">#{{ post.id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img v-if="post.featured_image" :src="'/storage/' + post.featured_image" class="w-12 h-8 rounded-lg object-cover border border-surface-200 dark:border-surface-600" />
                                        <div v-else class="w-12 h-8 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center">
                                            <DocumentTextIcon class="w-4 h-4 text-surface-400" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-surface-900 dark:text-white truncate max-w-[250px]">{{ post.title }}</p>
                                            <p class="text-xs text-surface-400 truncate max-w-[250px]">/blog/{{ post.slug }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-surface-600 dark:text-surface-400">{{ post.author?.name || '—' }}</td>
                                <td class="px-6 py-4">
                                    <span v-if="post.is_published" class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Published</span>
                                    <span v-else class="px-2 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Draft</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-surface-500">{{ formatDate(post.published_at || post.created_at) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a v-if="post.is_published" :href="route('blog.show', post.slug)" target="_blank" class="p-1.5 text-surface-400 hover:text-blue-600 transition-colors" title="View">
                                            <EyeIcon class="w-4 h-4" />
                                        </a>
                                        <Link :href="route('admin.blog.edit', post.id)" class="p-1.5 text-surface-400 hover:text-primary-600 transition-colors" title="Edit">
                                            <PencilIcon class="w-4 h-4" />
                                        </Link>
                                        <button @click="deletePost(post)" class="p-1.5 text-surface-400 hover:text-red-600 transition-colors" title="Delete">
                                            <TrashIcon class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="posts.data.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-surface-500">
                                    <DocumentTextIcon class="w-10 h-10 mx-auto mb-3 text-surface-300" />
                                    <p class="font-medium">No blog posts yet</p>
                                    <p class="text-xs mt-1">Create your first post to get started.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="posts.last_page > 1" class="flex justify-center gap-1">
                <Link v-for="link in posts.links" :key="link.label"
                    :href="link.url || '#'"
                    :class="[
                        'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                        link.active ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700 border border-surface-200 dark:border-surface-700',
                        !link.url ? 'opacity-50 pointer-events-none' : ''
                    ]"
                    v-html="link.label" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { DocumentTextIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    posts: Object,
});

const publishedCount = computed(() => props.posts.data.filter(p => p.is_published).length);
const draftCount = computed(() => props.posts.data.filter(p => !p.is_published).length);

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const deletePost = (post) => {
    if (confirm(`Delete "${post.title}"? This cannot be undone.`)) {
        router.delete(route('admin.blog.destroy', post.id));
    }
};
</script>
