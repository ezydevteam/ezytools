<template>
    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-gray-200 dark:border-surface-700 px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <span class="text-surface-700 dark:text-surface-300 font-semibold text-sm italic">Help Us Improve</span>

        <div class="flex items-center gap-4">
            <!-- Clickable stars -->
            <div class="flex gap-0.5">
                <button v-for="n in 5" :key="n"
                    @click="submitRating(n)"
                    @mouseenter="hoveredStar = n"
                    @mouseleave="hoveredStar = 0"
                    class="w-7 h-7 transition-transform hover:scale-110 disabled:opacity-50"
                    :disabled="submitting"
                >
                    <svg :class="n <= displayStars ? 'text-amber-400' : 'text-surface-200 dark:text-surface-600'" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </button>
            </div>

            <!-- Divider -->
            <div class="w-px h-6 bg-surface-200 dark:bg-surface-600"></div>

            <!-- Score & count -->
            <div class="flex items-baseline gap-1.5">
                <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ avgRating }}</span>
                <span class="text-sm text-surface-400">({{ formatCount(tool.review_count) }})</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ tool: Object });

const hoveredStar = ref(0);
const submitting = ref(false);

const avgRating = computed(() => Number(props.tool.average_rating ?? 0).toFixed(1));
const displayStars = computed(() => hoveredStar.value || Math.round(props.tool.average_rating ?? 0));

const submitRating = (rating) => {
    submitting.value = true;
    router.post(`/tools/${props.tool.id}/rate`, { rating }, {
        preserveScroll: true,
        onFinish: () => { submitting.value = false; },
    });
};

const formatCount = (num) => {
    if (!num) return '0';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
};
</script>
