<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Pending Payment Alert -->
                <div v-if="pendingSubscription" class="mb-6">
                    <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl border border-amber-300 dark:border-amber-700 p-5 flex items-start gap-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/40 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-amber-800 dark:text-amber-300 mb-1">Payment Processing</h4>
                            <p class="text-sm text-amber-700 dark:text-amber-400">
                                Your <strong class="capitalize">{{ pendingSubscription.plan }}</strong> plan payment of
                                <strong>৳{{ pendingSubscription.amount }}</strong> is being processed.
                                Your Pro features will be activated automatically once confirmed.
                            </p>
                            <Link :href="route('subscription.pending')" class="inline-flex items-center gap-1 text-sm font-semibold text-amber-800 dark:text-amber-300 mt-2 hover:underline">
                                Check Payment Status
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Profile Widget -->
                    <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700 p-6 flex items-center gap-6 md:col-span-2">
                        <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl font-bold dark:bg-primary-900/50 dark:text-primary-400 shrink-0">
                            <img class="h-20 w-20 object-cover rounded-full ring-2 ring-surface-100 dark:ring-surface-700" :src="$page.props.auth.user.avatar ? ($page.props.auth.user.avatar.startsWith('http') ? $page.props.auth.user.avatar : '/storage/' + $page.props.auth.user.avatar) : `https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=6366f1&color=fff&size=64`" :alt="$page.props.auth.user.name" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-2xl font-bold text-surface-900 dark:text-white">{{ user.name }}</h3>
                                <span v-if="user.is_pro" class="bg-primary-500 text-white text-xs font-bold px-2 py-0.5 rounded uppercase tracking-wide">PRO</span>
                                <span v-else class="bg-surface-200 text-surface-600 dark:bg-surface-700 dark:text-surface-300 text-xs font-bold px-2 py-0.5 rounded uppercase tracking-wide">FREE</span>
                            </div>
                            <p class="text-surface-500 dark:text-surface-400 flex items-center gap-2">
                                <span>{{ user.email }}</span>
                                <span class="text-surface-300 dark:text-surface-600">&bull;</span>
                                <span>Joined {{ new Date(user.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                            </p>
                            <div class="mt-3">
                                <Link :href="route('profile.edit')" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">Edit Profile &rarr;</Link>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Widget -->
                    <div class="bg-gradient-to-br from-primary-600 to-purple-700 overflow-hidden shadow-lg sm:rounded-2xl border border-primary-500 p-6 flex flex-col justify-center text-white relative">
                        <h3 class="font-bold text-lg mb-1">Subscription</h3>
                        <div v-if="user.is_pro && user.active_subscription">
                            <p class="text-primary-100 text-sm mb-3">You are on the <strong class="capitalize">{{ user.active_subscription.plan }}</strong> plan.</p>
                            <p class="text-xs text-primary-200 mb-4">Expires: {{ user.active_subscription.expires_at ? new Date(user.active_subscription.expires_at).toLocaleDateString() : 'Lifetime' }}</p>
                            <button @click="confirmCancellation" class="inline-block bg-white text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors">Cancel Subscription</button>
                        </div>
                        <div v-else>
                            <p class="text-primary-100 text-sm mb-4">You are on the Free plan. Upgrade to remove ads and unlock limits.</p>
                            <Link :href="route('pricing')" class="inline-block bg-white text-primary-700 hover:bg-surface-50 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors">Upgrade to Pro</Link>
                        </div>
                    </div>
                </div>

                <!-- AI Credits Widget -->
                <div v-if="creditInfo && creditInfo.enabled && user.ai_credit !== 0" class="mb-8">
                    <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-lg text-surface-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                AI Credits
                            </h3>
                            <div v-if="user.ai_credit === -1" class="text-2xl font-black text-primary-600 dark:text-primary-400">∞ Unlimited</div>
                            <div v-else class="text-2xl font-black text-primary-600 dark:text-primary-400">{{ user.ai_credit }}</div>
                        </div>
                        <div v-if="user.ai_credit !== -1" class="w-full bg-surface-200 dark:bg-surface-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-primary-500 to-purple-500 h-3 rounded-full transition-all duration-700"
                                 :style="{ width: Math.min(100, (user.ai_credit / creditInfo.max) * 100) + '%' }"></div>
                        </div>
                        <p v-if="user.ai_credit !== -1" class="text-xs text-surface-500 dark:text-surface-400 mt-2">
                            {{ user.ai_credit }} of {{ creditInfo.max }} credits remaining.
                            <Link v-if="!user.is_pro" :href="route('pricing')" class="text-primary-600 font-medium ml-1">Upgrade for more →</Link>
                        </p>
                    </div>
                </div>

                <!-- Recent & Favorites -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- Favorite Tools -->
                    <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-lg text-surface-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                                Favorite Tools
                            </h3>
                        </div>

                        <div v-if="favorites && favorites.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <ToolCard v-for="tool in favorites" :key="'fav'+tool.id" :tool="tool" />
                        </div>
                        <div v-else class="text-center py-8 text-surface-500 dark:text-surface-400 bg-surface-50 dark:bg-surface-900/50 rounded-xl">
                            <svg class="w-10 h-10 mx-auto text-surface-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                            <p>You haven't added any tools to your favorites yet.</p>
                            <Link :href="route('home')" class="text-primary-600 font-medium text-sm mt-2 inline-block">Browse Tools</Link>
                        </div>
                    </div>

                    <!-- Recent Usages -->
                    <div class="bg-white dark:bg-surface-800 overflow-hidden shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-lg text-surface-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Recently Used
                            </h3>
                        </div>

                        <div v-if="recentUsages && recentUsages.length > 0" class="space-y-3">
                            <Link v-for="usage in recentUsages" :key="'usage'+usage.id" :href="usage.tool && usage.tool.category ? route('tools.show', { category: usage.tool.category.slug, slug: usage.tool.slug }) : '#'" class="flex items-center gap-4 p-3 hover:bg-surface-50 dark:hover:bg-surface-900 rounded-xl transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                                <div class="w-10 h-10 rounded-lg bg-surface-100 dark:bg-surface-800 flex items-center justify-center shrink-0">
                                    <component v-if="usage.tool" :is="HeroIcons[usage.tool.icon] || HeroIcons.WrenchScrewdriverIcon" class="w-5 h-5 text-surface-600 dark:text-surface-300" />
                                </div>
                                <div class="flex-1 min-w-0" v-if="usage.tool">
                                    <h4 class="font-semibold text-surface-900 dark:text-white truncate">{{ usage.tool.name }}</h4>
                                    <p class="text-xs text-surface-500 truncate">{{ usage.tool.category ? usage.tool.category.name : 'Uncategorized' }}</p>
                                </div>
                                <div class="text-xs text-surface-400 font-medium shrink-0">
                                    {{ new Date(usage.created_at).toLocaleDateString() }}
                                </div>
                            </Link>
                        </div>
                        <div v-else class="text-center py-8 text-surface-500 dark:text-surface-400 bg-surface-50 dark:bg-surface-900/50 rounded-xl">
                            <svg class="w-10 h-10 mx-auto text-surface-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p>You haven't used any tools yet.</p>
                            <Link :href="route('home')" class="text-primary-600 font-medium text-sm mt-2 inline-block">Explore Tools</Link>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <Modal :show="confirmingSubscriptionCancellation" @close="closeModal">
            <div class="p-6 text-left">
                <h2 class="text-lg font-medium text-surface-900 dark:text-white">
                    Are you sure you want to cancel your subscription?
                </h2>

                <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">
                    Once your subscription is cancelled, you will immediately lose access to all Pro features, including higher limits and an ad-free experience. This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="cancelSubscription"
                    >
                        Yes, Cancel Subscription
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ToolCard from '@/Components/Tools/ToolCard.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import * as HeroIcons from '@heroicons/vue/24/outline';

const props = defineProps({
    user: Object,
    recentUsages: Array,
    favorites: Array,
    creditInfo: Object,
    pendingSubscription: Object,
});

const confirmingSubscriptionCancellation = ref(false);
const form = useForm({});

const confirmCancellation = () => {
    confirmingSubscriptionCancellation.value = true;
};

const cancelSubscription = () => {
    form.post(route('subscription.cancel-active'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingSubscriptionCancellation.value = false;
};
</script>
