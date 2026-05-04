<template>
    <Head :title="title" />
    <div class="min-h-screen bg-surface-50 dark:bg-surface-950 flex flex-col items-center justify-center p-6 transition-colors duration-500 relative overflow-hidden">
        <!-- Abstract Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-primary-500/10 dark:bg-primary-500/5 blur-[120px] rounded-full animate-float"></div>
            <div class="absolute -bottom-[10%] -right-[10%] w-[50%] h-[50%] bg-purple-500/10 dark:bg-purple-500/5 blur-[120px] rounded-full animate-float" style="animation-delay: -3s"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30%] h-[30%] bg-blue-500/5 blur-[100px] rounded-full"></div>
        </div>

        <!-- Site Header (Branded) -->
        <div class="absolute top-8 left-0 right-0 flex justify-center z-20">
            <Link :href="route('home')" class="flex items-center gap-3 group">
                <img v-if="$page.props.settings?.site_logo" :src="'/storage/' + $page.props.settings.site_logo" :alt="$page.props.settings.site_name" class="h-10 w-auto transition-transform group-hover:scale-110" />
                <div v-else class="w-10 h-10 bg-gradient-to-br from-primary-600 to-purple-600 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg transition-transform group-hover:scale-110">
                    {{ $page.props.settings?.site_name?.charAt(0) || 'E' }}
                </div>
                <span class="text-xl font-bold text-surface-900 dark:text-white">{{ $page.props.settings?.site_name }}</span>
            </Link>
        </div>

        <div class="max-w-2xl w-full relative z-10">
            <!-- Glassmorphic Card -->
            <div class="bg-white/70 dark:bg-surface-900/70 backdrop-blur-xl border border-white dark:border-surface-800 rounded-[2.5rem] shadow-2xl p-8 md:p-12 text-center animate-fade-in-up">

                <!-- Icon and Status -->
                <div class="relative inline-block mb-10">
                    <div class="text-[140px] md:text-[180px] font-black leading-none bg-gradient-to-br from-primary-600 via-purple-600 to-blue-600 bg-clip-text text-transparent select-none opacity-20 dark:opacity-30">
                        {{ status }}
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-24 h-24 md:w-32 md:h-32 bg-white dark:bg-surface-800 rounded-3xl shadow-2xl border border-surface-100 dark:border-surface-700 flex items-center justify-center animate-bounce-slow">
                            <ExclamationTriangleIcon v-if="status === 500" class="w-12 h-12 md:w-16 md:h-16 text-red-500" />
                            <MagnifyingGlassIcon v-else-if="status === 404" class="w-12 h-12 md:w-16 md:h-16 text-primary-500" />
                            <NoSymbolIcon v-else-if="status === 403" class="w-12 h-12 md:w-16 md:h-16 text-orange-500" />
                            <ClockIcon v-else-if="status === 503" class="w-12 h-12 md:w-16 md:h-16 text-amber-500" />
                            <ShieldExclamationIcon v-else class="w-12 h-12 md:w-16 md:h-16 text-surface-400" />
                        </div>
                    </div>
                </div>

                <!-- Text Content -->
                <h1 class="text-3xl md:text-4xl font-black text-surface-900 dark:text-white mb-4">
                    {{ title }}
                </h1>
                <p class="text-lg text-surface-600 dark:text-surface-400 mb-12 max-w-md mx-auto leading-relaxed">
                    {{ description }}
                </p>

                <!-- Actions -->
                <div v-if="status !== 503" class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link :href="route('home')" class="group w-full sm:w-auto inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-primary-600 to-purple-600 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-1 transition-all active:scale-95">
                        <HomeIcon class="w-5 h-5 mr-2 transition-transform group-hover:-translate-x-1" />
                        Return Home
                    </Link>
                    <button @click="goBack" class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-4 bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-200 font-bold rounded-2xl border border-surface-200 dark:border-surface-700 hover:bg-surface-200 dark:hover:bg-surface-700 transition-all active:scale-95">
                        <ArrowLeftIcon class="w-5 h-5 mr-2" />
                        Go Back
                    </button>
                </div>
            </div>

            <!-- Helpful Links -->
            <div v-if="status !== 503" class="mt-12 flex flex-wrap items-center justify-center gap-x-8 gap-y-4 animate-fade-in" style="animation-delay: 0.5s">
                <Link :href="route('pages.contact')" class="text-surface-500 hover:text-primary-600 transition-colors flex items-center">
                    <ChatBubbleLeftRightIcon class="w-4 h-4 mr-2" />
                    Support
                </Link>
                <Link :href="route('pages.about')" class="text-surface-500 hover:text-primary-600 transition-colors flex items-center">
                    <InformationCircleIcon class="w-4 h-4 mr-2" />
                    About Us
                </Link>
                <a href="#" @click.prevent="reload" class="text-surface-500 hover:text-primary-600 transition-colors flex items-center">
                    <ArrowPathIcon class="w-4 h-4 mr-2" />
                    Reload Page
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-8 text-surface-400 dark:text-surface-600 text-sm font-medium">
            &copy; {{ new Date().getFullYear() }} {{ $page.props.settings?.site_name }}. All rights reserved.
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    HomeIcon,
    ArrowLeftIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
    NoSymbolIcon,
    ClockIcon,
    ShieldExclamationIcon,
    ChatBubbleLeftRightIcon,
    InformationCircleIcon,
    ArrowPathIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    status: Number,
});

const title = computed(() => {
    return {
        503: 'Maintenance Mode',
        500: 'Server Error',
        404: 'Page Not Found',
        403: 'Access Forbidden',
    }[props.status] || 'Unexpected Error';
});

const description = computed(() => {
    return {
        503: 'We are currently performing scheduled maintenance to improve your experience. Please check back shortly.',
        500: 'Our server encountered an internal error. Our team has been notified and we are working to fix it.',
        404: "The page you are looking for might have been moved, deleted, or possibly never existed in the first place.",
        403: 'You do not have the necessary permissions to view this resource. Please contact your administrator.',
    }[props.status] || 'Something went wrong on our end. We are looking into it as we speak.';
});

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/';
    }
};

const reload = () => {
    window.location.reload();
};
</script>

<style scoped>
@keyframes float {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(20px, -20px); }
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-fade-in {
    opacity: 0;
    animation: fade-in 1s ease-out forwards;
}

.animate-bounce-slow {
    animation: bounce-slow 3s ease-in-out infinite;
}
</style>
