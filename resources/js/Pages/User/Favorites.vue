<template>
    <AppLayout title="Favorite Tools">
        <template #header>
            <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">
                Favorite Tools
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-surface-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                            Your Favorites
                        </h3>
                    </div>

                    <div v-if="favorites && favorites.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <ToolCard v-for="tool in favorites" :key="'fav'+tool.id" :tool="tool" />
                    </div>
                    
                    <div v-else class="text-center py-12 text-surface-500 dark:text-surface-400 bg-surface-50 dark:bg-surface-900/50 rounded-xl">
                        <svg class="w-12 h-12 mx-auto text-surface-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <p class="text-lg">You haven't added any tools to your favorites yet.</p>
                        <Link :href="route('home')" class="text-primary-600 hover:text-primary-700 font-medium mt-4 inline-block px-4 py-2 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors">
                            Browse Tools
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ToolCard from '@/Components/Tools/ToolCard.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    favorites: Array
});
</script>
