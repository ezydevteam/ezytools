<template>
    <transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-show="isOpen" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-surface-900/75 dark:bg-black/80 backdrop-blur-sm transition-opacity" @click="closeModal" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-surface-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-surface-200 dark:border-surface-700 relative">

                    <button @click="closeModal" class="absolute top-4 right-4 text-surface-400 hover:text-surface-600 dark:hover:text-surface-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <div class="px-6 py-8">
                        <div class="text-center mb-8">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-purple-600 rounded-lg flex items-center justify-center mx-auto text-white text-xl font-bold shadow-sm mb-3">
                                E
                            </div>
                            <h3 class="text-2xl font-bold text-surface-900 dark:text-white">{{ titles[currentView] }}</h3>
                            <p class="text-surface-500 dark:text-surface-400 mt-1 text-sm">{{ subtitles[currentView] }}</p>
                        </div>

                        <!-- Login View -->
                        <div v-if="currentView === 'login'">
                            <form @submit.prevent="submitLogin" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Email or Phone Number</label>
                                    <input type="text" v-model="loginForm.login" required class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p v-if="loginForm.errors.login" class="text-red-500 text-xs mt-1">{{ loginForm.errors.login }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Password</label>
                                    <input type="password" v-model="loginForm.password" required class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" v-model="loginForm.remember" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900">
                                        <span class="text-sm text-surface-600 dark:text-surface-400">Remember me</span>
                                    </label>
                                    <button type="button" @click="currentView = 'forgot'" class="text-sm font-medium text-primary-600 hover:text-primary-500">Forgot password?</button>
                                </div>
                                <button type="submit" :disabled="loginForm.processing" class="w-full py-2.5 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-sm mt-4">
                                    {{ loginForm.processing ? 'Signing in...' : 'Sign in' }}
                                </button>

                                <div class="relative mt-6 mb-4">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                        <div class="w-full border-t border-surface-200 dark:border-surface-700"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm font-medium leading-6">
                                        <span class="bg-white dark:bg-surface-800 px-6 text-surface-500 dark:text-surface-400">Or continue with</span>
                                    </div>
                                </div>

                                <a :href="`/auth/google?redirect=${encodeURIComponent(currentPageUrl)}`" class="flex w-full items-center justify-center gap-3 rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50 focus-visible:ring-transparent transition-colors dark:bg-surface-800 dark:text-white dark:ring-surface-600 dark:hover:bg-surface-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                    </svg>
                                    <span class="text-sm font-semibold leading-6">Google</span>
                                </a>
                            </form>
                            <p class="text-center text-sm text-surface-500 dark:text-surface-400 mt-6">
                                Don't have an account? <button @click="currentView = 'register'" class="font-medium text-primary-600 hover:text-primary-500">Sign up</button>
                            </p>
                        </div>

                        <!-- Register Step 1 -->
                        <div v-else-if="currentView === 'register'">
                            <form @submit.prevent="submitRegisterStep1" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Email or Phone Number</label>
                                    <input type="text" v-model="registerForm.login" required class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p v-if="registerForm.errors.login" class="text-red-500 text-xs mt-1">{{ registerForm.errors.login }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Password</label>
                                    <input type="password" v-model="registerForm.password" required class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p v-if="registerForm.errors.password" class="text-red-500 text-xs mt-1">{{ registerForm.errors.password }}</p>
                                </div>
                                <button type="submit" :disabled="registerForm.processing" class="w-full py-2.5 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-sm mt-4">
                                    {{ registerForm.processing ? 'Sending OTP...' : 'Continue' }}
                                </button>

                                <div class="relative mt-6 mb-4">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                        <div class="w-full border-t border-surface-200 dark:border-surface-700"></div>
                                    </div>
                                    <div class="relative flex justify-center text-sm font-medium leading-6">
                                        <span class="bg-white dark:bg-surface-800 px-6 text-surface-500 dark:text-surface-400">Or continue with</span>
                                    </div>
                                </div>

                                <a :href="`/auth/google?redirect=${encodeURIComponent(currentPageUrl)}`" class="flex w-full items-center justify-center gap-3 rounded-xl bg-white px-3 py-2.5 text-sm font-semibold text-surface-900 shadow-sm ring-1 ring-inset ring-surface-300 hover:bg-surface-50 focus-visible:ring-transparent transition-colors dark:bg-surface-800 dark:text-white dark:ring-surface-600 dark:hover:bg-surface-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                    </svg>
                                    <span class="text-sm font-semibold leading-6">Google</span>
                                </a>
                            </form>
                            <p class="text-center text-sm text-surface-500 dark:text-surface-400 mt-6">
                                Already have an account? <button @click="currentView = 'login'" class="font-medium text-primary-600 hover:text-primary-500">Sign in</button>
                            </p>
                        </div>

                        <!-- Register Step 2 (OTP) -->
                        <div v-else-if="currentView === 'otp'">
                            <form @submit.prevent="submitRegisterStep2" class="space-y-4">
                                <div class="p-4 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-xl mb-4 text-sm text-center">
                                    Enter the 6-digit code we sent to <br><span class="font-bold">{{ registerForm.login }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">One Time Password (OTP)</label>
                                    <input type="text" v-model="registerForm.otp" required class="block w-full text-center tracking-widest text-2xl font-mono rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p v-if="registerForm.errors.otp" class="text-red-500 text-xs mt-1 text-center">{{ registerForm.errors.otp }}</p>
                                </div>
                                <button type="submit" :disabled="registerForm.processing" class="w-full py-2.5 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-sm mt-4">
                                    {{ registerForm.processing ? 'Verifying...' : 'Verify & Create Account' }}
                                </button>
                                 <div class="mt-6 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">
                                        Didn't receive the code?
                                        <button 
                                            type="button" 
                                            @click="submitResendOtp" 
                                            :disabled="resendTimer > 0 || resendLoading"
                                            class="font-medium text-primary-600 hover:text-primary-500 disabled:text-surface-400 disabled:cursor-not-allowed ml-1"
                                        >
                                            {{ resendTimer > 0 ? `Resend in ${resendTimer}s` : (resendLoading ? 'Sending...' : 'Resend OTP') }}
                                        </button>
                                    </p>
                                </div>

                                <button type="button" @click="currentView = 'register'" class="w-full py-2 text-surface-500 hover:text-surface-700 text-sm font-medium mt-2">
                                    Go back
                                </button>
                            </form>
                        </div>

                         <!-- Forgot Password Step 1: Email -->
                        <div v-else-if="currentView === 'forgot'">
                            <form @submit.prevent="submitForgot" class="space-y-4">
                                <div v-if="forgotStatus" class="p-3 bg-green-50 text-green-700 rounded-lg text-sm mb-4">
                                    {{ forgotStatus }}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Email or Phone Number</label>
                                    <input type="text" v-model="forgotForm.email" required class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p v-if="forgotForm.errors.email" class="text-red-500 text-xs mt-1">{{ forgotForm.errors.email }}</p>
                                </div>
                                <button type="submit" :disabled="forgotForm.processing" class="w-full py-2.5 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-sm mt-4">
                                    {{ forgotForm.processing ? 'Sending...' : 'Send OTP' }}
                                </button>
                            </form>
                            <p class="text-center text-sm text-surface-500 dark:text-surface-400 mt-6">
                                Remembered your password? <button @click="currentView = 'login'" class="font-medium text-primary-600 hover:text-primary-500">Sign in</button>
                            </p>
                        </div>

                        <!-- Forgot Password Step 2: OTP -->
                        <div v-else-if="currentView === 'forgot-otp'">
                            <form @submit.prevent="currentView = 'forgot-reset'" class="space-y-4">
                                <div class="p-4 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-xl mb-4 text-sm text-center">
                                    Enter the reset code sent to <br><span class="font-bold">{{ forgotForm.email }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Reset Code</label>
                                    <input type="text" v-model="forgotForm.otp" required class="block w-full text-center tracking-widest text-2xl font-mono rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p v-if="forgotForm.errors.otp" class="text-red-500 text-xs mt-1 text-center">{{ forgotForm.errors.otp }}</p>
                                </div>
                                <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl transition-colors shadow-sm mt-4">
                                    Verify Code
                                </button>
                                
                                <div class="mt-6 text-center">
                                    <p class="text-sm text-surface-500 dark:text-surface-400">
                                        Didn't receive the code?
                                        <button 
                                            type="button" 
                                            @click="submitResendForgotOtp" 
                                            :disabled="resendTimer > 0 || resendLoading"
                                            class="font-medium text-primary-600 hover:text-primary-500 disabled:text-surface-400 disabled:cursor-not-allowed ml-1"
                                        >
                                            {{ resendTimer > 0 ? `Resend in ${resendTimer}s` : (resendLoading ? 'Sending...' : 'Resend OTP') }}
                                        </button>
                                    </p>
                                </div>

                                <button type="button" @click="currentView = 'forgot'" class="w-full py-2 text-surface-500 hover:text-surface-700 text-sm font-medium mt-2">
                                    Go back
                                </button>
                            </form>
                        </div>

                        <!-- Forgot Password Step 3: New Password -->
                        <div v-else-if="currentView === 'forgot-reset'">
                            <form @submit.prevent="submitResetPassword" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">New Password</label>
                                    <input type="password" v-model="forgotForm.password" required class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                    <p v-if="forgotForm.errors.password" class="text-red-500 text-xs mt-1">{{ forgotForm.errors.password }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Confirm New Password</label>
                                    <input type="password" v-model="forgotForm.password_confirmation" required class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <button type="submit" :disabled="forgotForm.processing" class="w-full py-2.5 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-sm mt-4">
                                    {{ forgotForm.processing ? 'Resetting...' : 'Update Password' }}
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const isOpen = ref(false);
const currentView = ref('login'); // login, register, otp, forgot
const currentPageUrl = computed(() => window.location.href);
const forgotStatus = ref('');
const resendTimer = ref(0);
const resendLoading = ref(false);
let timerInterval = null;

const titles = {
    login: 'Welcome Back',
    register: 'Create an Account',
    otp: 'Verify your identity',
    forgot: 'Reset Password',
    'forgot-otp': 'Verify Reset Code',
    'forgot-reset': 'New Password'
};

const subtitles = {
    login: 'Sign in to access your dashboard and saved tools.',
    register: 'Join EzyTools to unlock premium features.',
    otp: 'We need to verify it\'s really you.',
    forgot: 'Enter your email or phone to receive a password reset code.',
    'forgot-otp': 'Check your device for the verification code.',
    'forgot-reset': 'Create a new secure password for your account.'
};

const loginForm = useForm({
    login: '',
    password: '',
    remember: false,
});

const registerForm = useForm({
    login: '',
    password: '',
    otp: '',
});

const forgotForm = useForm({
    email: '',
    otp: '',
    password: '',
    password_confirmation: '',
});

const submitLogin = () => {
    loginForm.transform((data) => ({
        ...data,
        intended_url: window.location.pathname + window.location.search,
    })).post(route('login'), {
        onSuccess: () => closeModal(),
    });
};

const submitRegisterStep1 = () => {
    // Send request via axios or inertia. Since it returns JSON (requires_otp), we can use axios.
    registerForm.clearErrors();
    registerForm.processing = true;

    axios.post(route('register'), {
        login: registerForm.login,
        password: registerForm.password,
    }).then(response => {
        if (response.data.requires_otp) {
            currentView.value = 'otp';
            startResendTimer();
        }
    }).catch(error => {
        if (error.response && error.response.data.errors) {
            for (let field in error.response.data.errors) {
                registerForm.setError(field, error.response.data.errors[field][0]);
            }
        }
    }).finally(() => {
        registerForm.processing = false;
    });
};

const submitRegisterStep2 = () => {
    registerForm.transform((data) => ({
        ...data,
        intended_url: window.location.pathname + window.location.search,
    })).post(route('register'), {
        onSuccess: () => {
            closeModal();
            stopResendTimer();
        },
    });
};

const startResendTimer = () => {
    stopResendTimer();
    resendTimer.value = 60;
    timerInterval = setInterval(() => {
        if (resendTimer.value > 0) {
            resendTimer.value--;
        } else {
            stopResendTimer();
        }
    }, 1000);
};

const stopResendTimer = () => {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
};

const submitResendOtp = () => {
    if (resendTimer.value > 0 || resendLoading.value) return;

    resendLoading.value = true;
    axios.post(route('register.resend-otp'), {
        login: registerForm.login,
    }).then(() => {
        startResendTimer();
        // You could show a toast here
    }).catch(error => {
        if (error.response?.data?.message) {
            registerForm.setError('otp', error.response.data.message);
        }
    }).finally(() => {
        resendLoading.value = false;
    });
};

const submitForgot = () => {
    forgotForm.clearErrors();
    forgotForm.processing = true;

    axios.post(route('password.email'), {
        email: forgotForm.email,
    }).then(response => {
        if (response.data.requires_otp) {
            currentView.value = 'forgot-otp';
            startResendTimer();
        }
    }).catch(error => {
        if (error.response?.data?.errors) {
            for (let field in error.response.data.errors) {
                forgotForm.setError(field, error.response.data.errors[field][0]);
            }
        }
    }).finally(() => {
        forgotForm.processing = false;
    });
};

const submitResendForgotOtp = () => {
    if (resendTimer.value > 0 || resendLoading.value) return;

    resendLoading.value = true;
    axios.post(route('password.resend-otp'), {
        email: forgotForm.email,
    }).then(() => {
        startResendTimer();
    }).catch(error => {
        if (error.response?.data?.message) {
            forgotForm.setError('otp', error.response.data.message);
        }
    }).finally(() => {
        resendLoading.value = false;
    });
};

const submitResetPassword = () => {
    forgotForm.post(route('password.update'), {
        onSuccess: () => {
            currentView.value = 'login';
            forgotStatus.value = 'Password reset successfully! Please sign in with your new password.';
            forgotForm.reset();
            stopResendTimer();
        },
    });
};

const openModal = (view = 'login') => {
    currentView.value = view;
    isOpen.value = true;
    loginForm.reset();
    registerForm.reset();
    loginForm.clearErrors();
    registerForm.clearErrors();
    forgotForm.clearErrors();
    forgotStatus.value = '';
};

const closeModal = () => {
    isOpen.value = false;
    stopResendTimer();
};

const handleAuthEvent = (e) => {
    openModal(e.detail || 'login');
};

onMounted(() => {
    window.addEventListener('open-auth', handleAuthEvent);

    // Check URL parameters for auth actions
    const params = new URLSearchParams(window.location.search);
    const authParam = params.get('auth');
    if (authParam && titles[authParam]) {
        openModal(authParam);

        // Remove param from URL
        const url = new URL(window.location);
        url.searchParams.delete('auth');
        window.history.replaceState({}, '', url);
    }
});

onUnmounted(() => {
    window.removeEventListener('open-auth', handleAuthEvent);
    stopResendTimer();
});
</script>
