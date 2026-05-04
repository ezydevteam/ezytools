<template>
    <AppLayout>
        <Head title="Two-factor Confirmation" />

        <div class="min-h-[70vh] flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-surface-50 dark:bg-surface-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-surface-800 shadow-xl overflow-hidden sm:rounded-2xl border border-surface-200 dark:border-surface-700">
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-surface-900 dark:text-white">Two-factor Confirmation</h2>
                    <p class="mt-2 text-sm text-surface-600 dark:text-surface-400">
                        <template v-if="! recovery">
                            Please confirm access to your account by entering the authentication code provided by your authenticator application.
                        </template>

                        <template v-else>
                            Please confirm access to your account by entering one of your emergency recovery codes.
                        </template>
                    </p>
                </div>

                <form @submit.prevent="submit">
                    <div v-if="! recovery">
                        <InputLabel for="code" value="Code" />
                        <TextInput
                            id="code"
                            ref="codeInput"
                            v-model="form.code"
                            type="text"
                            inputmode="numeric"
                            class="mt-1 block w-full"
                            autofocus
                            autocomplete="one-time-code"
                        />
                        <InputError class="mt-2" :message="form.errors.code" />
                    </div>

                    <div v-else>
                        <InputLabel for="recovery_code" value="Recovery Code" />
                        <TextInput
                            id="recovery_code"
                            ref="recoveryCodeInput"
                            v-model="form.recovery_code"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="one-time-code"
                        />
                        <InputError class="mt-2" :message="form.errors.recovery_code" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <button type="button" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-500 underline cursor-pointer" @click.prevent="toggleRecovery">
                            <template v-if="! recovery">
                                Use a recovery code
                            </template>

                            <template v-else>
                                Use an authentication code
                            </template>
                        </button>

                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Log in
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>
