<template>
    <AdminLayout>
        <Head title="Tool Settings" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tools.index')" class="text-surface-500 hover:text-surface-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                <span>Tool Settings: {{ tool.name }}</span>
            </div>
        </template>

        <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
            <form @submit.prevent="submit" class="space-y-6">
                
                <div v-if="form.settings.length === 0" class="text-center py-8 text-surface-500">
                    No custom settings available for this tool.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="(setting, index) in form.settings" :key="index" class="space-y-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">
                            {{ setting.label || setting.key }}
                        </label>

                        <template v-if="setting.type === 'boolean'">
                            <label class="flex items-center cursor-pointer mt-2">
                                <input type="checkbox" v-model="setting.value" class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200" />
                                <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Enable</span>
                            </label>
                        </template>

                        <template v-else-if="setting.type === 'number'">
                            <input type="number" v-model="setting.value" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        </template>

                        <template v-else-if="setting.type === 'json'">
                            <textarea v-model="setting.value" rows="3" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 font-mono text-sm"></textarea>
                            <span class="text-xs text-surface-400">Must be valid JSON array or object.</span>
                        </template>

                        <template v-else>
                            <input type="text" v-model="setting.value" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        </template>
                    </div>
                </div>

                <div v-if="form.settings.length > 0" class="flex items-center justify-end mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                    <button type="submit" :disabled="form.processing" class="btn-primary px-4 py-2 rounded-md transition">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    tool: Object,
    settings: Array,
});

// Format settings for form
const formattedSettings = props.settings.map(s => {
    let value = s.value;
    if (s.type === 'boolean') {
        value = value === 'true' || value === '1' || value === true;
    } else if (s.type === 'json') {
        try {
            value = typeof value === 'string' ? value : JSON.stringify(value);
        } catch (e) {
            value = '[]';
        }
    }
    return {
        key: s.key,
        value: value,
        type: s.type,
        label: s.label
    };
});

const form = useForm({
    settings: formattedSettings
});

const submit = () => {
    // Before submit, ensure JSON is parsed
    const submitData = { ...form };
    submitData.settings = submitData.settings.map(s => {
        if (s.type === 'json') {
            try {
                return { ...s, value: JSON.parse(s.value) };
            } catch (e) {
                // Ignore parse errors on submit, let validation handle it or just send string
                return s;
            }
        }
        return s;
    });

    form.transform((data) => ({
        settings: submitData.settings
    })).post(route('admin.tools.settings.update', props.tool.id), {
        preserveScroll: true
    });
};
</script>
