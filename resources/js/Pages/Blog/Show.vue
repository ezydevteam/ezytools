<template>
    <AppLayout :title="post.meta_title || post.title + ' — EzyTools Blog'">
        <Head>
            <meta name="description" :content="post.meta_description || post.excerpt || ''" />
            <link rel="canonical" :href="route('blog.show', post.slug)" />
            <meta property="og:type" content="article" />
            <meta property="og:title" :content="post.meta_title || post.title" />
            <meta property="og:description" :content="post.meta_description || post.excerpt || ''" />
            <meta property="og:url" :content="route('blog.show', post.slug)" />
            <meta v-if="post.featured_image" property="og:image" :content="'/storage/' + post.featured_image" />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="post.meta_title || post.title" />
            <meta name="twitter:description" :content="post.meta_description || post.excerpt || ''" />
            <component v-for="(s, i) in schemas" :key="i" is="script" type="application/ld+json" v-text="JSON.stringify(s)" />
        </Head>

        <!-- Ad Top -->
        <div v-if="ads.top" class="mb-8 flex justify-center" v-html="ads.top.code"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Article -->
            <article class="lg:col-span-2 bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="p-6 md:p-10">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center gap-2 text-xs text-surface-400 mb-6">
                        <Link :href="route('home')" class="hover:text-primary-600 transition-colors">Home</Link>
                        <span>›</span>
                        <Link :href="route('blog.index')" class="hover:text-primary-600 transition-colors">Blog</Link>
                        <span>›</span>
                        <span class="text-surface-600 dark:text-surface-300 truncate max-w-[200px]">{{ post.title }}</span>
                    </nav>

                    <!-- Featured Image -->
                    <div v-if="post.featured_image" class="mb-8 rounded-2xl overflow-hidden border border-surface-200 dark:border-surface-700">
                        <img :src="'/storage/' + post.featured_image" :alt="post.title" loading="lazy" class="w-full h-auto max-h-[400px] object-cover" />
                    </div>

                    <!-- Header -->
                    <header class="mb-8">
                        <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white leading-tight mb-4">{{ post.title }}</h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-surface-500">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 text-sm font-bold">
                                    <img :src="'/storage/' + post.author?.avatar" :alt="post.author?.name" v-if="post.author?.avatar" loading="lazy" class="w-full h-full object-cover rounded-full">
                                    <span v-else>{{ post.author?.name?.charAt(0) || 'E' }}</span>
                                </div>
                                <span class="font-medium text-surface-700 dark:text-surface-300">{{ post.author?.name || 'EzyTools Team' }}</span>
                            </div>
                            <span class="text-surface-300 dark:text-surface-600">|</span>
                            <span>{{ formatDate(post.published_at) }}</span>
                            <span class="text-surface-300 dark:text-surface-600">|</span>
                            <span>{{ post.reading_time }} min read</span>
                        </div>
                    </header>

                    <!-- Body -->
                    <div class="blog-body text-surface-700 dark:text-surface-300 leading-relaxed" v-html="post.body"></div>
                </div>

                    <!-- Share -->
                    <div class="mt-10 pt-8 border-t border-surface-200 dark:border-surface-700 px-6 md:px-10 pb-8">
                        <p class="text-sm font-semibold text-surface-900 dark:text-white mb-3">Share this article</p>
                        <div class="flex gap-2">
                            <a :href="'https://twitter.com/intent/tweet?url=' + encodeURIComponent(currentUrl) + '&text=' + encodeURIComponent(post.title)" target="_blank" rel="noopener"
                                class="px-4 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-600 dark:text-surface-400 rounded-lg text-xs font-medium transition-colors">
                                𝕏 Twitter
                            </a>
                            <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl)" target="_blank" rel="noopener"
                                class="px-4 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-600 dark:text-surface-400 rounded-lg text-xs font-medium transition-colors">
                                Facebook
                            </a>
                            <button @click="copyLink" class="px-4 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-600 dark:text-surface-400 rounded-lg text-xs font-medium transition-colors">
                                {{ copied ? '✓ Copied' : 'Copy Link' }}
                            </button>
                        </div>
                    </div>
            </article>

            <!-- Sidebar -->
            <aside class="space-y-6">
                <!-- Ad Sidebar -->
                <div v-if="ads.sidebar" v-html="ads.sidebar.code"></div>

                <!-- Related Posts -->
                <div v-if="relatedPosts.length > 0" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                    <h3 class="font-bold text-surface-900 dark:text-white mb-4 text-sm">Related Articles</h3>
                    <div class="space-y-4">
                        <Link v-for="rp in relatedPosts" :key="rp.id" :href="route('blog.show', rp.slug)"
                            class="flex gap-3 group">
                            <div class="w-16 h-12 rounded-lg overflow-hidden shrink-0 bg-surface-100 dark:bg-surface-700">
                                <img v-if="rp.featured_image" :src="'/storage/' + rp.featured_image" :alt="rp.title" loading="lazy" class="w-full h-full object-cover" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2">{{ rp.title }}</p>
                                <p class="text-[10px] text-surface-400 mt-0.5">{{ formatDate(rp.published_at) }}</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Back to Blog -->
                <Link :href="route('blog.index')" class="block text-center bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 rounded-2xl p-4 text-sm font-medium text-surface-600 dark:text-surface-400 transition-colors border border-surface-200 dark:border-surface-700">
                    ← All Articles
                </Link>
            </aside>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    post: Object,
    relatedPosts: Array,
    ads: Object,
    schemas: { type: Array, default: () => [] },
});

const copied = ref(false);
const currentUrl = computed(() => window.location.href);

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
};

const copyLink = () => {
    navigator.clipboard.writeText(currentUrl.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
};
</script>

<style scoped>
.blog-body :deep(h2) {
    font-size: 1.35rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    color: var(--color-surface-900);
}
.blog-body :deep(h3) {
    font-size: 1.15rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--color-surface-900);
}
:is(.dark) .blog-body :deep(h2),
:is(.dark) .blog-body :deep(h3) {
    color: #fff;
}
.blog-body :deep(p) {
    margin-bottom: 1rem;
}
.blog-body :deep(a) {
    color: var(--color-primary-600);
    text-decoration: none;
    font-weight: 500;
}
.blog-body :deep(a:hover) {
    text-decoration: underline;
}
:is(.dark) .blog-body :deep(a) {
    color: var(--color-primary-400);
}
.blog-body :deep(ul),
.blog-body :deep(ol) {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}
.blog-body :deep(ul) {
    list-style-type: disc;
}
.blog-body :deep(ol) {
    list-style-type: decimal;
}
.blog-body :deep(li) {
    margin-bottom: 0.4rem;
}
.blog-body :deep(strong) {
    font-weight: 600;
    color: var(--color-surface-900);
}
:is(.dark) .blog-body :deep(strong) {
    color: #fff;
}
.blog-body :deep(img) {
    border-radius: 0.75rem;
    margin: 1.5rem 0;
}
</style>
