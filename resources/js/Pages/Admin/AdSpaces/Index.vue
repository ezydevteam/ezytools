<template>
    <AdminLayout>
        <Head title="Ad Spaces" />

        <template #header>
            Ad Spaces Management
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Edit Form -->
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">
                    {{ editing ? `Edit Ad: ${form.name}` : 'Select an Ad Space to Edit' }}
                </h3>
                
                <form v-if="editing" @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Name</label>
                        <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Type</label>
                        <select v-model="form.type" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700">
                            <option value="adsense">Google AdSense</option>
                            <option value="custom_html">Custom HTML</option>
                            <option value="image">Image Banner</option>
                        </select>
                    </div>

                    <div v-if="form.type === 'adsense' || form.type === 'custom_html'">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Ad Code (HTML)</label>
                        <textarea v-model="form.code" rows="5" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 font-mono text-sm dark:bg-surface-900 dark:border-surface-700"></textarea>
                    </div>

                    <div v-if="form.type === 'image'">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Image URL</label>
                        <input type="text" v-model="form.image_url" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div v-if="form.type === 'image'">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Target Link URL</label>
                        <input type="text" v-model="form.link_url" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Show to Users</label>
                        <select v-model="form.show_to" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700">
                            <option value="all">Everyone</option>
                            <option value="free">Guest & Free Users Only (Hide from Pro)</option>
                            <option value="guest">Guests Only</option>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center mt-4">
                            <input type="checkbox" v-model="form.is_active" class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Enable this Ad Space</span>
                        </label>
                        <label class="flex items-center mt-2">
                            <input type="checkbox" v-model="form.is_available" class="rounded border-surface-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Available for Purchase (Advertise page)</span>
                        </label>
                    </div>

                    <!-- Pricing Section -->
                    <div class="border-t border-surface-200 dark:border-surface-700 pt-4 mt-4">
                        <h4 class="text-sm font-semibold text-surface-700 dark:text-surface-300 mb-3">Pricing & Info (Advertise Page)</h4>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Description</label>
                            <textarea v-model="form.description" rows="2" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 text-sm" placeholder="Short description of this ad position..."></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-surface-700 dark:text-surface-300">Dimensions</label>
                                <input type="text" v-model="form.dimensions" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 text-sm" placeholder="728×90" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-700 dark:text-surface-300">Est. Impressions</label>
                                <input type="text" v-model="form.est_impressions" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 text-sm" placeholder="10K-50K/day" />
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-surface-700 dark:text-surface-300">3-Day ($)</label>
                                <input type="number" step="0.01" v-model="form.price_3d" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-700 dark:text-surface-300">7-Day ($)</label>
                                <input type="number" step="0.01" v-model="form.price_7d" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-700 dark:text-surface-300">30-Day ($)</label>
                                <input type="number" step="0.01" v-model="form.price_30d" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <button type="button" @click="cancelEdit" class="text-sm text-surface-500 hover:text-surface-700">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition ml-auto">
                            Update Ad Space
                        </button>
                    </div>
                </form>
                <div v-else class="text-surface-500 dark:text-surface-400 text-center py-12">
                    <p>Select an ad space from the list to manage it.</p>
                </div>
            </div>

            <!-- Ad Spaces List -->
            <div class="md:col-span-2 bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                    <thead class="bg-surface-50 dark:bg-surface-900/50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Position</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-surface-500 uppercase tracking-wider">3D</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-surface-500 uppercase tracking-wider">7D</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-surface-500 uppercase tracking-wider">30D</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-surface-800 divide-y divide-surface-200 dark:divide-surface-700">
                        <tr v-for="ad in ads" :key="ad.id">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-surface-900 dark:text-white">{{ ad.name }}</div>
                                <div class="text-xs text-surface-500 font-mono">{{ ad.position }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-surface-500 dark:text-surface-400 uppercase font-semibold">{{ ad.type.replace('_', ' ') }}</td>
                            <td class="px-4 py-3 text-center text-xs text-surface-600 dark:text-surface-400">${{ ad.price_3d }}</td>
                            <td class="px-4 py-3 text-center text-xs text-surface-600 dark:text-surface-400">${{ ad.price_7d }}</td>
                            <td class="px-4 py-3 text-center text-xs font-semibold text-amber-600">${{ ad.price_30d }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span :class="[ad.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full']">{{ ad.is_active ? 'Active' : 'Off' }}</span>
                                <span v-if="ad.is_available" class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">Avail</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="editAd(ad)" class="text-primary-600 hover:text-primary-900 dark:hover:text-primary-400">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    ads: Array,
});

const editing = ref(false);
const editId = ref(null);

const form = useForm({
    name: '',
    type: 'custom_html',
    code: '',
    image_url: '',
    link_url: '',
    is_active: false,
    is_available: true,
    show_to: 'free',
    description: '',
    dimensions: '',
    est_impressions: '',
    price_3d: 0,
    price_7d: 0,
    price_30d: 0,
});

const editAd = (ad) => {
    editing.value = true;
    editId.value = ad.id;
    form.name = ad.name;
    form.type = ad.type;
    form.code = ad.code;
    form.image_url = ad.image_url;
    form.link_url = ad.link_url;
    form.is_active = ad.is_active;
    form.is_available = ad.is_available;
    form.show_to = ad.show_to;
    form.description = ad.description || '';
    form.dimensions = ad.dimensions || '';
    form.est_impressions = ad.est_impressions || '';
    form.price_3d = ad.price_3d || 0;
    form.price_7d = ad.price_7d || 0;
    form.price_30d = ad.price_30d || 0;
};

const cancelEdit = () => {
    editing.value = false;
    editId.value = null;
    form.reset();
};

const submit = () => {
    if (editing.value) {
        form.put(route('admin.ads.update', editId.value), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Ad space updated');
                cancelEdit();
            }
        });
    }
};
</script>
