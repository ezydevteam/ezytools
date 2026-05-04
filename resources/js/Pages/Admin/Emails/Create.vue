<template>
    <AdminLayout>
        <Head title="Create Campaign" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <Link :href="route('admin.emails.index')" class="me-4 text-sm text-surface-400 hover:text-surface-700 transition-colors">
                    ← Back
                </Link>
                <span>Create Campaign</span>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">

            <form @submit.prevent="submit" class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 p-6 md:p-8 space-y-6">
                <!-- Campaign Name -->
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Campaign Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. Eid Special Offer" />
                    <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>

                <!-- Subject + Preheader -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Email Subject</label>
                        <input v-model="form.subject" type="text" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="Subject line..." />
                        <p v-if="form.errors.subject" class="text-xs text-red-500 mt-1">{{ form.errors.subject }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Preheader Text</label>
                        <input v-model="form.preheader" type="text" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="Preview text in inbox..." />
                    </div>
                </div>

                <!-- Target Audience -->
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Target Audience</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button v-for="opt in audiences" :key="opt.value" type="button" @click="form.target_audience = opt.value"
                            class="p-3 rounded-xl border-2 text-center transition-all text-sm"
                            :class="form.target_audience === opt.value ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'border-surface-200 dark:border-surface-700 hover:border-surface-300'">
                            <p class="font-semibold">{{ opt.label }}</p>
                            <p class="text-xs text-surface-400 mt-0.5">{{ counts[opt.value] }} users</p>
                        </button>
                    </div>
                </div>

                <!-- Body Heading -->
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Email Heading</label>
                    <input v-model="form.body_heading" type="text" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="Main heading in email..." />
                </div>

                <!-- Body Content -->
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Body Content (HTML allowed)</label>
                    <textarea v-model="form.body_content" rows="8" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 font-mono text-sm" placeholder="<p>Your email content here...</p>"></textarea>
                    <p v-if="form.errors.body_content" class="text-xs text-red-500 mt-1">{{ form.errors.body_content }}</p>
                </div>

                <!-- CTA -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">CTA Button Text</label>
                        <input v-model="form.cta_text" type="text" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. Get 30% Off →" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">CTA Button URL</label>
                        <input v-model="form.cta_url" type="url" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="https://..." />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-surface-200 dark:border-surface-700">
                    <button type="submit" @click="form.send_now = false" :disabled="form.processing"
                        class="px-5 py-2.5 bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-300 rounded-xl text-sm font-semibold hover:bg-surface-200 dark:hover:bg-surface-600 transition-colors disabled:opacity-50">
                        Save as Draft
                    </button>
                    <button type="submit" @click="form.send_now = true" :disabled="form.processing"
                        class="px-6 py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm disabled:opacity-50 flex items-center gap-2">
                        <PaperAirplaneIcon class="w-4 h-4" />
                        Send Now
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { PaperAirplaneIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ counts: Object });

const audiences = [
    { value: 'all', label: 'All Users' },
    { value: 'free', label: 'Free Users' },
    { value: 'pro', label: 'Pro Users' },
    { value: 'expired', label: 'Expired Pro' },
];

const form = useForm({
    name: '',
    subject: '',
    preheader: '',
    body_heading: '',
    body_content: '',
    cta_text: '',
    cta_url: '',
    target_audience: 'all',
    scheduled_at: null,
    send_now: false,
});

const submit = () => {
    form.post(route('admin.emails.store'));
};
</script>
