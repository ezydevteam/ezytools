<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const avatarPreview = ref(user.avatar ? (user.avatar.startsWith('http') ? user.avatar : '/storage/' + user.avatar) : `https://ui-avatars.com/api/?name=${user.name}&background=6366f1&color=fff&size=128`);
const avatarInput = ref(null);

const form = useForm({
    _method: 'patch',
    name: user.name,
    email: user.email,
    avatar: null,
});

const triggerAvatarUpload = () => {
    avatarInput.value?.click();
};

const onAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            if (form.avatar) {
                // If avatar was updated, the page props will have the new user data
                const updatedUser = usePage().props.auth.user;
                if (updatedUser.avatar) {
                    avatarPreview.value = updatedUser.avatar.startsWith('http') ? updatedUser.avatar : '/storage/' + updatedUser.avatar;
                }
            }
        }
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-xl font-bold text-surface-900 dark:text-white">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <!-- Avatar Upload -->
            <div class="flex items-center gap-6">
                <div class="shrink-0 relative group">
                    <img :src="avatarPreview" alt="Avatar" class="w-20 h-20 rounded-full object-cover ring-4 ring-surface-50 dark:ring-surface-800 shadow-sm" />
                    <label for="avatar" class="absolute inset-0 flex items-center justify-center bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </label>
                    <input type="file" ref="avatarInput" id="avatar" class="hidden" accept="image/*" @change="onAvatarChange" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-surface-900 dark:text-white">Profile Photo</h3>
                    <p class="text-xs text-surface-500 dark:text-surface-400 mt-1">JPG, GIF or PNG. 2MB max.</p>
                    <button type="button" @click="triggerAvatarUpload" class="mt-2 text-sm text-primary-600 dark:text-primary-400 font-medium hover:text-primary-700 transition-colors">Change Photo</button>
                    <InputError class="mt-2" :message="form.errors.avatar" />
                </div>
            </div>

            <div>
                <InputLabel for="name" value="Name" class="text-surface-700 dark:text-surface-300" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-surface-800 border-surface-200 dark:border-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 rounded-lg shadow-sm"
                    v-model="form.name"
                    required
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" class="text-surface-700 dark:text-surface-300" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-white dark:bg-surface-800 border-surface-200 dark:border-surface-700 text-surface-900 dark:text-white focus:border-primary-500 focus:ring-primary-500 rounded-lg shadow-sm"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-surface-800 dark:text-surface-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-primary-600 underline hover:text-primary-700 dark:hover:text-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button :disabled="form.processing" class="px-4 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50">
                    Save Changes
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 dark:text-green-400 font-medium">Saved successfully.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
