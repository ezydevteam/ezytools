<template>
    <AdminLayout>
        <Head title="AI Models" />
        
        <template #header>
            <div class="flex justify-between items-center w-full">
                <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">AI Models</h2>
                <button @click="openCreateModal" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                    Add Model
                </button>
            </div>
        </template>

        <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                    <thead class="bg-surface-50 dark:bg-surface-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Provider</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-surface-500 uppercase tracking-wider">Model Name / ID</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Context Window</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Input Cost (/1K)</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Output Cost (/1K)</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-surface-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-surface-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-surface-900 divide-y divide-surface-200 dark:divide-surface-700">
                        <tr v-for="model in models" :key="model.id" class="hover:bg-surface-50 dark:hover:bg-surface-800/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getProviderBadgeColor(model.provider.name)">
                                    {{ model.provider.label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-surface-900 dark:text-white">{{ model.label }}</div>
                                <div class="text-xs text-surface-500 font-mono mt-0.5">{{ model.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-surface-500 dark:text-surface-400 font-mono">
                                {{ formatNumber(model.context_window) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-surface-900 dark:text-surface-200">
                                ${{ parseFloat(model.cost_per_1k_input_tokens).toFixed(5) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-surface-900 dark:text-surface-200">
                                ${{ parseFloat(model.cost_per_1k_output_tokens).toFixed(5) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span v-if="model.is_active" class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                <span v-else class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal(model)" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 mr-3">Edit</button>
                                <button @click="deleteModel(model)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Model Modal -->
        <Modal :show="showingModal" @close="closeModal" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-surface-900 dark:text-white mb-4">
                    {{ editingModel ? 'Edit Model' : 'Add New Model' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="provider_id" value="Provider" />
                        <select id="provider_id" v-model="form.provider_id" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>
                            <option value="">Select Provider...</option>
                            <option v-for="provider in providers" :key="provider.id" :value="provider.id">{{ provider.label }}</option>
                        </select>
                        <InputError :message="form.errors.provider_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="name" value="Model ID (API Name, e.g. gpt-4o)" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full font-mono" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="label" value="Display Label" />
                        <TextInput id="label" v-model="form.label" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.label" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="context_window" value="Context Window (tokens)" />
                        <TextInput id="context_window" v-model="form.context_window" type="number" class="mt-1 block w-full" required min="1" />
                        <InputError :message="form.errors.context_window" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="cost_per_1k_input_tokens" value="Input Cost per 1K Tokens ($)" />
                            <TextInput id="cost_per_1k_input_tokens" v-model="form.cost_per_1k_input_tokens" type="number" step="0.000001" class="mt-1 block w-full" required min="0" />
                            <InputError :message="form.errors.cost_per_1k_input_tokens" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="cost_per_1k_output_tokens" value="Output Cost per 1K Tokens ($)" />
                            <TextInput id="cost_per_1k_output_tokens" v-model="form.cost_per_1k_output_tokens" type="number" step="0.000001" class="mt-1 block w-full" required min="0" />
                            <InputError :message="form.errors.cost_per_1k_output_tokens" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <label class="flex items-center">
                            <Checkbox name="is_active" v-model:checked="form.is_active" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-400">Active</span>
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Model
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    models: Array,
    providers: Array,
});

const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num || 0);
};

const showingModal = ref(false);
const editingModel = ref(null);

const form = useForm({
    provider_id: '',
    name: '',
    label: '',
    context_window: 128000,
    cost_per_1k_input_tokens: 0.000000,
    cost_per_1k_output_tokens: 0.000000,
    is_active: true,
});

const openCreateModal = () => {
    editingModel.value = null;
    form.reset();
    showingModal.value = true;
};

const openEditModal = (model) => {
    editingModel.value = model;
    form.provider_id = model.provider_id;
    form.name = model.name;
    form.label = model.label;
    form.context_window = model.context_window;
    form.cost_per_1k_input_tokens = model.cost_per_1k_input_tokens;
    form.cost_per_1k_output_tokens = model.cost_per_1k_output_tokens;
    form.is_active = model.is_active;
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingModel.value) {
        form.put(route('admin.ai.models.update', editingModel.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.ai.models.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteModel = (model) => {
    if (confirm('Are you sure you want to delete this AI model?')) {
        router.delete(route('admin.ai.models.destroy', model.id), {
            preserveScroll: true,
        });
    }
};

const getProviderBadgeColor = (name) => {
    switch(name) {
        case 'openai': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400';
        case 'gemini': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'grok': return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
        default: return 'bg-surface-100 text-surface-800 dark:bg-surface-800 dark:text-surface-300';
    }
};
</script>
