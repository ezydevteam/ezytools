<template>
    <div v-if="ad && ad.is_active" class="w-full my-4 flex justify-center items-center overflow-hidden rounded border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800">
        <div v-if="ad.type === 'custom_html'" v-html="ad.code" class="w-full"></div>
        <div v-else-if="ad.type === 'image'" class="w-full">
            <a v-if="ad.link_url" :href="ad.link_url" target="_blank" rel="noopener noreferrer">
                <img :src="ad.image_url" :alt="ad.name" loading="lazy" class="w-full max-h-[250px] object-cover" />
            </a>
            <img v-else :src="ad.image_url" :alt="ad.name" loading="lazy" class="w-full max-h-[250px] object-cover" />
        </div>
        <div v-else-if="ad.type === 'adsense'" v-html="ad.code" class="w-full text-center"></div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    ad: {
        type: Object,
        default: null
    }
});

onMounted(() => {
    if (props.ad?.type === 'adsense') {
        // Typically you'd call (adsbygoogle = window.adsbygoogle || []).push({}); here
        // but we assume the code injects it, or we handle it globally.
    }
});
</script>
