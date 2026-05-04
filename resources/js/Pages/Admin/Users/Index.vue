<template>
    <AdminLayout>
        <Head title="Users Management" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <span>Users Management</span>
                <button @click="openCreateModal" class="ms-4 px-4 py-2 bg-gradient-to-r from-primary-600 to-purple-600 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <PlusIcon class="w-4 h-4" />
                    Create User
                </button>
            </div>
        </template>

        <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="p-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex justify-between items-center">
                <form @submit.prevent="submitSearch" class="relative w-full max-w-md">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400" />
                    <input type="text" v-model="searchQuery" placeholder="Search users by name or email..." class="block w-full pl-10 pr-4 py-2 rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-surface-900 dark:border-surface-700 text-surface-900 dark:text-white" />
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                    <thead class="bg-surface-50 dark:bg-surface-900/50 text-surface-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">User</th>
                            <th scope="col" class="px-6 py-3 text-left">Plan / Credits</th>
                            <th scope="col" class="px-6 py-3 text-left">Role</th>
                            <th scope="col" class="px-6 py-3 text-left">Status</th>
                            <th scope="col" class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-surface-800 divide-y divide-surface-200 dark:divide-surface-700">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full border-2 border-surface-100 dark:border-surface-700" :src="user.avatar ? (user.avatar.startsWith('http') ? user.avatar : '/storage/' + user.avatar) : `https://ui-avatars.com/api/?name=${user.name}&background=6366f1&color=fff&size=64`" :alt="user.name" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-surface-900 dark:text-white">{{ user.name }}</div>
                                        <div class="text-xs text-surface-500 dark:text-surface-400"><span class="text-primary-600 dark:text-primary-400">#{{ user.id }}</span> - {{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span :class="[user.is_pro ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-surface-500']" class="text-xs uppercase tracking-tight">
                                        {{ user.subscription_type === 'pro' ? '★ Pro Plan' : 'Free Plan' }}
                                    </span>
                                    <span class="text-[11px] text-surface-400 font-mono">
                                        Credits: <span class="text-primary-600 dark:text-primary-400 font-bold">{{ user.ai_credit }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="[user.role === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-surface-100 text-surface-700 dark:bg-surface-700 dark:text-surface-400', 'px-2 py-0.5 inline-flex text-[10px] font-bold rounded-full uppercase tracking-wider']">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="[user.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'px-2 py-0.5 inline-flex text-[10px] font-bold rounded-full uppercase tracking-wider']">
                                    {{ user.is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <template v-if="user.role !== 'admin'">
                                    <button @click="editUser(user)" class="text-primary-600 hover:text-primary-700 font-medium text-sm">Edit</button>
                                    <button @click="deleteUser(user)" class="text-red-600 hover:text-red-700 font-medium text-sm">Delete</button>
                                </template>
                                <span v-else class="text-xs text-surface-400 italic">Protected</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex items-center justify-between">
                <span class="text-sm text-surface-500">Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users</span>
                <div class="flex gap-2">
                    <Link v-for="(link, k) in users.links" :key="k" :href="link.url || '#'" v-html="link.label"
                          class="px-3 py-1 border rounded text-sm transition-colors"
                          :class="[link.active ? 'bg-primary-600 text-white border-primary-600' : 'bg-white dark:bg-surface-800 text-surface-700 dark:text-surface-300 border-surface-300 hover:bg-surface-50', !link.url && 'opacity-50 cursor-not-allowed']" />
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-surface-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-2xl w-full max-w-2xl border border-surface-200 dark:border-surface-700 my-auto">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ editingUser ? 'Edit User: ' + editingUser.name : 'Create New User' }}</h3>
                    <button @click="showModal = false" class="text-surface-400 hover:text-surface-600"><XMarkIcon class="w-6 h-6" /></button>
                </div>
                <form @submit.prevent="submitForm" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Name</label>
                        <input type="text" v-model="form.name" required class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Email</label>
                        <input type="email" v-model="form.email" required class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Password {{ editingUser ? '(Leave blank to keep same)' : '' }}</label>
                        <input type="password" v-model="form.password" :required="!editingUser" class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Role</label>
                        <select v-model="form.role" class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="user">User</option>
                            <option value="moderator">Moderator</option>
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Status</label>
                        <div class="flex items-center gap-4 mt-2">
                            <label class="inline-flex items-center">
                                <input type="radio" v-model="form.is_active" :value="true" class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-surface-700 dark:text-surface-300">Active</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" v-model="form.is_active" :value="false" class="text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-surface-700 dark:text-surface-300">Suspended</span>
                            </label>
                        </div>
                    </div>

                    <div v-if="editingUser" class="col-span-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Subscription Plan</label>
                        <select v-model="form.subscription_type" class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="free">Free</option>
                            <option value="pro">Pro</option>
                        </select>
                    </div>

                    <div v-if="editingUser && form.subscription_type === 'pro'" class="col-span-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Pro Expiry Date</label>
                        <input type="date" v-model="form.subscription_expires_at" class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    </div>

                    <div v-if="editingUser" class="col-span-2 p-4 bg-surface-50 dark:bg-surface-900/50 rounded-xl border border-surface-200 dark:border-surface-700 mt-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-surface-400 mb-3">AI Credits Management</label>
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-xs text-surface-500 mb-1">Current Balance: <span class="text-primary-600 font-bold">{{ editingUser.ai_credit }}</span></label>
                                <input type="number" v-model="form.ai_credit" class="block w-full rounded-lg border-surface-300 dark:border-surface-700 bg-white dark:bg-surface-800 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                            </div>
                            <div class="flex gap-1">
                                <button type="button" @click="addCredits(100)" class="px-3 py-2 text-xs font-bold bg-green-100 text-green-700 rounded-lg hover:bg-green-200">+100</button>
                                <button type="button" @click="addCredits(500)" class="px-3 py-2 text-xs font-bold bg-green-100 text-green-700 rounded-lg hover:bg-green-200">+500</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 flex justify-end gap-3 mt-4">
                        <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-surface-600 hover:bg-surface-100 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg shadow-sm transition-colors disabled:opacity-50">
                            {{ editingUser ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { PlusIcon, MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    users: Object,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');
const showModal = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'user',
    is_active: true,
    subscription_type: 'free',
    subscription_expires_at: '',
    ai_credit: 0,
});

const submitSearch = () => {
    router.get(route('admin.users.index'), { search: searchQuery.value }, { preserveState: true });
};

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const editUser = (user) => {
    editingUser.value = user;
    form.clearErrors();
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    form.is_active = !!user.is_active;
    form.subscription_type = user.subscription_type || 'free';
    form.subscription_expires_at = user.subscription_expires_at ? user.subscription_expires_at.split('T')[0] : '';
    form.ai_credit = user.ai_credit || 0;
    showModal.value = true;
};

const addCredits = (amount) => {
    form.ai_credit = parseInt(form.ai_credit) + amount;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('admin.users.update', editingUser.value.id), {
            onSuccess: () => {
                showModal.value = false;
                toast.success('User updated successfully');
            }
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                showModal.value = false;
                toast.success('User created successfully');
            }
        });
    }
};

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to permanently delete user ${user.name}? This cannot be undone.`)) {
        router.delete(route('admin.users.destroy', user.id), {
            onSuccess: () => toast.success('User deleted successfully')
        });
    }
};
</script>
