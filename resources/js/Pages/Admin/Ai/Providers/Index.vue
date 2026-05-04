<template>
    <AdminLayout>
        <Head title="AI Providers" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">AI Providers</h2>
                <button @click="openCreateModal" class="ms-4 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                    Add Provider
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="provider in providers" :key="provider.id" class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden flex flex-col">
                    <div class="p-6 flex-grow">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold" :class="getProviderColor(provider.name)">
                                    {{ provider.label.charAt(0) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ provider.label }}</h3>
                                    <p class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ provider.name }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <span v-if="provider.is_active" class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                <span v-else class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                                <span v-if="provider.is_default" class="px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">Default</span>
                            </div>
                        </div>

                        <div class="space-y-2 mt-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">Base URL:</span>
                                <span class="font-medium text-surface-900 dark:text-surface-200 truncate max-w-[200px]" :title="provider.base_url">{{ provider.base_url || 'Default' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">API Key:</span>
                                <span class="font-medium text-surface-900 dark:text-surface-200 font-mono">
                                    {{ provider.api_key ? '••••' + provider.api_key.slice(-4) : 'Not Set' }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-surface-500 dark:text-surface-400">Active Models:</span>
                                <span class="font-medium text-surface-900 dark:text-surface-200">{{ provider.models_count }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/50 flex justify-between items-center">
                        <button @click="testProvider(provider)" class="text-sm font-medium text-surface-600 dark:text-surface-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                            Test Connection
                        </button>
                        <button @click="openEditModal(provider)" class="px-4 py-1.5 bg-white dark:bg-surface-700 border border-surface-200 dark:border-surface-600 rounded-lg text-sm font-medium hover:bg-surface-50 dark:hover:bg-surface-600 transition-colors">
                            Edit Config
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Modal -->
        <Modal :show="showingModal" @close="closeModal" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-surface-900 dark:text-white mb-4">
                    {{ editingProvider ? 'Edit Provider' : 'Add New Provider' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-4">
                    <div v-if="!editingProvider">
                        <InputLabel for="name" value="Internal Name (e.g. openai)" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="label" value="Display Label" />
                        <TextInput id="label" v-model="form.label" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.label" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="base_url" value="Base URL (Optional)" />
                        <TextInput id="base_url" v-model="form.base_url" type="url" class="mt-1 block w-full" placeholder="https://api.openai.com/v1" />
                        <InputError :message="form.errors.base_url" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="api_key" value="API Key" />
                        <TextInput id="api_key" v-model="form.api_key" type="password" class="mt-1 block w-full" :placeholder="editingProvider ? 'Leave blank to keep existing' : 'sk-...'" />
                        <InputError :message="form.errors.api_key" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <label class="flex items-center">
                            <Checkbox name="is_active" v-model:checked="form.is_active" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-400">Active</span>
                        </label>
                        <label class="flex items-center">
                            <Checkbox name="is_default" v-model:checked="form.is_default" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-400">Set as Default Provider</span>
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Provider
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
    providers: Array,
});

const showingModal = ref(false);
const editingProvider = ref(null);

const form = useForm({
    name: '',
    label: '',
    base_url: '',
    api_key: '',
    is_active: true,
    is_default: false,
});

const openCreateModal = () => {
    editingProvider.value = null;
    form.reset();
    showingModal.value = true;
};

const openEditModal = (provider) => {
    editingProvider.value = provider;
    form.name = provider.name;
    form.label = provider.label;
    form.base_url = provider.base_url || '';
    form.api_key = ''; // Never send API key to frontend
    form.is_active = provider.is_active;
    form.is_default = provider.is_default;
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingProvider.value) {
        form.put(route('admin.ai.providers.update', editingProvider.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.ai.providers.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const testProvider = (provider) => {
    router.post(route('admin.ai.providers.test', provider.id), {}, {
        preserveScroll: true,
    });
};

const getProviderColor = (name) => {
    switch(name) {
        case 'openai': return 'bg-emerald-500';
        case 'gemini': return 'bg-blue-500';
        case 'grok': return 'bg-gray-800 dark:bg-gray-700';
        default: return 'bg-surface-500';
    }
};
</script>
