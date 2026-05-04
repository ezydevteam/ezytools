<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import TwoFactorAuthenticationForm from './Partials/TwoFactorAuthenticationForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <AppLayout title="Profile Settings">
        <div class="py-8">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-surface-900 dark:text-white tracking-tight">Account Settings</h1>
                    <p class="text-surface-500 dark:text-surface-400 mt-2">Manage your profile, password, and account preferences.</p>
                </div>

                <div class="bg-white dark:bg-surface-800 p-6 sm:p-8 shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white dark:bg-surface-800 p-6 sm:p-8 shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white dark:bg-surface-800 p-6 sm:p-8 shadow-sm sm:rounded-2xl border border-surface-200 dark:border-surface-700">
                    <TwoFactorAuthenticationForm class="max-w-xl" />
                </div>

                <div v-if="$page.props.auth.user.id !== 1" class="bg-white dark:bg-surface-800 p-6 sm:p-8 shadow-sm sm:rounded-2xl border border-red-200 dark:border-red-900/30">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
