<template>
    <AppLayout title="Sitemap — EzyTools">
        <Head>
            <meta name="description" content="Browse the complete EzyTools sitemap. Find every tool, page, and resource available on our platform." />
        </Head>

        <div class="max-w-5xl mx-auto py-12 md:py-16">
            <!-- Header -->
            <div class="mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-xs font-semibold mb-4">
                    <MapIcon class="w-4 h-4" />
                    Navigation
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 dark:text-white mb-3">Sitemap</h1>
                <p class="text-surface-600 dark:text-surface-400">A complete directory of every page and tool on EzyTools.</p>
            </div>

            <!-- Main Pages -->
            <section class="mb-10">
                <h2 class="text-lg font-bold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <HomeIcon class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                    </div>
                    Main Pages
                </h2>
                <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                    <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        <li v-for="page in mainPages" :key="page.href">
                            <Link :href="page.href" class="flex items-center gap-2 text-sm text-surface-600 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors group">
                                <ChevronRightIcon class="w-3.5 h-3.5 text-surface-400 group-hover:text-primary-500 transition-colors" />
                                {{ page.label }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Tool Categories -->
            <section>
                <h2 class="text-lg font-bold text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <WrenchScrewdriverIcon class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    Tools by Category
                    <span class="text-xs font-normal text-surface-500 ml-1">({{ totalTools }} tools)</span>
                </h2>

                <div class="space-y-4">
                    <div v-for="cat in categories" :key="cat.id" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <!-- Category Header -->
                        <button @click="toggleCategory(cat.id)" class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <component :is="getIcon(cat.icon)" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                </div>
                                <div>
                                    <h3 class="font-bold text-surface-900 dark:text-white text-sm">{{ cat.name }}</h3>
                                    <p class="text-xs text-surface-500">{{ cat.tools?.length || 0 }} tools</p>
                                </div>
                            </div>
                            <ChevronDownIcon class="w-5 h-5 text-surface-400 transition-transform duration-200" :class="{ 'rotate-180': openCategories.includes(cat.id) }" />
                        </button>

                        <!-- Tool List -->
                        <transition enter-active-class="transition-all duration-200 ease-out" enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-[2000px]"
                                    leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 max-h-[2000px]" leave-to-class="opacity-0 max-h-0">
                            <div v-if="openCategories.includes(cat.id)" class="border-t border-surface-100 dark:border-surface-700 px-6 py-4">
                                <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                    <li v-for="tool in cat.tools" :key="tool.id">
                                        <Link :href="route('tools.show', { category: cat.slug, slug: tool.slug })"
                                            class="flex items-center gap-2 text-sm text-surface-600 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors group py-1">
                                            <ChevronRightIcon class="w-3 h-3 text-surface-400 group-hover:text-primary-500 transition-colors shrink-0" />
                                            <span class="truncate">{{ tool.name }}</span>
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </transition>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { MapIcon, HomeIcon, WrenchScrewdriverIcon, ChevronRightIcon, ChevronDownIcon,
    DocumentTextIcon, CalculatorIcon, CalendarIcon, FolderIcon, BriefcaseIcon,
    CodeBracketIcon, GlobeAltIcon, ArrowsRightLeftIcon, SparklesIcon,
    PhotoIcon, BanknotesIcon, VideoCameraIcon, DocumentIcon
} from '@heroicons/vue/24/outline';

const iconComponents = {
    DocumentTextIcon, CalculatorIcon, CalendarIcon, FolderIcon, BriefcaseIcon,
    CodeBracketIcon, GlobeAltIcon, ArrowsRightLeftIcon, SparklesIcon,
    PhotoIcon, BanknotesIcon, VideoCameraIcon, DocumentIcon, WrenchScrewdriverIcon,
};

const getIcon = (name) => {
    return iconComponents[name] || WrenchScrewdriverIcon;
};

const props = defineProps({
    categories: Array,
});

const openCategories = ref([]);

const toggleCategory = (id) => {
    const idx = openCategories.value.indexOf(id);
    if (idx === -1) {
        openCategories.value.push(id);
    } else {
        openCategories.value.splice(idx, 1);
    }
};

const totalTools = computed(() => {
    return props.categories?.reduce((sum, c) => sum + (c.tools?.length || 0), 0) || 0;
});

const mainPages = [
    { label: 'Home', href: '/' },
    { label: 'All Tools', href: '/tools' },
    { label: 'Pricing', href: '/pricing' },
    { label: 'About Us', href: '/about' },
    { label: 'Privacy Policy', href: '/privacy-policy' },
    { label: 'Terms of Service', href: '/terms-of-service' },
    { label: 'Contact Us', href: '/contact' },
    { label: 'Sitemap', href: '/sitemap' },
];
</script>
