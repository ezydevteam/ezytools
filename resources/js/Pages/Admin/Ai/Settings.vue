<template>
    <AdminLayout>
        <Head title="Global AI Settings" />
        
        <template #header>
            <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">
                Global AI Settings
            </h2>
        </template>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
                <form @submit.prevent="submit" class="space-y-8">
                    
                    <!-- Rate Limits -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">Daily Requests Limits</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <InputLabel for="daily_limit_guest" value="Guest Daily Limit (IP based)" />
                                <TextInput id="daily_limit_guest" v-model="form.daily_limit_guest" type="number" class="mt-1 block w-full" required min="0" />
                                <p class="text-xs text-surface-500 mt-1">AI requests allowed for unauthenticated users per day.</p>
                                <InputError :message="form.errors.daily_limit_guest" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="daily_limit_registered" value="Free User Daily Limit" />
                                <TextInput id="daily_limit_registered" v-model="form.daily_limit_registered" type="number" class="mt-1 block w-full" required min="0" />
                                <p class="text-xs text-surface-500 mt-1">AI requests allowed for registered free users per day.</p>
                                <InputError :message="form.errors.daily_limit_registered" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="daily_limit_pro" value="Pro User Daily Limit" />
                                <TextInput id="daily_limit_pro" v-model="form.daily_limit_pro" type="number" class="mt-1 block w-full" required min="-1" />
                                <p class="text-xs text-surface-500 mt-1">AI requests allowed for Pro users per day. <strong>-1</strong> = unlimited.</p>
                                <InputError :message="form.errors.daily_limit_pro" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700"></div>

                    <!-- Budget Controls -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">Budget & Cost Controls</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="max_daily_spend_usd" value="Maximum Daily Spend ($)" />
                                <TextInput id="max_daily_spend_usd" v-model="form.max_daily_spend_usd" type="number" step="0.01" class="mt-1 block w-full text-red-600 dark:text-red-400 font-bold" required min="0" />
                                <p class="text-xs text-surface-500 mt-1">Total estimated API cost allowed across all users per day.</p>
                                <InputError :message="form.errors.max_daily_spend_usd" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="alert_spend_exceed_usd" value="Alert Spend Threshold ($)" />
                                <TextInput id="alert_spend_exceed_usd" v-model="form.alert_spend_exceed_usd" type="number" step="0.01" class="mt-1 block w-full text-yellow-600 dark:text-yellow-400 font-bold" required min="0" />
                                <p class="text-xs text-surface-500 mt-1">Send an alert when daily spend reaches this amount.</p>
                                <InputError :message="form.errors.alert_spend_exceed_usd" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="flex items-start bg-surface-50 dark:bg-surface-900/50 p-4 rounded-lg border border-surface-200 dark:border-surface-700 cursor-pointer">
                                <Checkbox name="auto_disable_on_budget_exceed" v-model:checked="form.auto_disable_on_budget_exceed" class="mt-1 text-red-600" />
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-surface-900 dark:text-white">Auto-disable AI on budget exceed</span>
                                    <span class="block text-sm text-surface-500 dark:text-surface-400 mt-1">If enabled, all AI requests will be blocked automatically if the daily cost exceeds the maximum daily spend. This prevents unexpected bills.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700"></div>

                    <!-- Credit System -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">AI Credit System</h3>

                        <div class="mb-6">
                            <label class="flex items-start bg-surface-50 dark:bg-surface-900/50 p-4 rounded-lg border border-surface-200 dark:border-surface-700 cursor-pointer">
                                <Checkbox name="credit_system_enabled" v-model:checked="form.credit_system_enabled" class="mt-1 text-primary-600" />
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-surface-900 dark:text-white">Enable Credit System</span>
                                    <span class="block text-sm text-surface-500 dark:text-surface-400 mt-1">When enabled, each AI tool usage deducts credits from the user's balance. Disable to allow unlimited usage based only on daily limits.</span>
                                </div>
                            </label>
                        </div>

                        <div v-if="form.credit_system_enabled" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <InputLabel for="free_ai_credit_limit" value="Free User Credits" />
                                <TextInput id="free_ai_credit_limit" v-model="form.free_ai_credit_limit" type="number" class="mt-1 block w-full" required min="-1" />
                                <p class="text-xs text-surface-500 mt-1">Credits given to new free users. <strong>0</strong> = disabled, <strong>-1</strong> = unlimited.</p>
                                <InputError :message="form.errors.free_ai_credit_limit" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="pro_ai_credit_limit" value="Pro User Credits" />
                                <TextInput id="pro_ai_credit_limit" v-model="form.pro_ai_credit_limit" type="number" class="mt-1 block w-full" required min="-1" />
                                <p class="text-xs text-surface-500 mt-1">Credits given to Pro subscribers. <strong>-1</strong> = unlimited.</p>
                                <InputError :message="form.errors.pro_ai_credit_limit" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="credit_cost_default" value="Default Cost per Tool" />
                                <TextInput id="credit_cost_default" v-model="form.credit_cost_default" type="number" class="mt-1 block w-full" required min="1" />
                                <p class="text-xs text-surface-500 mt-1">Default credits deducted per AI request. Can be overridden per tool.</p>
                                <InputError :message="form.errors.credit_cost_default" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-surface-200 dark:border-surface-700"></div>

                    <!-- Voice & TTS API Keys -->
                    <div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-1">Voice & TTS API Keys</h3>
                        <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">Configure API keys for the AI Voice Generator tool. Keys are stored securely.</p>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <InputLabel for="elevenlabs_api_key" value="ElevenLabs API Key" />
                                <TextInput id="elevenlabs_api_key" v-model="form.elevenlabs_api_key" type="password" class="mt-1 block w-full font-mono" :placeholder="settings.elevenlabs_api_key ? '••••••••' + settings.elevenlabs_api_key.slice(-4) : 'xi-...'" />
                                <p class="text-xs text-surface-500 mt-1">Get your key from <a href="https://elevenlabs.io/app/settings/api-keys" target="_blank" class="text-primary-500 hover:underline">elevenlabs.io</a>. Used for Bangla and multilingual voices.</p>
                                <InputError :message="form.errors.elevenlabs_api_key" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="openai_tts_api_key" value="OpenAI TTS API Key" />
                                <TextInput id="openai_tts_api_key" v-model="form.openai_tts_api_key" type="password" class="mt-1 block w-full font-mono" :placeholder="settings.openai_tts_api_key ? '••••••••' + settings.openai_tts_api_key.slice(-4) : 'sk-...'" />
                                <p class="text-xs text-surface-500 mt-1">Uses OpenAI's TTS-1-HD model. Leave blank to use the main OpenAI provider key.</p>
                                <InputError :message="form.errors.openai_tts_api_key" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="google_tts_api_key" value="Google Cloud TTS API Key" />
                                <TextInput id="google_tts_api_key" v-model="form.google_tts_api_key" type="password" class="mt-1 block w-full font-mono" :placeholder="settings.google_tts_api_key ? '••••••••' + settings.google_tts_api_key.slice(-4) : 'AIza...'" />
                                <p class="text-xs text-surface-500 mt-1">Get your key from <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-primary-500 hover:underline">Google Cloud Console</a>. Enable "Cloud Text-to-Speech API" first.</p>
                                <InputError :message="form.errors.google_tts_api_key" class="mt-2" />
                            </div>
                        </div>

                        <!-- Status indicators -->
                        <div class="mt-4 flex flex-wrap gap-3">
                            <div v-for="provider in ttsProviders" :key="provider.key" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium" :class="provider.configured ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800' : 'bg-surface-50 dark:bg-surface-900 text-surface-400 border border-surface-200 dark:border-surface-700'">
                                <span class="w-2 h-2 rounded-full" :class="provider.configured ? 'bg-green-500' : 'bg-surface-300 dark:bg-surface-600'"></span>
                                {{ provider.label }}: {{ provider.configured ? 'Configured' : 'Not set' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end border-t border-surface-200 dark:border-surface-700 pt-6">
                        <transition leave-active-class="transition ease-in duration-1000" leave-from-class="opacity-100" leave-to-class="opacity-0">
                            <span v-show="form.recentlySuccessful" class="text-sm text-green-600 dark:text-green-400 mr-3">
                                Settings Saved.
                            </span>
                        </transition>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Settings
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    daily_limit_guest: props.settings.daily_limit_guest || 3,
    daily_limit_registered: props.settings.daily_limit_registered || 10,
    daily_limit_pro: props.settings.daily_limit_pro ?? -1,
    max_daily_spend_usd: props.settings.max_daily_spend_usd || 5.00,
    alert_spend_exceed_usd: props.settings.alert_spend_exceed_usd || 3.00,
    auto_disable_on_budget_exceed: props.settings.auto_disable_on_budget_exceed === 'true',
    credit_system_enabled: props.settings.credit_system_enabled === 'true',
    free_ai_credit_limit: props.settings.free_ai_credit_limit || 100,
    pro_ai_credit_limit: props.settings.pro_ai_credit_limit || 1000,
    credit_cost_default: props.settings.credit_cost_default || 1,
    elevenlabs_api_key: '',
    openai_tts_api_key: '',
    google_tts_api_key: '',
});

const ttsProviders = computed(() => [
    { key: 'elevenlabs', label: 'ElevenLabs', configured: !!props.settings.elevenlabs_api_key },
    { key: 'openai_tts', label: 'OpenAI TTS', configured: !!props.settings.openai_tts_api_key },
    { key: 'google_tts', label: 'Google TTS', configured: !!props.settings.google_tts_api_key },
]);

const submit = () => {
    form.post(route('admin.ai.settings.update'), {
        preserveScroll: true,
    });
};
</script>
