<template>
    <AdminLayout>
        <Head title="Site Settings" />

        <template #header>
            Site Settings
        </template>

        <form @submit.prevent="submit" class="space-y-8">
            <div v-for="(groupSettings, groupName) in settings" :key="groupName" class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50">
                    <h3 class="text-lg font-medium text-surface-900 dark:text-white capitalize">{{ groupName.replace('_', ' ') }} Settings</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="setting in groupSettings" :key="setting.key" class="col-span-1" :class="{'md:col-span-2': setting.type === 'textarea'}">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">{{ setting.label || setting.key }}</label>

                        <div class="mt-1">
                            <input v-if="setting.type === 'text' || setting.type === 'number'"
                                   :type="setting.type"
                                   v-model="form.settings[setting.key]"
                                   class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />

                            <textarea v-else-if="setting.type === 'textarea'"
                                      v-model="form.settings[setting.key]"
                                      rows="3"
                                      class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700"></textarea>

                            <div v-else-if="setting.type === 'boolean'" class="flex items-center mt-2">
                                <input type="checkbox"
                                       :checked="form.settings[setting.key] === 'true' || form.settings[setting.key] === true"
                                       @change="e => form.settings[setting.key] = e.target.checked ? 'true' : 'false'"
                                       class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200" />
                                <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Enable</span>
                            </div>

                            <div v-else-if="setting.type === 'image'" class="space-y-2 mt-2">
                                <div v-if="form.settings[setting.key] && typeof form.settings[setting.key] === 'string'" class="mb-2 p-2 bg-surface-100 dark:bg-surface-900 rounded-lg inline-block">
                                    <img :src="form.settings[setting.key]" class="h-12 w-auto object-contain" alt="Current image">
                                </div>
                                <input type="file"
                                       @input="e => form.settings[setting.key] = e.target.files[0]"
                                       class="block w-full text-sm text-surface-500 dark:text-surface-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="form.processing" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg shadow-lg hover:bg-primary-700 hover:shadow-xl transition-all font-semibold">
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Save All Settings
                </button>
            </div>
        </form>
    </AdminLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    settings: {}
});

onMounted(() => {
    // Initialize form object with current setting values
    Object.values(props.settings).forEach(group => {
        group.forEach(setting => {
            form.settings[setting.key] = setting.value;
        });
    });
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Settings updated successfully');
        }
    });
};
</script>
