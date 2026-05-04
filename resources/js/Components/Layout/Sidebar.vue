<template>
    <div class="shrink-0">
        <!-- Mobile backdrop -->
        <div v-show="isOpen"
             class="fixed inset-0 z-40 bg-surface-900/80 backdrop-blur-sm transition-opacity lg:hidden"
             @click="$emit('close')"></div>

        <!-- Sidebar component -->
        <div :class="[
                isOpen ? 'translate-x-0' : '-translate-x-full',
                'fixed inset-y-0 left-0 z-50 w-64 bg-surface-900 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:inset-auto flex flex-col'
            ]">

            <!-- Logo area -->
            <div class="flex h-16 shrink-0 items-center px-6 bg-surface-950 border-b border-surface-800 justify-between">
                <Link :href="route('admin.dashboard')" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary-900/30">E</div>
                    <span class="font-bold text-lg tracking-tight">EzyTools</span>
                    <span class="text-[10px] font-semibold bg-primary-600/30 text-primary-300 px-1.5 py-0.5 rounded uppercase">Admin</span>
                </Link>
                <button @click="$emit('close')" class="lg:hidden text-surface-400 hover:text-white">
                    <XMarkIcon class="w-6 h-6" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                <!-- Main -->
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-surface-500">Main</p>
                    <div class="space-y-0.5">
                        <Link :href="route('admin.dashboard')"
                              :class="[route().current('admin.dashboard') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <HomeIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.dashboard') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Dashboard
                        </Link>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-surface-500">Content</p>
                    <div class="space-y-0.5">
                        <Link :href="route('admin.tools.index')"
                              :class="[route().current('admin.tools.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <WrenchScrewdriverIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.tools.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Tools
                        </Link>

                        <Link :href="route('admin.categories.index')"
                              :class="[route().current('admin.categories.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <Squares2X2Icon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.categories.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Categories
                        </Link>

                        <Link :href="route('admin.ai.dashboard')"
                              :class="[route().current('admin.ai.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <SparklesIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.ai.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            AI Management
                        </Link>

                        <Link :href="route('admin.blog.index')"
                              :class="[route().current('admin.blog.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <NewspaperIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.blog.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Blog
                        </Link>
                    </div>
                </div>

                <!-- Users & Revenue -->
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-surface-500">Users & Revenue</p>
                    <div class="space-y-0.5">
                        <Link :href="route('admin.users.index')"
                              :class="[route().current('admin.users.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <UsersIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.users.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Users
                        </Link>

                        <Link :href="route('admin.subscriptions.index')"
                              :class="[route().current('admin.subscriptions.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <CreditCardIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.subscriptions.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Subscriptions
                        </Link>

                        <Link :href="route('admin.ads.index')"
                              :class="[route().current('admin.ads.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <MegaphoneIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.ads.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Ad Spaces
                        </Link>

                        <Link :href="route('admin.ad-inquiries.index')"
                              :class="[route().current('admin.ad-inquiries.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <InboxStackIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.ad-inquiries.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Ad Inquiries
                        </Link>

                        <Link :href="route('admin.emails.index')"
                              :class="[route().current('admin.emails.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <EnvelopeIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.emails.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Emails
                        </Link>
                    </div>
                </div>

                <!-- System -->
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-surface-500">System</p>
                    <div class="space-y-0.5">
                        <Link :href="route('admin.settings.index')"
                              :class="[route().current('admin.settings.*') ? 'bg-primary-600/20 text-primary-400 border-l-2 border-primary-400' : 'text-surface-300 hover:bg-surface-800 hover:text-white border-l-2 border-transparent', 'group flex items-center px-3 py-2 text-sm font-medium rounded-r-md transition-all']">
                            <Cog6ToothIcon class="mr-3 h-5 w-5 flex-shrink-0" :class="route().current('admin.settings.*') ? 'text-primary-400' : 'text-surface-500 group-hover:text-surface-300'" aria-hidden="true" />
                            Settings
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- Footer -->
            <div class="px-3 py-4 border-t border-surface-800 space-y-1">
                <Link :href="route('logout')" method="post" as="button" class="w-full group flex items-center px-3 py-2 text-sm font-medium rounded-md text-surface-400 hover:bg-red-500/10 hover:text-red-400 transition-colors">
                    <ArrowRightOnRectangleIcon class="mr-3 h-5 w-5 flex-shrink-0 text-surface-500 group-hover:text-red-400" />
                    Logout
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import {
    HomeIcon,
    WrenchScrewdriverIcon,
    UsersIcon,
    CreditCardIcon,
    MegaphoneIcon,
    InboxStackIcon,
    Cog6ToothIcon,
    XMarkIcon,
    ArrowRightOnRectangleIcon,
    SparklesIcon,
    Squares2X2Icon,
    NewspaperIcon,
    EnvelopeIcon
} from '@heroicons/vue/24/outline';

defineProps({
    isOpen: Boolean
});

defineEmits(['close']);
</script>
