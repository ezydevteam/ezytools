<template>
    <AdminLayout>
        <Head title="Email Campaigns" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <span>Email Campaigns</span>
                <Link :href="route('admin.emails.create')" class="inline-flex items-center gap-2 ms-4 px-4 py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 text-white rounded-xl text-sm font-semibold hover:from-primary-700 hover:to-purple-700 transition-all shadow-sm">
                    <PlusIcon class="w-4 h-4" />
                    New Campaign
                </Link>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Stats -->
            <div class="grid grid-cols-4 gap-4">
                <div v-for="stat in stats" :key="stat.label" class="bg-white dark:bg-surface-800 rounded-xl p-4 border border-surface-200 dark:border-surface-700">
                    <p class="text-xs text-surface-500 font-medium">{{ stat.label }}</p>
                    <p class="text-xl font-bold text-surface-900 dark:text-white mt-1">{{ stat.value }}</p>
                </div>
            </div>

            <!-- Campaign list -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div v-if="campaigns.data.length === 0" class="p-12 text-center">
                    <EnvelopeIcon class="w-12 h-12 text-surface-300 mx-auto mb-3" />
                    <p class="text-surface-500 font-medium">No campaigns yet</p>
                    <p class="text-sm text-surface-400 mt-1">Create your first email campaign</p>
                </div>

                <table v-else class="w-full text-sm">
                    <thead class="bg-surface-50 dark:bg-surface-900 border-b border-surface-200 dark:border-surface-700">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-surface-600 dark:text-surface-400">Campaign</th>
                            <th class="text-left px-4 py-3 font-semibold text-surface-600 dark:text-surface-400">Audience</th>
                            <th class="text-center px-4 py-3 font-semibold text-surface-600 dark:text-surface-400">Recipients</th>
                            <th class="text-center px-4 py-3 font-semibold text-surface-600 dark:text-surface-400">Status</th>
                            <th class="text-right px-6 py-3 font-semibold text-surface-600 dark:text-surface-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100 dark:divide-surface-700">
                        <tr v-for="c in campaigns.data" :key="c.id" class="hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-surface-900 dark:text-white">{{ c.name }}</p>
                                <p class="text-xs text-surface-400 mt-0.5 truncate max-w-[250px]">{{ c.subject }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium" :class="audienceClass(c.target_audience)">
                                    {{ c.target_audience }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center font-medium text-surface-700 dark:text-surface-300">
                                {{ c.total_recipients || '—' }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusClass(c.status)">
                                    {{ c.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button v-if="c.status !== 'sent'" @click="sendCampaign(c.id)" class="p-1.5 rounded-lg hover:bg-green-50 text-green-600 transition-colors" title="Send">
                                        <PaperAirplaneIcon class="w-4 h-4" />
                                    </button>
                                    <button @click="duplicateCampaign(c.id)" class="p-1.5 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-700 text-surface-500 transition-colors" title="Duplicate">
                                        <DocumentDuplicateIcon class="w-4 h-4" />
                                    </button>
                                    <button v-if="c.status !== 'sent'" @click="deleteCampaign(c.id)" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition-colors" title="Delete">
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { PlusIcon, EnvelopeIcon, PaperAirplaneIcon, DocumentDuplicateIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ campaigns: Object });

const stats = computed(() => [
    { label: 'Total', value: props.campaigns.total ?? props.campaigns.data?.length ?? 0 },
    { label: 'Sent', value: props.campaigns.data?.filter(c => c.status === 'sent').length ?? 0 },
    { label: 'Draft', value: props.campaigns.data?.filter(c => c.status === 'draft').length ?? 0 },
    { label: 'Scheduled', value: props.campaigns.data?.filter(c => c.status === 'scheduled').length ?? 0 },
]);

const statusClass = (s) => ({
    'draft': 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300',
    'scheduled': 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    'sending': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    'sent': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    'failed': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
}[s] ?? 'bg-surface-100 text-surface-600');

const audienceClass = (a) => ({
    'all': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    'free': 'bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300',
    'pro': 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400',
    'expired': 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
}[a] ?? 'bg-surface-100 text-surface-600');

const sendCampaign = (id) => {
    if (!confirm('Send this campaign now?')) return;
    router.post(route('admin.emails.send', id), {}, { preserveScroll: true });
};

const duplicateCampaign = (id) => {
    router.post(route('admin.emails.duplicate', id), {}, { preserveScroll: true });
};

const deleteCampaign = (id) => {
    if (!confirm('Delete this campaign?')) return;
    router.delete(route('admin.emails.destroy', id), { preserveScroll: true });
};
</script>
