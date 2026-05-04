<template>
    <AdminLayout>
        <Head :title="`AI Config: ${tool.name}`" />

        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.tools.index')" class="text-surface-500 hover:text-primary-600 transition-colors">
                    &larr; Back to Tools
                </Link>
                <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">
                    AI Config: {{ tool.name }}
                </h2>
            </div>
        </template>

        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                <form @submit.prevent="submit" class="space-y-8">

                    <!-- Free Tier Settings -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="p-1 rounded bg-surface-100 text-surface-600 dark:bg-surface-700 dark:text-surface-300">Free Tier</span>
                            Primary AI Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <InputLabel for="provider_id" value="Provider" />
                                <select id="provider_id" v-model="form.provider_id" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                    <option :value="null">Default Provider</option>
                                    <option v-for="provider in providers" :key="provider.id" :value="provider.id">{{ provider.label }}</option>
                                </select>
                                <InputError :message="form.errors.provider_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="model_id" value="Model" />
                                <select id="model_id" v-model="form.model_id" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                    <option :value="null">Default Model</option>
                                    <option v-for="model in filteredModels(form.provider_id)" :key="model.id" :value="model.id">{{ model.label }}</option>
                                </select>
                                <InputError :message="form.errors.model_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="max_tokens_free" value="Max Output Tokens" />
                                <TextInput id="max_tokens_free" v-model="form.max_tokens_free" type="number" class="mt-1 block w-full" required min="1" />
                                <InputError :message="form.errors.max_tokens_free" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="max_input_length_free" value="Max Input Characters" />
                                <TextInput id="max_input_length_free" v-model="form.max_input_length_free" type="number" class="mt-1 block w-full" required min="1" />
                                <InputError :message="form.errors.max_input_length_free" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700"></div>

                    <!-- Pro Tier Settings -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="p-1 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-500">Pro Tier</span>
                            Premium AI Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <InputLabel for="pro_provider_id" value="Provider" />
                                <select id="pro_provider_id" v-model="form.pro_provider_id" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                    <option :value="null">Same as Free Tier</option>
                                    <option v-for="provider in providers" :key="provider.id" :value="provider.id">{{ provider.label }}</option>
                                </select>
                                <InputError :message="form.errors.pro_provider_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="pro_model_id" value="Model" />
                                <select id="pro_model_id" v-model="form.pro_model_id" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                    <option :value="null">Same as Free Tier</option>
                                    <option v-for="model in filteredModels(form.pro_provider_id)" :key="model.id" :value="model.id">{{ model.label }}</option>
                                </select>
                                <InputError :message="form.errors.pro_model_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="max_tokens_pro" value="Max Output Tokens" />
                                <TextInput id="max_tokens_pro" v-model="form.max_tokens_pro" type="number" class="mt-1 block w-full" required min="1" />
                                <InputError :message="form.errors.max_tokens_pro" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="max_input_length_pro" value="Max Input Characters" />
                                <TextInput id="max_input_length_pro" v-model="form.max_input_length_pro" type="number" class="mt-1 block w-full" required min="1" />
                                <InputError :message="form.errors.max_input_length_pro" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700"></div>

                    <!-- Fallback Settings -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="p-1 rounded bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-500">Fallback</span>
                            If Primary Fails
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="fallback_provider_id" value="Provider" />
                                <select id="fallback_provider_id" v-model="form.fallback_provider_id" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                    <option :value="null">None (Fail Request)</option>
                                    <option v-for="provider in providers" :key="provider.id" :value="provider.id">{{ provider.label }}</option>
                                </select>
                                <InputError :message="form.errors.fallback_provider_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="fallback_model_id" value="Model" />
                                <select id="fallback_model_id" v-model="form.fallback_model_id" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                    <option :value="null">None</option>
                                    <option v-for="model in filteredModels(form.fallback_provider_id)" :key="model.id" :value="model.id">{{ model.label }}</option>
                                </select>
                                <InputError :message="form.errors.fallback_model_id" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700"></div>

                    <!-- Language Settings (NEW) -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="p-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-500">🌐</span>
                            Language Settings
                        </h3>
                        <div class="space-y-6">
                            <!-- Supported Languages checkboxes -->
                            <div>
                                <InputLabel value="Supported Languages" />
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <label v-for="(lang, key) in languages" :key="key" class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" :value="key" v-model="form.supported_languages" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" />
                                        <span class="text-sm text-surface-700 dark:text-surface-300">{{ lang.label }}</span>
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <InputLabel for="default_language" value="Default Language" />
                                    <select id="default_language" v-model="form.default_language" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                        <option v-for="(lang, key) in languages" :key="key" :value="key">{{ lang.label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="output_format" value="Output Format" />
                                    <select id="output_format" v-model="form.output_format" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                        <option value="text">Text</option>
                                        <option value="json">JSON</option>
                                        <option value="markdown">Markdown</option>
                                        <option value="html">HTML</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <label class="flex items-center gap-2 cursor-pointer pb-2">
                                        <input type="checkbox" v-model="form.show_language_selector" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" />
                                        <span class="text-sm text-surface-700 dark:text-surface-300">Show Language Selector</span>
                                    </label>
                                </div>
                                <div class="flex items-end">
                                    <label class="flex items-center gap-2 cursor-pointer pb-2">
                                        <input type="checkbox" v-model="form.enable_rtl_support" class="rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500" />
                                        <span class="text-sm text-surface-700 dark:text-surface-300">Enable RTL Support</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700"></div>

                    <!-- System Prompt & Params -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">Prompt Engineering</h3>

                        <div class="space-y-6">
                            <!-- Available Variables -->
                            <div class="p-3 bg-surface-50 dark:bg-surface-900 rounded-lg border border-surface-200 dark:border-surface-700">
                                <p class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-2">Available Variables</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <span v-for="v in promptVariables" :key="v" class="px-2 py-0.5 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded text-xs font-mono cursor-pointer hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors" @click="insertVariable(v)">
                                        {{ wrapVariable(v) }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <InputLabel for="system_prompt" value="System Prompt" />
                                    <span class="text-xs text-surface-500 font-mono">Role: system</span>
                                </div>
                                <textarea id="system_prompt" ref="promptTextarea" v-model="form.system_prompt" rows="8" class="mt-1 block w-full border-surface-300 dark:border-surface-700 dark:bg-surface-900 dark:text-surface-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm font-mono text-sm" required></textarea>
                                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                                    This dictates the behavior of the AI for this tool. Click variable tags above to insert them.
                                </p>
                                <InputError :message="form.errors.system_prompt" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <InputLabel for="temperature" value="Temperature" />
                                    <span class="text-sm font-medium text-surface-900 dark:text-white">{{ form.temperature }}</span>
                                </div>
                                <input type="range" id="temperature" v-model="form.temperature" min="0" max="2" step="0.1" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700">
                                <div class="flex justify-between text-xs text-surface-500 dark:text-surface-400 mt-1">
                                    <span>0.0 (Precise/Factual)</span>
                                    <span>2.0 (Creative/Random)</span>
                                </div>
                                <InputError :message="form.errors.temperature" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="credit_cost" value="Credit Cost per Use" />
                                <TextInput id="credit_cost" v-model="form.credit_cost" type="number" class="mt-1 block w-full" required min="1" />
                                <p class="text-xs text-surface-500 mt-1">Credits deducted per AI generation for this tool.</p>
                                <InputError :message="form.errors.credit_cost" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <transition
                            enter-active-class="transition ease-out duration-300"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            leave-active-class="transition ease-in duration-300"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <span v-if="form.recentlySuccessful" class="text-sm text-green-600 dark:text-green-400 mr-3">Saved.</span>
                        </transition>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Configuration
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    tool: Object,
    config: Object,
    providers: Array,
    models: Array,
    languages: Object,
});

const promptTextarea = ref(null);
const promptVariables = ['language', 'tone', 'style', 'length', 'audience', 'platform', 'input'];
const wrapVariable = (v) => '{' + v + '}';

const form = useForm({
    provider_id: props.config?.provider_id || null,
    model_id: props.config?.model_id || null,
    pro_provider_id: props.config?.pro_provider_id || null,
    pro_model_id: props.config?.pro_model_id || null,
    fallback_provider_id: props.config?.fallback_provider_id || null,
    fallback_model_id: props.config?.fallback_model_id || null,
    system_prompt: props.config?.system_prompt || 'You are a helpful assistant.',
    max_tokens_free: props.config?.max_tokens_free || 500,
    max_tokens_pro: props.config?.max_tokens_pro || 2000,
    max_input_length_free: props.config?.max_input_length_free || 1000,
    max_input_length_pro: props.config?.max_input_length_pro || 5000,
    temperature: parseFloat(props.config?.temperature || 0.70),
    supported_languages: props.config?.supported_languages || ['bangla', 'english_us', 'english_british', 'hindi', 'urdu', 'arabic'],
    default_language: props.config?.default_language || 'english_us',
    output_format: props.config?.output_format || 'text',
    show_language_selector: props.config?.show_language_selector ?? true,
    enable_rtl_support: props.config?.enable_rtl_support ?? true,
    credit_cost: props.config?.credit_cost || 1,
});

const filteredModels = (providerId) => {
    if (!providerId) return props.models;
    return props.models.filter(m => m.provider_id === providerId);
};

const insertVariable = (v) => {
    const ta = promptTextarea.value;
    if (!ta) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = form.system_prompt;
    form.system_prompt = text.substring(0, start) + `{${v}}` + text.substring(end);
    setTimeout(() => {
        ta.focus();
        ta.setSelectionRange(start + v.length + 2, start + v.length + 2);
    }, 0);
};

const submit = () => {
    form.put(route('admin.ai.tools.config.update', props.tool.id), {
        preserveScroll: true,
    });
};
</script>
