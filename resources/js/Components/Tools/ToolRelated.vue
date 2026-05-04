<template>
    <section v-if="tools?.length" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center">
                    <LinkIcon class="w-5 h-5" />
                </div>
                <h2 class="text-xl font-bold text-surface-900 dark:text-white">Related Tools</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <Link v-for="tool in tools" :key="tool.id" :href="route('tools.show', [tool.category?.slug, tool.slug])" class="flex items-center gap-3 p-4 bg-surface-50 dark:bg-surface-900/50 rounded-xl border border-surface-100 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-sm transition-all group">
                    <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 rounded-lg flex items-center justify-center text-primary-600 dark:text-primary-400 flex-shrink-0 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                        <component :is="getIcon(tool.icon)" class="w-5 h-5" v-if="getIcon(tool.icon)" />
                        <WrenchScrewdriverIcon v-else class="w-5 h-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-surface-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                            {{ tool.name }}
                        </p>
                        <p class="text-xs text-surface-400 truncate">
                            {{ tool.category?.name }}
                        </p>
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { LinkIcon, WrenchScrewdriverIcon } from '@heroicons/vue/24/outline';
import * as HeroIcons from '@heroicons/vue/24/outline';

defineProps({ tools: Array });

const getIcon = (iconName) => {
    if (iconName && HeroIcons[iconName]) return HeroIcons[iconName];
    return null;
};
</script>
