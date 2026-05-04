<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const page = usePage();

const twoFactorEnabled = computed(() => page.props.auth.user.two_factor_confirmed_at !== null);
const twoFactorSetup = computed(() => page.props.flash?.two_factor_setup);
const recoveryCodes = computed(() => page.props.flash?.recovery_codes);

const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);

const enableForm = useForm({});

const confirmForm = useForm({
    code: '',
});

const disableForm = useForm({
    password: '',
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;
    enableForm.post(route('two-factor.enable'), {
        preserveScroll: true,
        onSuccess: () => {
            confirming.value = true;
            enabling.value = false;
        },
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmForm.post(route('two-factor.confirm'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            confirmForm.reset();
        },
    });
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;
    disableForm.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            disableForm.reset();
        },
        onError: () => {
            disableForm.reset('password');
        }
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-surface-900 dark:text-surface-100">
                Two Factor Authentication
            </h2>
            <p class="mt-1 text-sm text-surface-600 dark:text-surface-400">
                Add additional security to your account using two factor authentication.
            </p>
        </header>

        <div class="mt-6">
            <h3 class="text-lg font-medium text-surface-900 dark:text-surface-100">
                <span v-if="twoFactorEnabled">You have enabled two factor authentication.</span>
                <span v-else-if="confirming">Finish enabling two factor authentication.</span>
                <span v-else>You have not enabled two factor authentication.</span>
            </h3>

            <div class="mt-3 max-w-xl text-sm text-surface-600 dark:text-surface-400">
                <p>
                    When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
                </p>
            </div>

            <!-- Setup / Confirmation Step -->
            <div v-if="confirming && twoFactorSetup" class="mt-6">
                <p class="font-semibold text-sm text-surface-900 dark:text-surface-100">
                    To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.
                </p>

                <div class="mt-4 flex flex-col items-start gap-4">
                    <div class="p-2 bg-white rounded-lg inline-block shadow-sm" v-html="twoFactorSetup.qr_code"></div>
                    <div class="text-sm font-semibold">
                        Setup Key: <span class="bg-surface-100 dark:bg-surface-800 p-1 rounded font-mono">{{ twoFactorSetup.secret }}</span>
                    </div>
                </div>

                <div class="mt-6 max-w-xl">
                    <InputLabel for="code" value="Code" />

                    <TextInput
                        id="code"
                        v-model="confirmForm.code"
                        type="text"
                        name="code"
                        class="block mt-1 w-1/2"
                        inputmode="numeric"
                        autofocus
                        autocomplete="one-time-code"
                        @keyup.enter="confirmTwoFactorAuthentication"
                    />

                    <InputError :message="confirmForm.errors.code" class="mt-2" />
                </div>

                <div class="mt-6 flex gap-4">
                    <PrimaryButton
                        :class="{ 'opacity-25': confirmForm.processing }"
                        :disabled="confirmForm.processing"
                        @click="confirmTwoFactorAuthentication"
                    >
                        Confirm
                    </PrimaryButton>

                    <SecondaryButton
                        @click="() => { confirming = false; }"
                    >
                        Cancel
                    </SecondaryButton>
                </div>
            </div>

            <!-- Recovery Codes -->
            <div v-if="twoFactorEnabled && recoveryCodes" class="mt-6">
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <p class="font-semibold text-sm text-yellow-800 dark:text-yellow-200">
                        Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                    </p>

                    <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-white dark:bg-surface-900 rounded-lg shadow-inner text-surface-700 dark:text-surface-300">
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enable/Disable Actions -->
            <div class="mt-6 flex gap-4" v-if="!confirming">
                <div v-if="!twoFactorEnabled">
                    <PrimaryButton
                        type="button"
                        :class="{ 'opacity-25': enabling }"
                        :disabled="enabling"
                        @click="enableTwoFactorAuthentication"
                    >
                        Enable
                    </PrimaryButton>
                </div>

                <div v-else class="w-full max-w-xl">
                    <div class="flex items-center gap-4 mb-4">
                        <TextInput
                            v-model="disableForm.password"
                            type="password"
                            placeholder="Enter password to disable"
                            class="block w-full"
                        />
                    </div>
                    <InputError :message="disableForm.errors.password" class="mb-4" />
                    <DangerButton
                        :class="{ 'opacity-25': disabling }"
                        :disabled="disabling || !disableForm.password"
                        @click="disableTwoFactorAuthentication"
                    >
                        Disable 2FA
                    </DangerButton>
                </div>
            </div>
        </div>
    </section>
</template>
