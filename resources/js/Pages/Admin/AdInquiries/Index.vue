<template>
    <AdminLayout>
        <Head title="Ad Inquiries" />

        <div class="space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="s in statCards" :key="s.label" class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 p-4">
                    <p class="text-xs text-surface-500 dark:text-surface-400 mb-1">{{ s.label }}</p>
                    <p class="text-2xl font-bold" :class="s.color">{{ s.value }}</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                    <h2 class="text-lg font-bold text-surface-900 dark:text-white">Advertising Inquiries</h2>
                </div>

                <div v-if="inquiries.data.length === 0" class="p-10 text-center text-surface-400">
                    <MegaphoneIcon class="w-10 h-10 mx-auto mb-3 opacity-50" />
                    <p class="font-medium">No inquiries yet</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-50 dark:bg-surface-900/50">
                            <tr>
                                <th class="py-3 px-4 text-left font-semibold text-surface-600 dark:text-surface-400">ID</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-600 dark:text-surface-400">Contact</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-600 dark:text-surface-400">Spaces</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-600 dark:text-surface-400">Duration</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-600 dark:text-surface-400">Budget</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-600 dark:text-surface-400">Status</th>
                                <th class="py-3 px-4 text-left font-semibold text-surface-600 dark:text-surface-400">Date</th>
                                <th class="py-3 px-4 text-center font-semibold text-surface-600 dark:text-surface-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                            <tr v-for="inq in inquiries.data" :key="inq.id" class="hover:bg-surface-50 dark:hover:bg-surface-800/50">
                                <td class="py-3 px-4 font-mono text-xs text-primary-600 dark:text-primary-400">{{ inq.inquiry_id }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-surface-900 dark:text-white text-xs">{{ inq.name }}</div>
                                    <div class="text-[10px] text-surface-400">{{ inq.email }}</div>
                                    <div v-if="inq.company" class="text-[10px] text-surface-400">{{ inq.company }}</div>
                                </td>
                                <td class="py-3 px-4 text-xs text-surface-500">{{ inq.ad_spaces.length }} space(s)</td>
                                <td class="py-3 px-4 text-xs text-surface-500">{{ durationLabel(inq.duration) }}</td>
                                <td class="py-3 px-4 text-xs text-surface-500">{{ inq.budget || '—' }}</td>
                                <td class="py-3 px-4">
                                    <select :value="inq.status" @change="updateStatus(inq, $event.target.value)"
                                        class="text-xs rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-700 dark:text-surface-300 py-1 px-2">
                                        <option value="pending">Pending</option>
                                        <option value="contacted">Contacted</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </td>
                                <td class="py-3 px-4 text-xs text-surface-400">{{ formatDate(inq.created_at) }}</td>
                                <td class="py-3 px-4 text-center">
                                    <button @click="openDetail(inq)" class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 text-xs font-medium mr-2">View</button>
                                    <button @click="deleteInquiry(inq)" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="inquiries.last_page > 1" class="flex justify-center gap-1 p-4 border-t border-surface-200 dark:border-surface-700">
                    <Link v-for="link in inquiries.links" :key="link.label"
                        :href="link.url || '#'"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-medium transition-colors',
                            link.active ? 'bg-primary-600 text-white' : 'bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-400 hover:bg-surface-200 dark:hover:bg-surface-700',
                            !link.url ? 'opacity-50 pointer-events-none' : ''
                        ]"
                        v-html="link.label" />
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <Teleport to="body">
            <div v-if="detailInq" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" @click.self="detailInq = null">
                <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 w-full max-w-lg max-h-[80vh] overflow-y-auto shadow-2xl">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ detailInq.inquiry_id }}</h3>
                            <button @click="detailInq = null" class="text-surface-400 hover:text-surface-600 dark:hover:text-surface-300">
                                <XMarkIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div><span class="font-medium text-surface-600 dark:text-surface-400">Name:</span> <span class="text-surface-900 dark:text-white ml-2">{{ detailInq.name }}</span></div>
                            <div><span class="font-medium text-surface-600 dark:text-surface-400">Email:</span> <a :href="'mailto:' + detailInq.email" class="text-primary-600 hover:underline ml-2">{{ detailInq.email }}</a></div>
                            <div v-if="detailInq.company"><span class="font-medium text-surface-600 dark:text-surface-400">Company:</span> <span class="text-surface-900 dark:text-white ml-2">{{ detailInq.company }}</span></div>
                            <div v-if="detailInq.website"><span class="font-medium text-surface-600 dark:text-surface-400">Website:</span> <a :href="detailInq.website" target="_blank" class="text-primary-600 hover:underline ml-2">{{ detailInq.website }}</a></div>
                            <div><span class="font-medium text-surface-600 dark:text-surface-400">Duration:</span> <span class="text-surface-900 dark:text-white ml-2">{{ durationLabel(detailInq.duration) }}</span></div>
                            <div v-if="detailInq.budget"><span class="font-medium text-surface-600 dark:text-surface-400">Budget:</span> <span class="text-surface-900 dark:text-white ml-2">{{ detailInq.budget }}</span></div>
                            <div>
                                <span class="font-medium text-surface-600 dark:text-surface-400">Selected Spaces:</span>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    <span v-for="sid in detailInq.ad_spaces" :key="sid" class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded text-xs">
                                        Space #{{ sid }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <span class="font-medium text-surface-600 dark:text-surface-400">Message:</span>
                                <p class="mt-1 text-surface-700 dark:text-surface-300 bg-surface-50 dark:bg-surface-900 rounded-lg p-3 text-xs leading-relaxed whitespace-pre-wrap">{{ detailInq.message }}</p>
                            </div>

                            <!-- Admin Notes -->
                            <div>
                                <label class="font-medium text-surface-600 dark:text-surface-400">Admin Notes:</label>
                                <textarea v-model="notesForm.admin_notes" rows="3"
                                    class="mt-1 block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white text-xs p-3 resize-y"
                                    placeholder="Internal notes about this inquiry..."></textarea>
                                <button @click="saveNotes" class="mt-2 px-4 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">Save Notes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { MegaphoneIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    inquiries: Object,
    stats: Object,
});

const detailInq = ref(null);
const notesForm = reactive({ admin_notes: '', status: '' });

const statCards = [
    { label: 'Total Inquiries', value: props.stats.total, color: 'text-surface-900 dark:text-white' },
    { label: 'Pending', value: props.stats.pending, color: 'text-amber-600' },
    { label: 'Contacted', value: props.stats.contacted, color: 'text-blue-600' },
    { label: 'Approved', value: props.stats.approved, color: 'text-green-600' },
];

const durationLabel = (d) => ({ '3d': '3 Days', '7d': '7 Days', '30d': '30 Days', 'custom': 'Custom' }[d] || d);

const formatDate = (d) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

const openDetail = (inq) => {
    detailInq.value = inq;
    notesForm.admin_notes = inq.admin_notes || '';
    notesForm.status = inq.status;
};

const updateStatus = (inq, status) => {
    router.put(route('admin.ad-inquiries.update', inq.id), {
        status,
        admin_notes: inq.admin_notes,
    }, { preserveScroll: true });
};

const saveNotes = () => {
    router.put(route('admin.ad-inquiries.update', detailInq.value.id), {
        status: detailInq.value.status,
        admin_notes: notesForm.admin_notes,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            detailInq.value.admin_notes = notesForm.admin_notes;
        },
    });
};

const deleteInquiry = (inq) => {
    if (!confirm(`Delete inquiry ${inq.inquiry_id}?`)) return;
    router.delete(route('admin.ad-inquiries.destroy', inq.id), { preserveScroll: true });
};
</script>
