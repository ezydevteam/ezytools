<template>
    <AppLayout>
        <Head>
            <title>{{ meta.title }}</title>
            <meta name="description" :content="meta.description" />
            <meta name="keywords" :content="meta.keywords" />
            <link rel="canonical" :href="meta.canonical" />
        </Head>

        <!-- Hero Section -->
        <div class="text-center pt-16 pb-12 px-4 mx-auto relative overflow-hidden">
            <!-- Background Floating Shapes (TinyWow Style) -->
            <div class="absolute top-10 left-[10%] text-pink-400 opacity-60 animate-[float_4s_ease-in-out_infinite] hidden sm:block">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 22h20L12 2z"/></svg>
            </div>
            <div class="absolute top-20 right-[10%] text-blue-400 opacity-60 animate-[float_3s_ease-in-out_infinite_reverse] hidden sm:block">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div class="absolute bottom-10 left-[20%] text-yellow-400 opacity-60 animate-[float_5s_ease-in-out_infinite] hidden sm:block">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="4" width="16" height="16" rx="4"/></svg>
            </div>
            <div class="absolute bottom-20 right-[10%] text-green-400 opacity-60 animate-[float_4.5s_ease-in-out_infinite_reverse] hidden sm:block">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <div class="absolute top-1/2 left-[5%] text-purple-400 opacity-50 animate-[pulseSoft_2s_ease-in-out_infinite] hidden lg:block">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12,2 15,9 22,9 16,14 18,21 12,17 6,21 8,14 2,9 9,9"/></svg>
            </div>

            <h1 class="text-4xl md:text-5xl lg:text-[56px] font-extrabold text-surface-900 dark:text-white tracking-tight leading-tight mb-6 relative z-10 px-2">
                Free online tools for
                <span class="relative inline-block w-[220px] md:w-[320px] h-[52px] md:h-[72px] align-middle overflow-hidden rounded-xl ml-2 -mt-2">
                    <transition name="slide-down">
                        <span :key="currentHighlight.text"
                        class="absolute inset-0 flex items-center justify-center bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600 w-full h-full pb-1 md:pb-2">
                            {{ currentHighlight.text }}
                        </span>
                    </transition>
                </span>
                <!-- Hidden crawlable text for SEO (all rotating keywords) -->
                <span class="sr-only">everyone, students, creators, developers</span>
            </h1>
            <p class="text-lg md:text-xl text-surface-500 dark:text-surface-400 mb-10 font-bangla max-w-3xl mx-auto leading-relaxed relative z-10 px-4">
                We offer AI, PDF, Video, Image and other 150+ powerful online tools to simplify your daily tasks.<br>
            </p>

            <!-- Search Box -->
            <div class="max-w-2xl mx-auto mb-16 relative z-10">
                <form @submit.prevent="openSearchModal" class="relative flex items-center bg-white dark:bg-surface-800 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.08)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.3)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.12)] transition-shadow p-2 pl-6" @click="openSearchModal">
                    <MagnifyingGlassIcon class="w-6 h-6 text-surface-400 shrink-0" />
                    <input type="text" readonly
                           class="w-full bg-transparent border-0 focus:ring-0 text-surface-900 dark:text-white placeholder-surface-400 py-3 px-4 text-lg cursor-text"
                           placeholder="Search" />
                    <button type="button" class="bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white px-10 py-3.5 rounded-full font-bold text-base transition-colors shrink-0 shadow-sm">
                        Search
                    </button>
                </form>
            </div>

            <!-- Big Category Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-20 text-left">
                 <!-- AI Write -->
                 <Link :href="route('tools.category', 'ai-tools')" class="group rounded-[20px] overflow-hidden shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex flex-col h-40">
                     <div class="bg-blue-50 dark:bg-blue-900/20 p-5 text-blue-700 dark:text-blue-300 flex-1 relative overflow-hidden group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40 transition-colors">
                         <div class="text-center mx-auto w-10 h-10 bg-blue-200 dark:bg-blue-800/50 rounded-full flex items-center justify-center mb-3 text-blue-600 dark:text-blue-400">
                             <DocumentTextIcon class="w-5 h-5" />
                         </div>
                         <h3 class="font-bold text-lg mb-0.5 relative z-10">AI Tools</h3>
                         <p class="text-blue-600/70 dark:text-blue-300/70 text-[11px] relative z-10">Solve Your Text Problems</p>
                         <ArrowRightIcon class="w-4 h-4 absolute bottom-5 right-5 text-blue-300 dark:text-blue-700 group-hover:text-blue-600 dark:group-hover:text-blue-400 group-hover:translate-x-1 transition-all" />
                     </div>
                     <div class="bg-white dark:bg-surface-800 p-3 text-[10px] font-bold text-surface-400 flex items-center justify-between">
                         <span>Featured Tool</span>
                         <span class="text-[#2196f3] truncate ml-2">Paragraph Writer</span>
                     </div>
                 </Link>

                 <!-- File Tools -->
                 <Link :href="route('tools.category', 'file-tools')" class="group rounded-[20px] overflow-hidden shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex flex-col h-40 hidden lg:flex">
                     <div class="bg-teal-50 dark:bg-teal-900/20 p-5 text-teal-700 dark:text-teal-300 flex-1 relative overflow-hidden group-hover:bg-teal-100 dark:group-hover:bg-teal-900/40 transition-colors">
                         <div class="text-center mx-auto w-10 h-10 bg-teal-200 dark:bg-teal-800/50 rounded-full flex items-center justify-center mb-3 text-teal-600 dark:text-teal-400">
                             <FolderIcon class="w-5 h-5" />
                         </div>
                         <h3 class="font-bold text-lg mb-0.5 relative z-10">File Tools</h3>
                         <p class="text-teal-600/70 dark:text-teal-300/70 text-[11px] relative z-10">Solve Your File Problems</p>
                         <ArrowRightIcon class="w-4 h-4 absolute bottom-5 right-5 text-teal-300 dark:text-teal-700 group-hover:text-teal-600 dark:group-hover:text-teal-400 group-hover:translate-x-1 transition-all" />
                     </div>
                     <div class="bg-white dark:bg-surface-800 p-3 text-[10px] font-bold text-surface-400 flex items-center justify-between">
                         <span>Featured Tool</span>
                         <span class="text-teal-600 dark:text-teal-400 truncate ml-2">Format Converter</span>
                     </div>
                 </Link>

                 <!-- PDF Tools -->
                 <Link :href="route('tools.category', 'business-tools')" class="group rounded-[20px] overflow-hidden shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex flex-col h-40">
                     <div class="bg-purple-50 dark:bg-purple-900/20 p-5 text-purple-700 dark:text-purple-300 flex-1 relative overflow-hidden group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors">
                         <div class="text-center mx-auto w-10 h-10 bg-purple-200 dark:bg-purple-800/50 rounded-full flex items-center justify-center mb-3 text-purple-600 dark:text-purple-400">
                             <DocumentIcon class="w-5 h-5" />
                         </div>
                         <h3 class="font-bold text-lg mb-0.5 relative z-10">Business Tools</h3>
                         <p class="text-purple-600/70 dark:text-purple-300/70 text-[11px] relative z-10">Solve Your Business Problems</p>
                         <ArrowRightIcon class="w-4 h-4 absolute bottom-5 right-5 text-purple-300 dark:text-purple-700 group-hover:text-purple-600 dark:group-hover:text-purple-400 group-hover:translate-x-1 transition-all" />
                     </div>
                     <div class="bg-white dark:bg-surface-800 p-3 text-[10px] font-bold text-surface-400 flex items-center justify-between">
                         <span>Featured Tool</span>
                         <span class="text-[#7c4dff] truncate ml-2">PDF to JPG</span>
                     </div>
                 </Link>

                 <!-- Web Tools -->
                 <Link :href="route('tools.category', 'web-tools')" class="group rounded-[20px] overflow-hidden shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex flex-col h-40">
                     <div class="bg-orange-50 dark:bg-orange-900/20 p-5 text-orange-700 dark:text-orange-300 flex-1 relative overflow-hidden group-hover:bg-orange-100 dark:group-hover:bg-orange-900/40 transition-colors">
                         <div class="text-center mx-auto w-10 h-10 bg-orange-200 dark:bg-orange-800/50 rounded-full flex items-center justify-center mb-3 text-orange-600 dark:text-orange-400">
                             <PhotoIcon class="w-5 h-5" />
                         </div>
                         <h3 class="font-bold text-lg mb-0.5 relative z-10">Web Tools</h3>
                         <p class="text-orange-600/70 dark:text-orange-300/70 text-[11px] relative z-10">Solve Your Web Problems</p>
                         <ArrowRightIcon class="w-4 h-4 absolute bottom-5 right-5 text-orange-300 dark:text-orange-700 group-hover:text-orange-600 dark:group-hover:text-orange-400 group-hover:translate-x-1 transition-all" />
                     </div>
                     <div class="bg-white dark:bg-surface-800 p-3 text-[10px] font-bold text-surface-400 flex items-center justify-between">
                         <span>Featured Tool</span>
                         <span class="text-[#ff9800] truncate ml-2">Site Map Generator</span>
                     </div>
                 </Link>

                 <!-- Developer Tools -->
                 <Link :href="route('tools.category', 'developer-tools')" class="group rounded-[20px] overflow-hidden shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 flex flex-col h-40">
                     <div class="bg-pink-50 dark:bg-pink-900/20 p-5 text-pink-700 dark:text-pink-300 flex-1 relative overflow-hidden group-hover:bg-pink-100 dark:group-hover:bg-pink-900/40 transition-colors">
                         <div class="text-center mx-auto w-10 h-10 bg-pink-200 dark:bg-pink-800/50 rounded-full flex items-center justify-center mb-3 text-pink-600 dark:text-pink-400">
                             <CodeBracketIcon class="w-5 h-5" />
                         </div>
                         <h3 class="font-bold text-lg mb-0.5 relative z-10">Developer Tools</h3>
                         <p class="text-pink-600/70 dark:text-pink-300/70 text-[11px] relative z-10">Solve Your Developer Problems</p>
                         <ArrowRightIcon class="w-4 h-4 absolute bottom-5 right-5 text-pink-300 dark:text-pink-700 group-hover:text-pink-600 dark:group-hover:text-pink-400 group-hover:translate-x-1 transition-all" />
                     </div>
                     <div class="bg-white dark:bg-surface-800 p-3 text-[10px] font-bold text-surface-400 flex items-center justify-between">
                         <span>Featured Tool</span>
                         <span class="text-[#e91e63] truncate ml-2">JSON Formatter</span>
                     </div>
                 </Link>
            </div>

            <!-- Stats -->
            <div class="bg-surface-100/50 dark:bg-surface-800/30 rounded-[32px] py-6 px-3 flex flex-wrap items-center justify-around gap-12 md:gap-24 mb-10">
                <div class="text-center">
                    <p class="text-4xl md:text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600 mb-1">20k+</p>
                    <p class="text-xs font-bold text-surface-500 uppercase tracking-widest">Active<br>Users</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl md:text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600 mb-1">100k+</p>
                    <p class="text-xs font-bold text-surface-500 uppercase tracking-widest">Files<br>Processed</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl md:text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600 mb-1">150+</p>
                    <p class="text-xs font-bold text-surface-500 uppercase tracking-widest">Online<br>Tools</p>
                </div>
                <div class="text-center">
                    <p class="text-4xl md:text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600 mb-1">50k+</p>
                    <p class="text-xs font-bold text-surface-500 uppercase tracking-widest">PDFs<br>Created</p>
                </div>
            </div>
        </div>

        <AdBanner :ad="ads?.header" class="mb-16" />

        <!-- Popular Tools Section -->
        <div class="max-w-6xl mx-auto px-4 pb-20">
            <div class="text-center mb-10">
                <h2 class="text-3xl md:text-[40px] font-extrabold text-surface-900 dark:text-white mb-3 tracking-tight">Our Most <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600">Popular</span> Tools</h2>
                <p class="text-surface-500 dark:text-surface-400 text-lg">We present the best of the best. All free, no catch</p>
            </div>

            <!-- Category Pill Filter -->
            <div class="relative max-w-4xl mx-auto mb-12 flex items-center px-10 sm:px-14">
                <!-- Left Scroll Button -->
                <button v-show="showLeftScroll" @click="scrollCategories(-200)" class="absolute left-0 top-1/2 -translate-y-1/2 z-20 bg-white dark:bg-surface-800 p-2 md:p-3 rounded-full shadow-md border border-surface-100 dark:border-surface-700 text-surface-500 hover:text-[#2196F3] flex items-center justify-center transition-colors">
                    <ChevronLeftIcon class="w-5 h-5" />
                </button>

                <!-- Scrollable Container -->
                <div ref="categoryScrollContainer" @scroll="handleScroll" class="flex-1 flex overflow-x-auto hide-scrollbar snap-x gap-2 bg-white dark:bg-surface-800 p-2 rounded-full shadow-sm border border-surface-100 dark:border-surface-700 scroll-smooth px-3 mx-auto w-fit">
                    <button @click="activeCategory = 'all'"
                            :class="[activeCategory === 'all' ? 'bg-gradient-to-r from-primary-600 to-purple-600 text-white shadow-md' : 'text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700']"
                            class="shrink-0 snap-start px-5 py-2.5 rounded-full text-sm font-semibold transition-all flex items-center gap-2">
                        <Squares2X2Icon class="w-4 h-4" /> All Tools
                    </button>
                    <button v-for="category in filteredCategories" :key="category.id"
                            @click="activeCategory = category.slug"
                            :class="[activeCategory === category.slug ? 'bg-gradient-to-r from-primary-600 to-purple-600 text-white shadow-md' : 'text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700']"
                            class="shrink-0 snap-start px-5 py-2.5 rounded-full text-sm font-semibold transition-all flex items-center gap-2">
                        <component :is="getCategoryIcon(category)" class="w-4 h-4" />
                        {{ category.name }}
                    </button>
                </div>

                <!-- Right Scroll Button -->
                <button v-show="showRightScroll" @click="scrollCategories(200)" class="absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-white dark:bg-surface-800 p-2 md:p-3 rounded-full shadow-md border border-surface-100 dark:border-surface-700 text-surface-500 hover:text-[#2196F3] flex items-center justify-center transition-colors">
                    <ChevronRightIcon class="w-5 h-5" />
                </button>
            </div>

            <!-- Tools Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <ToolCard v-for="tool in popularFilteredTools" :key="tool.id" :tool="tool" />
            </div>

            <!-- View All Tools Button -->
            <div v-if="activeCategory === 'all'" class="mt-10 flex justify-center">
                <Link :href="route('tools.index')" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white dark:bg-surface-800 text-surface-900 dark:text-white border border-surface-200 dark:border-surface-700 rounded-full font-semibold hover:border-primary-500 hover:text-primary-600 dark:hover:border-primary-500 dark:hover:text-primary-400 transition-all shadow-sm group">
                    View All Tools
                    <ArrowRightIcon class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </Link>
            </div>
        </div>

        <AdBanner :ad="ads?.middle" class="mb-16" />
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ToolCard from '@/Components/Tools/ToolCard.vue';
import AdBanner from '@/Components/Ads/AdBanner.vue';
import { MagnifyingGlassIcon, ArrowRightIcon, DocumentIcon, PhotoIcon, CodeBracketIcon, DocumentTextIcon, FolderIcon, Squares2X2Icon, ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';
import * as HeroIcons from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: Array,
    popularTools: Array,
    allTools: Array,
    ads: Object,
    meta: Object,
});

const activeCategory = ref('all');

// Text cycling logic — each word paired with a unique color
const highlightItems = [
    { text: 'students', color: '#E91E63' },
    { text: 'businesses', color: '#7c4dff' },
    { text: 'creators', color: '#ff9800' },
    { text: 'developers', color: '#2196F3' },
];
const currentHighlight = ref(highlightItems[0]);
const currentHighlightColor = ref(highlightItems[0].color);
let highlightIndex = 0;
let highlightInterval = null;

// Category scroll logic
const categoryScrollContainer = ref(null);
const showLeftScroll = ref(false);
const showRightScroll = ref(false);

const handleScroll = () => {
    if (!categoryScrollContainer.value) return;
    const { scrollLeft, scrollWidth, clientWidth } = categoryScrollContainer.value;
    showLeftScroll.value = scrollLeft > 12;
    showRightScroll.value = Math.ceil(scrollLeft + clientWidth) < scrollWidth - 12;
};

const scrollCategories = (amount) => {
    if (categoryScrollContainer.value) {
        categoryScrollContainer.value.scrollBy({ left: amount, behavior: 'smooth' });
    }
};

onMounted(() => {
    // Initial check for scroll buttons
    setTimeout(() => {
        handleScroll();
    }, 100);

    // Resize listener to re-evaluate scroll buttons
    window.addEventListener('resize', handleScroll);

    highlightInterval = setInterval(() => {
        highlightIndex = (highlightIndex + 1) % highlightItems.length;
        currentHighlight.value = highlightItems[highlightIndex];
        currentHighlightColor.value = highlightItems[highlightIndex].color;
    }, 2500);
});

onUnmounted(() => {
    if (highlightInterval) clearInterval(highlightInterval);
    window.removeEventListener('resize', handleScroll);
});

const openSearchModal = () => {
    window.dispatchEvent(new Event('open-search'));
};

const getCategoryIcon = (category) => {
    const iconMap = {
        'calculators': HeroIcons.CalculatorIcon,
        'converters': HeroIcons.ArrowsRightLeftIcon,
        'text-tools': HeroIcons.DocumentTextIcon,
        'developer-tools': HeroIcons.CodeBracketIcon,
        'image-tools': HeroIcons.PhotoIcon,
        'pdf-tools': HeroIcons.DocumentIcon,
        'seo-tools': HeroIcons.GlobeAltIcon,
        'generators': HeroIcons.SparklesIcon,
        'finance-tools': HeroIcons.BanknotesIcon,
        'ai-write': HeroIcons.DocumentTextIcon,
        'video-tools': HeroIcons.VideoCameraIcon,
    };
    return iconMap[category.slug] || HeroIcons.WrenchScrewdriverIcon;
};

// Filter categories to only show active ones
const filteredCategories = computed(() => {
    return props.categories || [];
});

// Filter tools based on active pill
const popularFilteredTools = computed(() => {
    if (activeCategory.value === 'all') {
        return props.popularTools.slice(0, 12); // Show top 12 overall
    }
    return (props.allTools || []).filter(t => t.category?.slug === activeCategory.value);
});
</script>

<style>
/* Hide scrollbar for category filter but keep functionality */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

/* Slide Down Transition */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-100%);
}
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(100%);
}
</style>
