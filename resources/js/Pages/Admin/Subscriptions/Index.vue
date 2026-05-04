<template>
    <AdminLayout>
        <Head title="Manage Subscriptions" />

        <template #header>
            Subscriptions
        </template>

        <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
            <!-- Filters -->
            <div class="p-6 border-b border-surface-200 dark:border-surface-700 flex flex-wrap gap-4 items-center justify-between">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="relative min-w-[300px]">
                        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" />
                        <input type="text" v-model="search" @input="debounceSearch" placeholder="Search user or transaction ID..." class="pl-10 block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>
                    <select v-model="status" @change="fetchSubscriptions" class="block rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-50 dark:bg-surface-900/50 text-surface-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Plan</th>
                            <th class="px-6 py-4">Amount</th>
                            <th class="px-6 py-4">Transaction ID</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Expires At</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100 dark:divide-surface-700">
                        <tr v-for="sub in subscriptions.data" :key="sub.id" class="hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-surface-900 dark:text-white">{{ sub.user.name }}</div>
                                <div class="text-xs text-surface-500">{{ sub.user.email }}</div>
                            </td>
                            <td class="px-6 py-4 capitalize">{{ sub.plan }}</td>
                            <td class="px-6 py-4">{{ sub.amount }} {{ sub.currency }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ sub.transaction_id || 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span :class="getStatusClass(sub.status)" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                    {{ sub.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ sub.expires_at ? new Date(sub.expires_at).toLocaleDateString() : 'Lifetime' }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="editSubscription(sub)" class="text-primary-600 hover:text-primary-700 font-medium text-sm">Edit</button>
                                <button @click="deleteSubscription(sub)" class="text-red-600 hover:text-red-700 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="subscriptions.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center text-surface-500">No subscriptions found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700 flex items-center justify-between">
                <div class="text-sm text-surface-500">
                    Showing {{ subscriptions.from }} to {{ subscriptions.to }} of {{ subscriptions.total }} subscriptions
                </div>
                <div class="flex gap-2">
                    <Link v-for="(link, k) in subscriptions.links" :key="k" 
                          :href="link.url || '#'" 
                          v-html="link.label"
                          class="px-3 py-1 rounded border text-sm transition-colors"
                          :class="[
                              link.active ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white dark:bg-surface-800 border-surface-300 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:bg-surface-50',
                              !link.url ? 'opacity-50 cursor-not-allowed' : ''
                          ]"
                    />
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="editingSub" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-900/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-surface-200 dark:border-surface-700">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white">Edit Subscription</h3>
                    <button @click="editingSub = null" class="text-surface-400 hover:text-surface-600">
                        <XMarkIcon class="w-6 h-6" />
                    </button>
                </div>
                <form @submit.prevent="updateSubscription" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Status</label>
                        <select v-model="editForm.status" class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Expires At</label>
                        <input type="date" v-model="editForm.expires_at" class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="editingSub = null" class="px-4 py-2 text-sm font-medium text-surface-600 hover:bg-surface-100 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" :disabled="editForm.processing" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition-colors disabled:opacity-50">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    subscriptions: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const editingSub = ref(null);

const editForm = useForm({
    status: '',
    expires_at: '',
});

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        pending: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        expired: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        cancelled: 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-400',
    };
    return classes[status] || 'bg-surface-100 text-surface-700';
};

const fetchSubscriptions = () => {
    router.get(route('admin.subscriptions.index'), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

// Custom debounce implementation
const debounce = (fn, delay) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), delay);
    };
};

const debounceSearch = debounce(fetchSubscriptions, 300);

const editSubscription = (sub) => {
    editingSub.value = sub;
    editForm.status = sub.status;
    editForm.expires_at = sub.expires_at ? sub.expires_at.split('T')[0] : '';
};

const updateSubscription = () => {
    editForm.put(route('admin.subscriptions.update', editingSub.value.id), {
        onSuccess: () => {
            editingSub.value = null;
        }
    });
};

const deleteSubscription = (sub) => {
    if (confirm('Are you sure you want to delete this subscription?')) {
        router.delete(route('admin.subscriptions.destroy', sub.id));
    }
};
</script>
