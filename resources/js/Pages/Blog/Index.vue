<template>
    <AppLayout title="Blog — EzyTools">
        <Head>
            <meta name="description" content="Read the latest articles, tips, and tutorials from EzyTools. Stay updated on AI tools, web development, and productivity." />
        </Head>

        <!-- Hero -->
        <section class="text-center py-12 md:py-16">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-xs font-semibold mb-4">
                <BookOpenIcon class="w-4 h-4" />
                Blog
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">Latest Articles</h1>
            <p class="text-surface-600 dark:text-surface-400 max-w-lg mx-auto">Tips, tutorials, and insights from the EzyTools team.</p>
        </section>

        <!-- Ad Top -->
        <div v-if="ads.top" class="mb-8 flex justify-center" v-html="ads.top.code"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Posts Grid -->
            <div class="lg:col-span-2">
                <div v-if="posts.data.length === 0" class="text-center py-20">
                    <BookOpenIcon class="w-12 h-12 mx-auto mb-4 text-surface-300" />
                    <p class="text-surface-500 font-medium">No articles published yet.</p>
                    <p class="text-xs text-surface-400 mt-1">Check back soon for new content!</p>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <Link v-for="post in posts.data" :key="post.id" :href="route('blog.show', post.slug)"
                        class="group bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <!-- Image -->
                        <div class="aspect-[16/9] overflow-hidden bg-surface-100 dark:bg-surface-700">
                            <img v-if="post.featured_image" :src="'/storage/' + post.featured_image" :alt="post.title"
                                loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <DocumentTextIcon class="w-12 h-12 text-surface-300" />
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-[10px] text-surface-400 font-medium">{{ formatDate(post.published_at) }}</span>
                                <span class="text-surface-300">·</span>
                                <span class="text-[10px] text-surface-400">{{ post.reading_time }} min read</span>
                            </div>
                            <h2 class="text-base font-bold text-surface-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2">{{ post.title }}</h2>
                            <p v-if="post.excerpt" class="text-xs text-surface-500 dark:text-surface-400 line-clamp-3 leading-relaxed">{{ post.excerpt }}</p>
                            <div class="flex items-center gap-2 mt-4">
                                <div class="w-6 h-6 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 text-[10px] font-bold">
                                    {{ post.author?.name?.charAt(0) || 'E' }}
                                </div>
                                <span class="text-xs text-surface-500">{{ post.author?.name || 'EzyTools' }}</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="posts.last_page > 1" class="flex justify-center gap-1 mt-10">
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

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Ad Sidebar -->
                <div v-if="ads.sidebar" v-html="ads.sidebar.code"></div>

                <!-- Newsletter CTA -->
                <div class="bg-gradient-to-br from-primary-50 to-purple-50 dark:from-primary-950/30 dark:to-purple-950/20 rounded-2xl p-6 border border-primary-100 dark:border-primary-900/30 text-center">
                    <SparklesIcon class="w-8 h-8 text-primary-500 mx-auto mb-3" />
                    <h3 class="font-bold text-surface-900 dark:text-white mb-2 text-sm">Upgrade to Pro</h3>
                    <p class="text-xs text-surface-600 dark:text-surface-400 mb-4 leading-relaxed">Unlimited AI tools, no ads, and premium features.</p>
                    <Link :href="route('pricing')" class="inline-flex items-center gap-1 px-4 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-colors text-xs">
                        Get Pro
                    </Link>
                </div>

                <!-- Popular Tools -->
                <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                    <h3 class="font-bold text-surface-900 dark:text-white mb-4 text-sm">Popular Tools</h3>
                    <ul class="space-y-2">
                        <li><Link :href="route('tools.index')" class="text-xs text-primary-600 hover:underline">Browse All Tools →</Link></li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { BookOpenIcon, DocumentTextIcon, SparklesIcon } from '@heroicons/vue/24/outline';

defineProps({
    posts: Object,
    ads: Object,
});

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
};
</script>
