<template>
    <Link :href="route('tools.show', { category: tool.category.slug, slug: tool.slug })" 
          class="group block bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 hover:border-surface-300 dark:hover:border-surface-600 transition-all duration-200 hover:shadow-md relative p-5">
        
        <div v-if="tool.is_premium" class="absolute top-4 right-4 z-10">
            <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[10px] font-bold px-2.5 py-0.5 uppercase tracking-wider rounded-full shadow-sm">Pro</span>
        </div>

        <div class="flex items-start gap-4">
            <!-- Icon -->
            <div :class="[categoryColor.bg, categoryColor.text, 'w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-105']">
                <component :is="iconComponent" class="w-6 h-6" v-if="iconComponent" />
                <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            
            <!-- Title & Category -->
            <div class="flex-1 min-w-0 pt-0.5">
                <h3 class="text-[17px] font-bold text-surface-900 dark:text-white transition-colors leading-tight truncate group-hover:bg-clip-text group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-primary-600 group-hover:to-purple-600">
                    {{ tool.name }}
                </h3>
                <p :class="[categoryColor.text, 'text-[11px] font-bold mt-1 uppercase tracking-wide']">{{ tool.category?.name }}</p>
            </div>
        </div>
        
        <!-- Description -->
        <p class="mt-4 text-[13px] text-surface-500 dark:text-surface-400 line-clamp-2 leading-relaxed font-bangla">
            {{ tool.short_description || tool.description }}
        </p>
    </Link>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import * as HeroIcons from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: {
        type: Object,
        required: true
    }
});

const iconComponent = computed(() => {
    if (props.tool.icon && HeroIcons[props.tool.icon]) {
        return HeroIcons[props.tool.icon];
    }
    return null;
});

const categoryColor = computed(() => {
    const slug = props.tool.category?.slug;
    switch (slug) {
        case 'pdf-tools': return { bg: 'bg-purple-50 dark:bg-purple-900/20', text: 'text-purple-600 dark:text-purple-400' };
        case 'image-tools': return { bg: 'bg-orange-50 dark:bg-orange-900/20', text: 'text-orange-600 dark:text-orange-400' };
        case 'video-tools': return { bg: 'bg-pink-50 dark:bg-pink-900/20', text: 'text-pink-600 dark:text-pink-400' };
        case 'ai-write': 
        case 'text-tools': return { bg: 'bg-blue-50 dark:bg-blue-900/20', text: 'text-blue-600 dark:text-blue-400' };
        case 'developer-tools': return { bg: 'bg-emerald-50 dark:bg-emerald-900/20', text: 'text-emerald-600 dark:text-emerald-400' };
        case 'converters': return { bg: 'bg-indigo-50 dark:bg-indigo-900/20', text: 'text-indigo-600 dark:text-indigo-400' };
        case 'calculators': return { bg: 'bg-cyan-50 dark:bg-cyan-900/20', text: 'text-cyan-600 dark:text-cyan-400' };
        default: return { bg: 'bg-teal-50 dark:bg-teal-900/20', text: 'text-teal-600 dark:text-teal-400' };
    }
});
</script>
