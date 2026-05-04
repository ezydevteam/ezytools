<template>
    <AppLayout>
        <Head>
            <title>{{ meta.title }}</title>
            <meta name="description" :content="meta.description" />
            <meta name="keywords" :content="meta.keywords" />

            <!-- OpenGraph / Facebook -->
            <meta property="og:type" :content="meta.og_type" />
            <meta property="og:url" :content="meta.og_url" />
            <meta property="og:title" :content="meta.og_title" />
            <meta property="og:description" :content="meta.og_description" />
            <meta property="og:image" :content="meta.og_image" />

            <!-- Twitter -->
            <meta property="twitter:card" :content="meta.twitter_card" />
            <meta property="twitter:url" :content="meta.og_url" />
            <meta property="twitter:title" :content="meta.og_title" />
            <meta property="twitter:description" :content="meta.og_description" />
            <meta property="twitter:image" :content="meta.og_image" />

            <link rel="canonical" :href="meta.canonical" />

            <!-- Schema Markup -->
            <component
                v-for="(schema, i) in schemas"
                :key="i"
                is="script"
                type="application/ld+json"
                v-html="JSON.stringify(schema)"
            />
        </Head>

        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <Link :href="route('home')" class="inline-flex items-center text-sm font-medium text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-white">
                        <HomeIcon class="w-4 h-4 mr-2" />
                        Home
                    </Link>
                </li>
                <li>
                    <div class="flex items-center">
                        <ChevronRightIcon class="w-4 h-4 text-surface-400" />
                        <Link :href="route('tools.index')" class="ml-1 text-sm font-medium text-surface-500 hover:text-primary-600 md:ml-2 dark:text-surface-400 dark:hover:text-white">Tools</Link>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <ChevronRightIcon class="w-4 h-4 text-surface-400" />
                        <Link :href="route('tools.category', tool.category.slug)" class="ml-1 text-sm font-medium text-surface-500 hover:text-primary-600 md:ml-2 dark:text-surface-400 dark:hover:text-white">{{ tool.category.name }}</Link>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <ChevronRightIcon class="w-4 h-4 text-surface-400" />
                        <span class="ml-1 text-sm font-medium text-surface-600 md:ml-2 dark:text-surface-200">{{ tool.name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="space-y-8">
            <!-- Main Tool Area -->
            <div class="w-full">
                <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6 md:p-8 mb-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 to-secondary-500"></div>

                    <div class="flex items-start justify-between mb-8">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl bg-primary-50 dark:bg-surface-700 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                <component :is="iconComponent" class="w-8 h-8" v-if="iconComponent" />
                                <WrenchScrewdriverIcon v-else class="w-8 h-8" />
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <h1 class="text-2xl md:text-3xl font-bold text-surface-900 dark:text-white">{{ tool.name }}</h1>
                                    <span v-if="tool.is_premium" class="px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">PRO</span>
                                    <span v-else class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">FREE</span>
                                </div>
                                <p class="text-lg text-surface-500 dark:text-surface-400 font-bangla mt-1">{{ tool.short_description }}</p>
                                <p class="text-sm text-surface-400 mt-2 flex flex-wrap gap-2 md:gap-4">
                                    <span>Used {{ formatNumber(tool.usage_count) }} times</span>
                                    <span class="hidden md:inline">&bull;</span>
                                    <span v-if="$page.props.auth.user?.is_pro">Daily Limit: {{ tool.daily_limit_pro === -1 ? 'Unlimited' : tool.daily_limit_pro }} (Pro)</span>
                                    <span v-else>Daily Limit: {{ tool.daily_limit_free }} (Free) / {{ tool.daily_limit_pro === -1 ? 'Unlimited' : tool.daily_limit_pro }} (Pro)</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <Menu as="div" class="relative inline-block text-left">
                                <div class="flex items-center gap-2">
                                    <button @click="toggleFavorite" class="p-2 transition-colors rounded-full hover:bg-surface-100 dark:hover:bg-surface-700" :class="isFavorited ? 'text-red-500 hover:text-red-600' : 'text-surface-400 hover:text-red-500'" title="Add to favorites">
                                        <HeartIconSolid v-if="isFavorited" class="w-6 h-6" />
                                        <HeartIcon v-else class="w-6 h-6" />
                                    </button>
                                    <MenuButton class="p-2 text-surface-400 hover:text-primary-500 transition-colors rounded-full hover:bg-surface-100 dark:hover:bg-surface-700" title="Share tool">
                                        <ShareIcon class="w-6 h-6" />
                                    </MenuButton>
                                </div>
                                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-xl bg-white dark:bg-surface-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden border border-surface-100 dark:border-surface-700">
                                        <div class="py-1">
                                            <MenuItem v-slot="{ active }">
                                                <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`" target="_blank" rel="noopener noreferrer" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center px-4 py-2 text-sm text-surface-700 dark:text-surface-200']">
                                                    <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" /></svg>
                                                    Facebook
                                                </a>
                                            </MenuItem>
                                            <MenuItem v-slot="{ active }">
                                                <a :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent(tool.name)}`" target="_blank" rel="noopener noreferrer" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center px-4 py-2 text-sm text-surface-700 dark:text-surface-200']">
                                                    <svg class="w-5 h-5 mr-3 text-surface-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                                    X (Twitter)
                                                </a>
                                            </MenuItem>
                                            <MenuItem v-slot="{ active }">
                                                <a :href="`https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(currentUrl)}`" target="_blank" rel="noopener noreferrer" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center px-4 py-2 text-sm text-surface-700 dark:text-surface-200']">
                                                    <svg class="w-5 h-5 mr-3 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                                    LinkedIn
                                                </a>
                                            </MenuItem>
                                            <MenuItem v-slot="{ active }">
                                                <a :href="`https://api.whatsapp.com/send?text=${encodeURIComponent(tool.name + ' ' + currentUrl)}`" target="_blank" rel="noopener noreferrer" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center px-4 py-2 text-sm text-surface-700 dark:text-surface-200']">
                                                     <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.394 0 12.03c0 2.12.553 4.189 1.605 6.002L0 24l6.163-1.617a11.831 11.831 0 005.88 1.55h.005c6.632 0 12.028-5.398 12.03-12.033a11.85 11.85 0 00-3.528-8.418z"/></svg>
                                                    WhatsApp
                                                </a>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>
                        </div>
                    </div>

                    <AdBanner :ad="ads?.top" class="mb-8" />

                    <!-- DYNAMIC TOOL COMPONENT INJECTION -->
                    <div class="tool-interface-container relative bg-surface-50 dark:bg-surface-900 p-4 md:p-6 rounded-xl border border-surface-200 dark:border-surface-700">
                        <!-- Premium Gate Overlay -->
                        <div v-if="!canUseTool" class="premium-gate-overlay">
                            <div class="premium-gate-content">
                                <div class="premium-gate-badge">
                                    <SparklesIcon class="w-6 h-6" />
                                    <span>PRO</span>
                                </div>
                                <h3 class="text-xl md:text-2xl font-bold text-surface-900 dark:text-white mt-4">
                                    This is a Pro Tool
                                </h3>
                                <p class="text-surface-500 dark:text-surface-400 mt-2 max-w-sm mx-auto text-sm md:text-base">
                                    Upgrade to Pro to unlock <strong>{{ tool.name }}</strong> and all premium tools with unlimited access.
                                </p>
                                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-6">
                                    <Link v-if="$page.props.auth.user" :href="route('pricing')" class="premium-gate-btn-primary">
                                        <SparklesIcon class="w-5 h-5" />
                                        Upgrade to Pro
                                    </Link>
                                    <template v-else>
                                        <button @click="openLogin" class="premium-gate-btn-primary">
                                            <ArrowRightOnRectangleIcon class="w-5 h-5" />
                                            Login to Continue
                                        </button>
                                        <Link :href="route('pricing')" class="premium-gate-btn-secondary">
                                            View Pro Plans
                                        </Link>
                                    </template>
                                </div>
                                <div class="flex items-center justify-center gap-4 mt-5 text-xs text-surface-400">
                                    <span class="flex items-center gap-1"><CheckCircleIcon class="w-4 h-4 text-green-500" /> Unlimited AI</span>
                                    <span class="flex items-center gap-1"><CheckCircleIcon class="w-4 h-4 text-green-500" /> Ad-Free</span>
                                    <span class="flex items-center gap-1"><CheckCircleIcon class="w-4 h-4 text-green-500" /> All Tools</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tool Interface (blurred when locked) -->
                        <div :class="{ 'pointer-events-none select-none blur-sm': !canUseTool }">
                            <component :is="dynamicComponent" :tool="tool" :settings="settings" :voices="voices" :report="report" />
                        </div>
                    </div>
                    <AdBanner :ad="ads?.bottom" class="mt-8" />

                    <!-- Rating -->
                    <ToolRating :tool="tool" class="mt-8" :class="{ 'hidden': !canUseTool }" />
                </div>

                <!-- SEO Sections with consistent spacing -->
                <div class="space-y-6">
                    <!-- About This Tool -->
                    <ToolAbout v-if="seoContent" :content="seoContent" :lang="lang" />

                    <!-- SEO Content Area (How to use) -->
                    <ToolHowTo v-if="seoContent" :content="seoContent" :lang="lang" @toggle-lang="lang = $event" />



                    <!-- FAQ Section -->
                    <ToolFaq :faqs="tool.faqs" :lang="lang" />

                    <!-- Related Tools -->
                    <ToolRelated :tools="related" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, defineAsyncComponent, ref, h } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AdBanner from '@/Components/Ads/AdBanner.vue';
import ToolAbout from '@/Components/Tools/ToolAbout.vue';
import ToolFaq from '@/Components/Tools/ToolFaq.vue';
import ToolHowTo from '@/Components/Tools/ToolHowTo.vue';
import ToolRating from '@/Components/Tools/ToolRating.vue';
import ToolRelated from '@/Components/Tools/ToolRelated.vue';
import { HomeIcon, ChevronRightIcon, HeartIcon, WrenchScrewdriverIcon, ShareIcon, CheckCircleIcon, SparklesIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/24/solid';
import * as HeroIcons from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object,
    settings: Object,
    seoContent: Object,
    related: Array,
    ads: Object,
    meta: Object,
    schemas: Array,
    is_favorited: Boolean,
    can_use_tool: Boolean,
    voices: { type: Array, default: () => [] },
    report: { type: Object, default: null },
});

const canUseTool = computed(() => props.can_use_tool);

const openLogin = () => {
    window.dispatchEvent(new CustomEvent('open-auth', { detail: 'login' }));
};

const page = usePage();
const isFavorited = ref(props.is_favorited);
const lang = ref('en');
const currentUrl = computed(() => route('tools.show', { category: props.tool.category.slug, slug: props.tool.slug }));

const iconComponent = computed(() => {
    if (props.tool.icon && HeroIcons[props.tool.icon]) {
        return HeroIcons[props.tool.icon];
    }
    return null;
});

const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
};

const toggleFavorite = () => {
    if (!page.props.auth.user) {
        window.dispatchEvent(new CustomEvent('open-auth', { detail: 'login' }));
        return;
    }

    useForm({}).post(route('user.tools.favorite', props.tool.id), {
        preserveScroll: true,
        onSuccess: () => {
            isFavorited.value = !isFavorited.value;
        }
    });
};

// Dynamically load the tool component based on component_name
const dynamicComponent = computed(() => {
    return defineAsyncComponent({
        loader: () => import(`../Tools/Implementations/${props.tool.component_name}.vue`)
            .catch(() => import('../Tools/Implementations/ToolNotFound.vue')),
        loadingComponent: {
            render: () => h('div', { class: 'flex flex-col items-center justify-center py-12' }, [
                h('div', { class: 'w-12 h-12 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin' }),
                h('p', { class: 'mt-4 text-surface-500 font-medium' }, 'Loading tool...')
            ])
        },
        delay: 200, // Show loading component only if loading takes longer than 200ms
    });
});
</script>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes pulseSoft {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-pulse-soft {
    animation: pulseSoft 2s ease-in-out infinite;
}

/* Premium Gate Overlay */
.premium-gate-overlay {
    position: absolute;
    inset: 0;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

:root.dark .premium-gate-overlay {
    background: rgba(15, 23, 42, 0.88);
}

.premium-gate-content {
    text-align: center;
    padding: 2rem;
    max-width: 480px;
}

.premium-gate-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    color: #fff;
    background: linear-gradient(135deg, #6366F1, #8B5CF6, #A855F7);
    background-size: 200% 200%;
    animation: shimmer 3s ease-in-out infinite;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
}

.premium-gate-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.75rem;
    font-size: 0.9375rem;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, #6366F1, #8B5CF6);
    border-radius: 0.75rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.premium-gate-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
}

.premium-gate-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.75rem;
    font-size: 0.9375rem;
    font-weight: 500;
    color: #475569;
    background: #F1F5F9;
    border: 1px solid #E2E8F0;
    border-radius: 0.75rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

:root.dark .premium-gate-btn-secondary {
    color: #CBD5E1;
    background: #1E293B;
    border-color: #334155;
}

.premium-gate-btn-secondary:hover {
    background: #E2E8F0;
}

:root.dark .premium-gate-btn-secondary:hover {
    background: #334155;
}
</style>
