<template>
    <footer class="bg-white dark:bg-surface-900 mt-16">
        <!-- Premium Banner -->
        <div v-if="!route().current('pricing') && (!$page.props.auth.user || !$page.props.auth.user.is_pro)" class="bg-gradient-to-r from-primary-600 to-purple-700 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
            <div class="absolute bottom-0 left-1/2 w-48 h-48 bg-white/5 rounded-full translate-y-1/2"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 flex flex-col lg:flex-row items-center gap-10">
                <div class="flex-1 text-center lg:text-left">
                    <h2 class="text-3xl md:text-4xl font-extrabold mb-4">Get more with Premium</h2>
                    <p class="text-blue-100 text-sm md:text-base max-w-xl mx-auto lg:mx-0 mb-8 leading-relaxed">
                        Take your projects further with premium tools that stay out of your way and work smarter. Create without limits, ads, or roadblocks. Get started for just $9 a month.
                    </p>
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 mb-8 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                                <NoSymbolIcon class="w-4 h-4" />
                            </div>
                            Ad-free
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                                <SparklesIcon class="w-4 h-4" />
                            </div>
                            Unlimited usage
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                                <ClockIcon class="w-4 h-4" />
                            </div>
                            Faster processing
                        </div>
                    </div>
                    <Link :href="route('pricing')" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-full text-primary-800 bg-white hover:bg-blue-50 transition-colors shadow-sm">
                        Get started
                    </Link>
                </div>

                <!-- Graphic (Placeholder for the laptop graphic) -->
                <div class="flex-1 hidden md:flex justify-center lg:justify-end relative">
                    <!-- A simple illustrative element to stand in for the image -->
                    <div class="w-full max-w-[400px] h-[280px] bg-white/10 backdrop-blur-sm rounded-3xl border border-white/20 p-6 flex flex-col relative">
                        <div class="flex gap-2 mb-4">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="flex-1 bg-white/10 rounded-xl flex items-center justify-center">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-primary-800 font-black text-2xl">Pro</span>
                            </div>
                        </div>

                        <!-- Floating icons -->
                        <div class="absolute -top-4 -left-4 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg text-red-500 animate-[float_3s_ease-in-out_infinite]">
                            <DocumentIcon class="w-6 h-6" />
                        </div>
                        <div class="absolute -bottom-4 -left-2 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg text-green-500 animate-[float_4s_ease-in-out_infinite_reverse]">
                            <PhotoIcon class="w-5 h-5" />
                        </div>
                        <div class="absolute top-1/2 -right-6 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg text-purple-500 animate-[float_5s_ease-in-out_infinite]">
                            <VideoCameraIcon class="w-7 h-7" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-surface-100 dark:border-surface-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Footer -->
            <div class="py-10 grid grid-cols-2 md:grid-cols-5 gap-8">
                <!-- Brand -->
                <div class="col-span-2 md:col-span-1">
                    <Link :href="route('home')" class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 bg-gradient-to-br from-primary-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                            E
                        </div>
                        <span class="font-bold text-lg text-surface-900 dark:text-white">{{ $page.props.settings.site_name }}</span>
                    </Link>
                    <p class="text-xs text-surface-500 dark:text-surface-400 leading-relaxed max-w-xs">
                        {{ $page.props.settings.site_name }} is a free online platform offering a comprehensive suite of tools and utilities designed to simplify everyday tasks.
                    </p>
                    <!-- Social Icons -->
                    <div class="flex items-center gap-3 mt-4">
                        <a v-if="$page.props.settings.social_facebook" :href="$page.props.settings.social_facebook" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-surface-100 dark:bg-surface-800 flex items-center justify-center text-surface-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                        </a>
                        <a v-if="$page.props.settings.social_youtube" :href="$page.props.settings.social_youtube" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-surface-100 dark:bg-surface-800 flex items-center justify-center text-surface-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="YouTube">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a v-if="$page.props.settings.social_reddit" :href="$page.props.settings.social_reddit" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-surface-100 dark:bg-surface-800 flex items-center justify-center text-surface-500 hover:text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-colors" title="Reddit">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/></svg>
                        </a>
                        <a v-if="$page.props.settings.social_telegram" :href="$page.props.settings.social_telegram" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-lg bg-surface-100 dark:bg-surface-800 flex items-center justify-center text-surface-500 hover:text-sky-500 hover:bg-sky-50 dark:hover:bg-sky-900/20 transition-colors" title="Telegram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Categories -->
                <div class="col-span-2">
                    <h3 class="text-xs font-semibold text-surface-900 dark:text-white uppercase tracking-wider mb-3">Categories</h3>
                    <ul class="grid grid-cols-2 gap-x-6 gap-y-2">
                        <li v-for="cat in $page.props.navCategories" :key="cat.slug">
                            <Link :href="route('tools.category', cat.slug)" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">{{ cat.name }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xs font-semibold text-surface-900 dark:text-white uppercase tracking-wider mb-3">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><Link :href="route('tools.index')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">All Tools</Link></li>
                        <li><Link :href="route('pricing')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Pricing</Link></li>
                        <li><Link :href="route('blog.index')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Blog</Link></li>
                        <li><Link :href="route('pages.about')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">About Us</Link></li>
                        <li><Link :href="route('pages.advertise')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Advertise</Link></li>
                        <li><Link :href="route('pages.faq')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">FAQs</Link></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="text-xs font-semibold text-surface-900 dark:text-white uppercase tracking-wider mb-3">Legal</h3>
                    <ul class="space-y-2">
                        <li><Link :href="route('pages.privacy')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Privacy Policy</Link></li>
                        <li><Link :href="route('pages.terms')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Terms of Service</Link></li>
                        <li><Link :href="route('pages.gdpr')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">GDPR Compliance</Link></li>
                        <li><Link :href="route('pages.do-not-sell')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Do Not Sell My Info</Link></li>
                        <li><Link :href="route('pages.contact')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Contact Us</Link></li>
                        <li><Link :href="route('pages.sitemap')" class="text-xs text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 transition-colors">Sitemap</Link></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="border-t border-surface-100 dark:border-surface-800 py-5 flex flex-col sm:flex-row justify-between items-center gap-2">
                <p class="text-xs text-surface-400">
                    &copy; {{ new Date().getFullYear() }} EzyTools. All rights reserved.
                </p>
                <p class="text-xs text-surface-400 flex items-center gap-1">
                    Made with <span class="text-red-500">♥</span> in Bangladesh
                </p>
            </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { NoSymbolIcon, SparklesIcon, ClockIcon, DocumentIcon, PhotoIcon, VideoCameraIcon } from '@heroicons/vue/24/outline';
</script>
