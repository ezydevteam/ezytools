<template>
    <AdminLayout>
        <Head :title="isEditing ? 'Edit Post' : 'Create Post'" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <span>{{ isEditing ? 'Edit Post' : 'Create New Post' }}</span>
                <Link :href="route('admin.blog.index')" class="inline-flex items-center ms-4 px-4 py-2 bg-surface-100 dark:bg-surface-700 border border-transparent rounded-lg font-semibold text-xs text-surface-700 dark:text-surface-300 uppercase tracking-widest hover:bg-surface-200 dark:hover:bg-surface-600 transition-all">
                    ← Back to Posts
                </Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Post Title</label>
                        <input v-model="form.title" type="text" @input="autoSlug" required
                            class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-lg font-semibold p-4"
                            placeholder="Enter a compelling title..." />
                        <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                    </div>

                    <!-- Slug -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">URL Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-surface-400">/blog/</span>
                            <input v-model="form.slug" type="text"
                                class="flex-1 rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2"
                                placeholder="auto-generated-slug" />
                        </div>
                        <p v-if="form.errors.slug" class="text-red-500 text-xs mt-1">{{ form.errors.slug }}</p>
                    </div>

                    <!-- Excerpt -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Excerpt <span class="text-surface-400 font-normal">(optional)</span></label>
                        <textarea v-model="form.excerpt" rows="2" maxlength="500"
                            class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-4 resize-none"
                            placeholder="A short summary for listings and SEO..."></textarea>
                        <p class="text-xs text-surface-400 mt-1 text-right">{{ (form.excerpt || '').length }}/500</p>
                    </div>

                    <!-- Body -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Post Content</label>
                        <textarea v-model="form.body" rows="18" required
                            class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-4 resize-y font-mono"
                            placeholder="Write your blog content here... HTML is supported."></textarea>
                        <p v-if="form.errors.body" class="text-red-500 text-xs mt-1">{{ form.errors.body }}</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publish Settings -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white mb-4">Publish</h3>
                        <div class="space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input v-model="form.is_published" type="checkbox" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" />
                                <span class="text-sm text-surface-700 dark:text-surface-300">Publish immediately</span>
                            </label>
                            <button type="submit" :disabled="form.processing"
                                class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm">
                                <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                {{ isEditing ? 'Update Post' : (form.is_published ? 'Publish Post' : 'Save Draft') }}
                            </button>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white mb-4">Featured Image</h3>
                        <div v-if="imagePreview" class="mb-4 relative">
                            <img :src="imagePreview" class="w-full h-40 object-cover rounded-xl border border-surface-200 dark:border-surface-600" />
                            <button type="button" @click="removeImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-xl cursor-pointer hover:border-primary-500 transition-colors bg-surface-50 dark:bg-surface-900/50">
                            <svg class="w-8 h-8 text-surface-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 001.5-1.5V5.25a1.5 1.5 0 00-1.5-1.5H3.75a1.5 1.5 0 00-1.5 1.5V19.5a1.5 1.5 0 001.5 1.5z" /></svg>
                            <span class="text-xs text-surface-500">Click to upload</span>
                            <input type="file" class="hidden" accept="image/*" @change="handleImage" />
                        </label>
                    </div>

                    <!-- SEO -->
                    <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white mb-4">SEO Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-surface-500 mb-1">Meta Title</label>
                                <input v-model="form.meta_title" type="text" maxlength="255"
                                    class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5"
                                    placeholder="SEO title (defaults to post title)" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-500 mb-1">Meta Description</label>
                                <textarea v-model="form.meta_description" rows="3" maxlength="500"
                                    class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm p-2.5 resize-none"
                                    placeholder="SEO description (defaults to excerpt)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    post: { type: Object, default: null },
});

const isEditing = computed(() => !!props.post);

const form = useForm({
    title: props.post?.title || '',
    slug: props.post?.slug || '',
    excerpt: props.post?.excerpt || '',
    body: props.post?.body || '',
    featured_image: null,
    meta_title: props.post?.meta_title || '',
    meta_description: props.post?.meta_description || '',
    is_published: props.post?.is_published || false,
});

const imagePreview = ref(props.post?.featured_image ? '/storage/' + props.post.featured_image : null);

const autoSlug = () => {
    if (!isEditing.value) {
        form.slug = form.title
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
};

const handleImage = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.featured_image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.featured_image = null;
    imagePreview.value = null;
};

const submit = () => {
    if (isEditing.value) {
        form.post(route('admin.blog.update', props.post.id), {
            forceFormData: true,
            _method: 'PUT',
        });
    } else {
        form.post(route('admin.blog.store'), { forceFormData: true });
    }
};
</script>
