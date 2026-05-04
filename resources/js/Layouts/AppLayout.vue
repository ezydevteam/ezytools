<template>
    <Head :title="title" />
    <div class="min-h-screen bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-surface-50 font-sans transition-colors duration-200">
        <Navbar />

        <!-- Page Heading (optional) -->
        <header v-if="$slots.header" class="bg-white dark:bg-surface-900 border-b border-surface-100 dark:border-surface-800">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <slot />
        </main>

        <Footer />
        <AuthModal />
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Layout/Navbar.vue';
import Footer from '@/Components/Layout/Footer.vue';
import AuthModal from '@/Components/Auth/AuthModal.vue';

defineProps({
    title: {
        type: String,
        default: 'EzyTools',
    },
});
</script>
