<template>
    <nav class="bg-white dark:bg-surface-900 border-b border-surface-100 dark:border-surface-800 sticky top-0 z-50 backdrop-blur-xl bg-white/95 dark:bg-surface-900/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14">
                <!-- Left: Logo + Category Nav -->
                <div class="flex items-center gap-6">
                    <Link :href="route('home')" class="flex shrink-0 items-center gap-2">
                        <img v-if="$page.props.settings?.site_logo" :src="$page.props.settings.site_logo" :alt="$page.props.settings.site_name" class="h-8 w-auto object-contain" />
                        <div v-else class="w-7 h-7 bg-gradient-to-br from-primary-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm">
                            {{ ($page.props.settings?.site_name || 'E').charAt(0) }}
                        </div>
                        <span class="font-bold text-lg text-surface-900 dark:text-white">{{ $page.props.settings?.site_name || 'EzyTools' }}</span>
                    </Link>

                    <!-- Category Menus (Desktop) -->
                    <div class="hidden lg:flex items-center gap-0.5">
                        <Link :href="route('tools.index')" class="flex items-center gap-1 px-3 py-1.5 text-sm font-semibold rounded-lg transition-colors text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white hover:bg-surface-50 dark:hover:bg-surface-800">
                            All Tools
                        </Link>
                        <!-- Main 5 categories -->
                        <div v-for="category in mainCategories" :key="category.id"
                             class="relative"
                             @mouseenter="openDropdown = category.slug; moreOpen = false"
                             @mouseleave="openDropdown = null">
                            <Link :href="route('tools.category', category.slug)"
                                  class="relative flex items-center gap-1 px-3 py-1.5 text-sm font-semibold rounded-lg transition-colors"
                                  :class="openDropdown === category.slug
                                      ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20'
                                      : 'text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white hover:bg-surface-50 dark:hover:bg-surface-800'">
                                <div class="flex items-center">
                                    {{ category.name.replace(' Tools', '').replace(' tools', '') }}
                                </div>
                                <ChevronDownIcon class="w-3.5 h-3.5 transition-transform" :class="openDropdown === category.slug ? 'rotate-180' : ''" />
                                <span v-if="category.slug === 'ai-tools'" class="absolute top-0 right-0 -mt-1 px-1 py-[2px] text-[8px] font-bold uppercase tracking-wider bg-red-500 text-white rounded shadow-sm leading-none">NEW</span>
                            </Link>

                            <!-- Mega Dropdown -->
                            <transition
                                enter-active-class="transition ease-out duration-150"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-1">
                                <div v-if="openDropdown === category.slug"
                                     class="absolute left-0 top-full pt-2 z-50"
                                     @mouseenter="openDropdown = category.slug"
                                     @mouseleave="openDropdown = null">
                                    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl ring-1 ring-surface-200 dark:ring-surface-700 overflow-hidden min-w-[420px] max-w-[560px]">
                                        <div class="flex">
                                            <div class="w-1/2 p-4 border-r border-surface-100 dark:border-surface-700">
                                                <p class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-3 px-1">Featured Tools</p>
                                                <div class="space-y-0.5">
                                                    <Link v-for="tool in getTools(category, 0, 4)" :key="tool.id"
                                                          :href="route('tools.show', { category: category.slug, slug: tool.slug })"
                                                          @click="openDropdown = null"
                                                          class="flex items-center gap-3 px-2 py-2.5 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors group">
                                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                                                             :class="getCategoryBg(category.slug)">
                                                            <component :is="HeroIcons[tool.icon] || HeroIcons.WrenchScrewdriverIcon"
                                                                       class="w-4 h-4"
                                                                       :class="getCategoryText(category.slug)" />
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-semibold text-surface-800 dark:text-white truncate" :title="tool.name">{{ tool.name }}</p>
                                                        </div>
                                                    </Link>
                                                </div>
                                            </div>
                                            <div class="w-1/2 p-4">
                                                <p class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-3 px-1">Other {{ category.name }}</p>
                                                <div class="space-y-0.5">
                                                    <Link v-for="tool in getTools(category, 4, 10)" :key="tool.id"
                                                          :href="route('tools.show', { category: category.slug, slug: tool.slug })"
                                                          @click="openDropdown = null"
                                                          class="block px-2 py-1.5 text-sm text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-surface-50 dark:hover:bg-surface-700/50 rounded-lg transition-colors truncate" :title="tool.name">
                                                        {{ tool.name }}
                                                    </Link>
                                                </div>
                                                <Link :href="route('tools.category', category.slug)"
                                                      @click="openDropdown = null"
                                                      class="block mt-3 px-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                                                    All {{ category.name }} →
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <!-- More dropdown -->
                        <div v-if="moreCategories.length" class="relative"
                             @mouseenter="moreOpen = true; openDropdown = null"
                             @mouseleave="moreOpen = false; moreSubOpen = null">
                            <button class="flex items-center gap-1 px-3 py-1.5 text-sm font-semibold rounded-lg transition-colors"
                                    :class="moreOpen
                                        ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20'
                                        : 'text-surface-700 dark:text-surface-300 hover:text-surface-900 dark:hover:text-white hover:bg-surface-50 dark:hover:bg-surface-800'">
                                More
                                <ChevronDownIcon class="w-3.5 h-3.5 transition-transform" :class="moreOpen ? 'rotate-180' : ''" />
                            </button>
                            <transition
                                enter-active-class="transition ease-out duration-150"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-100"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-1">
                                <div v-if="moreOpen" class="absolute right-0 top-full pt-2 z-50"
                                     @mouseenter="moreOpen = true"
                                     @mouseleave="moreOpen = false; moreSubOpen = null">
                                    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-xl ring-1 ring-surface-200 dark:ring-surface-700 overflow-hidden">
                                        <div class="flex">
                                            <!-- Left: Two-column mega menu for hovered category -->
                                            <div v-if="hoveredMoreCategory" class="border-r border-surface-100 dark:border-surface-700">
                                                <div class="flex min-w-[420px]">
                                                    <div class="w-1/2 p-4 border-r border-surface-100 dark:border-surface-700">
                                                        <p class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-3 px-1">Featured Tools</p>
                                                        <div class="space-y-0.5">
                                                            <Link v-for="tool in getTools(hoveredMoreCategory, 0, 4)" :key="tool.id"
                                                                  :href="route('tools.show', { category: hoveredMoreCategory.slug, slug: tool.slug })"
                                                                  @click="moreOpen = false; moreSubOpen = null"
                                                                  class="flex items-center gap-3 px-2 py-2.5 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors group">
                                                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                                                                     :class="getCategoryBg(hoveredMoreCategory.slug)">
                                                                    <component :is="HeroIcons[tool.icon] || HeroIcons.WrenchScrewdriverIcon"
                                                                               class="w-4 h-4" :class="getCategoryText(hoveredMoreCategory.slug)" />
                                                                </div>
                                                                <p class="text-sm font-semibold text-surface-800 dark:text-white truncate" :title="tool.name">{{ tool.name }}</p>
                                                            </Link>
                                                        </div>
                                                    </div>
                                                    <div class="w-1/2 p-4">
                                                        <p class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-3 px-1">More {{ hoveredMoreCategory.name }}</p>
                                                        <div class="space-y-0.5">
                                                            <Link v-for="tool in getTools(hoveredMoreCategory, 4, 10)" :key="tool.id"
                                                                  :href="route('tools.show', { category: hoveredMoreCategory.slug, slug: tool.slug })"
                                                                  @click="moreOpen = false; moreSubOpen = null"
                                                                  class="block px-2 py-1.5 text-sm text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-surface-50 dark:hover:bg-surface-700/50 rounded-lg transition-colors truncate" :title="tool.name">
                                                                {{ tool.name }}
                                                            </Link>
                                                        </div>
                                                        <Link :href="route('tools.category', hoveredMoreCategory.slug)"
                                                              @click="moreOpen = false; moreSubOpen = null"
                                                              class="block mt-3 px-2 text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                                                            All {{ hoveredMoreCategory.name }} →
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Right: Category list -->
                                            <div class="min-w-[230px] p-2">
                                                <p class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-2 px-3 pt-1">Categories</p>
                                                <div v-for="cat in moreCategories" :key="cat.id"
                                                     @mouseenter="moreSubOpen = cat.slug">
                                                    <Link :href="route('tools.category', cat.slug)"
                                                          @click="moreOpen = false; moreSubOpen = null"
                                                          class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-xl text-sm transition-colors"
                                                          :class="moreSubOpen === cat.slug
                                                              ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-semibold'
                                                              : 'text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-700/50 font-medium'">
                                                        <div class="flex items-center gap-2.5">
                                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" :class="getCategoryBg(cat.slug)">
                                                                <component :is="HeroIcons[cat.icon] || HeroIcons.FolderIcon" class="w-3.5 h-3.5" :class="getCategoryText(cat.slug)" />
                                                            </div>
                                                            {{ cat.name }}
                                                        </div>
                                                        <ChevronDownIcon class="w-3.5 h-3.5 -rotate-90 text-surface-400 shrink-0" />
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="hidden sm:flex sm:items-center gap-2">
                    <!-- Dark Mode -->
                    <button @click="toggleDark" class="p-2 rounded-lg hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors text-surface-500 dark:text-surface-500 hover:text-surface-700 dark:hover:text-surface-300" title="Toggle dark mode">
                        <SunIcon v-if="isDark" class="w-[18px] h-[18px] text-amber-400" />
                        <MoonIcon v-else class="w-[18px] h-[18px]" />
                    </button>

                    <!-- Search -->
                    <button @click="searchOpen = true" class="flex items-center gap-2 rounded-lg bg-surface-50 dark:bg-surface-800 px-3 py-1.5 text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-400 border border-surface-200 dark:border-surface-700 hover:border-surface-300 dark:hover:border-surface-600 transition-colors">
                        <MagnifyingGlassIcon class="w-4 h-4" />
                        <span class="hidden lg:inline">Search</span>
                    </button>

                    <!-- Auth -->
                    <div v-if="$page.props.auth.user" class="relative ml-1">
                        <Menu as="div" class="relative">
                            <MenuButton class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">
                                <img class="h-7 w-7 object-cover rounded-full ring-2 ring-surface-100 dark:ring-surface-700" :src="$page.props.auth.user.avatar ? ($page.props.auth.user.avatar.startsWith('http') ? $page.props.auth.user.avatar : '/storage/' + $page.props.auth.user.avatar) : `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=6366f1&color=fff&size=64`" :alt="$page.props.auth.user.name" />
                                <ChevronDownIcon class="w-3.5 h-3.5 text-surface-400" />
                            </MenuButton>
                            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                <MenuItems class="absolute right-0 z-10 mt-1.5 w-52 origin-top-right rounded-xl bg-white dark:bg-surface-800 py-1.5 shadow-lg ring-1 ring-surface-200 dark:ring-surface-700 focus:outline-none">
                                    <div class="px-3 py-2 border-b border-surface-100 dark:border-surface-700">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ $page.props.auth.user.name }}</p>
                                            <span v-if="$page.props.auth.user.is_pro" class="shrink-0 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded shadow-sm leading-none">PRO</span>
                                        </div>
                                        <p class="text-xs text-surface-400 truncate mt-0.5">{{ $page.props.auth.user.email }}</p>
                                    </div>
                                    <MenuItem v-slot="{ active }">
                                        <Link :href="route('user.dashboard')" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center gap-2 px-3 py-2 text-sm text-surface-600 dark:text-surface-300']">
                                            <Squares2X2Icon class="w-4 h-4" /> Dashboard
                                        </Link>
                                    </MenuItem>
                                    <MenuItem v-slot="{ active }">
                                        <Link :href="route('user.favorites')" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center gap-2 px-3 py-2 text-sm text-surface-600 dark:text-surface-300']">
                                            <HeartIcon class="w-4 h-4" /> Favorite Tools
                                        </Link>
                                    </MenuItem>
                                    <MenuItem v-if="$page.props.auth.user.role === 'admin'" v-slot="{ active }">
                                        <Link :href="route('admin.dashboard')" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center gap-2 px-3 py-2 text-sm text-primary-600 dark:text-primary-400 font-medium']">
                                            <ShieldCheckIcon class="w-4 h-4" /> Admin Panel
                                        </Link>
                                    </MenuItem>
                                    <div class="border-t border-surface-100 dark:border-surface-700 mt-1 pt-1">
                                        <MenuItem v-slot="{ active }">
                                            <Link :href="route('logout')" method="post" as="button" class="w-full text-left" :class="[active ? 'bg-surface-50 dark:bg-surface-700' : '', 'flex items-center gap-2 px-3 py-2 text-sm text-red-500']">
                                                <ArrowRightStartOnRectangleIcon class="w-4 h-4" /> Sign out
                                            </Link>
                                        </MenuItem>
                                    </div>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                    <div v-else class="flex items-center gap-2 ml-1">
                        <button @click="openAuth('login')" class="text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 px-5 py-1.5 rounded-full transition-colors shadow-sm">Sign In</button>
                    </div>

                     <!-- Upgrade Pro -->
                    <Link :href="route('pricing')" v-if="!$page.props.auth?.user || (!$page.props.auth.user.is_pro && $page.props.auth.user.role !== 'admin')" class="flex items-center gap-1.5 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 px-4 py-1.5 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md">
                        <SparklesIcon class="w-4 h-4" />
                        <span class="hidden lg:inline">Go Pro</span>
                        <span class="inline lg:hidden">Pro</span>
                    </Link>
                </div>

                <!-- Mobile button -->
                <div class="flex items-center gap-2 sm:hidden">
                    <button @click="searchOpen = true" class="p-2 text-surface-400">
                        <MagnifyingGlassIcon class="w-5 h-5" />
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-md text-surface-400 hover:text-surface-500 hover:bg-surface-50 dark:hover:bg-surface-800">
                        <Bars3Icon v-if="!mobileMenuOpen" class="h-5 w-5" />
                        <XMarkIcon v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div v-show="mobileMenuOpen" class="sm:hidden border-t border-surface-100 dark:border-surface-800 bg-white dark:bg-surface-900">
            <div class="px-4 pt-3 pb-4 space-y-1">
                <Link :href="route('home')" class="block px-3 py-2 rounded-lg text-sm font-medium" :class="route().current('home') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800'">Home</Link>
                <Link :href="route('tools.index')" class="block px-3 py-2 rounded-lg text-sm font-medium" :class="route().current('tools.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800'">All Tools</Link>

                <!-- Mobile Category Accordions -->
                <div v-for="category in navCategories" :key="'m-'+category.id">
                    <button @click="mobileExpanded === category.slug ? mobileExpanded = null : mobileExpanded = category.slug"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800">
                        <div class="flex items-center">
                            {{ category.name }}
                            <span v-if="category.slug === 'ai-tools'" class="ml-1.5 px-1 py-[2px] text-[8px] font-bold uppercase tracking-wider bg-red-500 text-white rounded shadow-sm leading-none relative -top-1">NEW</span>
                        </div>
                        <ChevronDownIcon class="w-4 h-4 transition-transform" :class="mobileExpanded === category.slug ? 'rotate-180' : ''" />
                    </button>
                    <div v-if="mobileExpanded === category.slug" class="pl-4 space-y-0.5 mt-1">
                        <Link v-for="tool in getTools(category, 0, 8)" :key="tool.id"
                              :href="route('tools.show', { category: category.slug, slug: tool.slug })"
                              class="block px-3 py-1.5 rounded-lg text-sm text-surface-500 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                            {{ tool.name }}
                        </Link>
                        <Link :href="route('tools.category', category.slug)"
                              class="block px-3 py-1.5 text-sm font-semibold text-primary-600 dark:text-primary-400">
                            All {{ category.name }} →
                        </Link>
                    </div>
                </div>

                <Link :href="route('pricing')" class="block px-3 py-2 rounded-lg text-sm font-medium text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800">Pricing</Link>
            </div>
            <div v-if="$page.props.auth.user" class="border-t border-surface-100 dark:border-surface-800 px-4 py-3">
                <div class="flex items-center gap-3 mb-3">
                    <img class="h-9 w-9 object-cover rounded-full" :src="$page.props.auth.user.avatar ? ($page.props.auth.user.avatar.startsWith('http') ? $page.props.auth.user.avatar : '/storage/' + $page.props.auth.user.avatar) : `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=6366f1&color=fff&size=64`" :alt="$page.props.auth.user.name" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ $page.props.auth.user.name }}</p>
                            <span v-if="$page.props.auth.user.is_pro" class="shrink-0 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded shadow-sm leading-none">PRO</span>
                        </div>
                        <p class="text-xs text-surface-400 truncate">{{ $page.props.auth.user.email }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <Link :href="route('user.dashboard')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800">
                        <Squares2X2Icon class="w-4 h-4" /> Dashboard
                    </Link>
                    <Link :href="route('user.favorites')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800">
                        <HeartIcon class="w-4 h-4" /> Favorite Tools
                    </Link>
                    <Link v-if="$page.props.auth.user.role === 'admin'" :href="route('admin.dashboard')" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-primary-600 dark:text-primary-400 font-medium hover:bg-surface-50 dark:hover:bg-surface-800">
                        <ShieldCheckIcon class="w-4 h-4" /> Admin Panel
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-2 w-full text-left px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10">
                        <ArrowRightStartOnRectangleIcon class="w-4 h-4" /> Sign out
                    </Link>
                </div>
            </div>
            <div v-else class="border-t border-surface-100 dark:border-surface-800 px-4 py-3 flex gap-3">
                <button @click="openAuth('login')" class="flex-1 text-center px-4 py-2 border border-surface-200 dark:border-surface-700 rounded-lg text-sm font-medium text-surface-700 dark:text-surface-200 hover:bg-surface-50 dark:hover:bg-surface-800">Log in</button>
                <button @click="openAuth('register')" class="flex-1 text-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700">Sign up</button>
            </div>
        </div>

        <!-- Global Search Modal -->
        <Teleport to="body">
            <transition enter-active-class="ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-show="searchOpen" class="fixed inset-0 z-[100]" aria-labelledby="search-modal" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-surface-900/60 dark:bg-black/70 backdrop-blur-sm" @click="closeSearch"></div>
                <div class="flex items-start justify-center min-h-screen pt-[15vh] px-4">
                    <div class="relative bg-white dark:bg-surface-800 rounded-2xl shadow-2xl w-full max-w-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <div class="relative">
                            <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-surface-400" />
                            <input type="text" ref="searchInput" v-model="searchQuery" @input="debounceSearch"
                                   class="block w-full pl-12 pr-12 py-4 border-0 text-surface-900 dark:text-white bg-transparent placeholder-surface-400 focus:ring-0 text-base focus:outline-none"
                                   placeholder="Search tools... (e.g., Image Compressor)" />
                            <div class="absolute right-4 top-1/2 -translate-y-1/2">
                                <svg v-if="isSearching" class="animate-spin h-5 w-5 text-primary-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <button v-else @click="closeSearch" class="text-surface-400 hover:text-surface-600 text-xs font-medium bg-surface-100 dark:bg-surface-700 px-2 py-1 rounded">ESC</button>
                            </div>
                        </div>

                        <div v-if="searchResults.length > 0" class="max-h-[50vh] overflow-y-auto border-t border-surface-100 dark:border-surface-700">
                            <Link v-for="result in searchResults" :key="result.id"
                                  :href="result.url" @click="closeSearch"
                                  class="flex items-center px-4 py-3 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors group">
                                <div class="w-9 h-9 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center shrink-0 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30 transition-colors">
                                    <component :is="HeroIcons[result.icon] || HeroIcons.WrenchScrewdriverIcon" class="w-4 h-4 text-surface-500 dark:text-surface-400 group-hover:text-primary-600 dark:group-hover:text-primary-400" />
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-surface-900 dark:text-white truncate">{{ result.name }}</p>
                                    <p class="text-xs text-surface-400 truncate">{{ result.category_name }}</p>
                                </div>
                                <ArrowRightIcon class="w-4 h-4 text-surface-300 group-hover:text-primary-500 shrink-0" />
                            </Link>
                        </div>
                        <div v-else-if="searchQuery && !isSearching" class="px-6 py-12 text-center border-t border-surface-100 dark:border-surface-700">
                            <p class="text-sm text-surface-500">No tools found for "<span class="font-medium text-surface-700 dark:text-surface-300">{{ searchQuery }}</span>"</p>
                        </div>
                    </div>
                </div>
            </div>
            </transition>
        </Teleport>
    </nav>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { MagnifyingGlassIcon, SunIcon, MoonIcon, Bars3Icon, XMarkIcon, ChevronDownIcon, ArrowRightIcon, SparklesIcon, Squares2X2Icon, HeartIcon, ShieldCheckIcon, ArrowRightStartOnRectangleIcon } from '@heroicons/vue/24/outline';
import * as HeroIcons from '@heroicons/vue/24/outline';
import axios from 'axios';

const page = usePage();
const allCategories = computed(() => page.props.navCategories || []);
const mainCategories = computed(() => allCategories.value.slice(0, 5));
const moreCategories = computed(() => allCategories.value.slice(5));
// Keep navCategories for mobile menu (all categories)
const navCategories = allCategories;

const mobileMenuOpen = ref(false);
const mobileExpanded = ref(null);
const searchOpen = ref(false);
const isDark = ref(false);
const openDropdown = ref(null);
const moreOpen = ref(false);
const moreSubOpen = ref(null);

// Helper to safely get tools with valid slugs
const getTools = (category, start, end) => {
    const tools = category.active_tools || [];
    return tools.filter(t => t && t.slug).slice(start, end);
};

// Resolve hovered category in More dropdown
const hoveredMoreCategory = computed(() => {
    if (!moreSubOpen.value) return null;
    return moreCategories.value.find(c => c.slug === moreSubOpen.value) || null;
});

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
let searchTimeout = null;
const searchInput = ref(null);

watch(searchOpen, (isOpen) => {
    if (isOpen) {
        nextTick(() => {
            searchInput.value?.focus();
        });
    }
});

// Category color mappings for dropdown icons
const getCategoryBg = (slug) => {
    const map = {
        'text-tools': 'bg-cyan-100 dark:bg-cyan-900/30',
        'calculators': 'bg-emerald-100 dark:bg-emerald-900/30',
        'date-time': 'bg-violet-100 dark:bg-violet-900/30',
        'image-tools': 'bg-orange-100 dark:bg-orange-900/30',
        'file-tools': 'bg-sky-100 dark:bg-sky-900/30',
        'business-tools': 'bg-lime-100 dark:bg-lime-900/30',
        'developer-tools': 'bg-indigo-100 dark:bg-indigo-900/30',
        'ai-tools': 'bg-blue-100 dark:bg-blue-900/30',
        'web-tools': 'bg-green-100 dark:bg-green-900/30',
        'unit-converters': 'bg-teal-100 dark:bg-teal-900/30',
        'video-tools': 'bg-pink-100 dark:bg-pink-900/30',
    };
    return map[slug] || 'bg-surface-100 dark:bg-surface-700';
};

const getCategoryText = (slug) => {
    const map = {
        'text-tools': 'text-cyan-600 dark:text-cyan-400',
        'calculators': 'text-emerald-600 dark:text-emerald-400',
        'date-time': 'text-violet-600 dark:text-violet-400',
        'image-tools': 'text-orange-600 dark:text-orange-400',
        'file-tools': 'text-sky-600 dark:text-sky-400',
        'business-tools': 'text-lime-600 dark:text-lime-400',
        'developer-tools': 'text-indigo-600 dark:text-indigo-400',
        'ai-tools': 'text-blue-600 dark:text-blue-400',
        'web-tools': 'text-green-600 dark:text-green-400',
        'unit-converters': 'text-teal-600 dark:text-teal-400',
        'video-tools': 'text-pink-600 dark:text-pink-400',
    };
    return map[slug] || 'text-surface-600 dark:text-surface-400';
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        return;
    }

    searchTimeout = setTimeout(async () => {
        isSearching.value = true;
        try {
            const response = await axios.get('/api/search', { params: { q: searchQuery.value } });
            searchResults.value = response.data;
        } catch (e) {
            console.error(e);
        } finally {
            isSearching.value = false;
        }
    }, 300);
};

const closeSearch = () => {
    searchOpen.value = false;
    setTimeout(() => {
        searchQuery.value = '';
        searchResults.value = [];
    }, 200);
};

const toggleDark = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const openAuth = (view) => {
    mobileMenuOpen.value = false;
    window.dispatchEvent(new CustomEvent('open-auth', { detail: view }));
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }

    window.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchOpen.value = true;
            nextTick(() => { if (searchInput.value) searchInput.value.focus(); });
        }
        if (e.key === 'Escape' && searchOpen.value) {
            closeSearch();
        }
    });

    window.addEventListener('open-search', () => {
        searchOpen.value = true;
        nextTick(() => { if (searchInput.value) searchInput.value.focus(); });
    });
});
</script>
