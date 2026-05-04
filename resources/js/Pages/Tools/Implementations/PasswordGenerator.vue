<template>
    <div class="max-w-3xl mx-auto">
        <!-- Result Display -->
        <div class="relative bg-surface-900 rounded-xl p-6 mb-8 flex items-center justify-between shadow-lg">
            <div class="font-mono text-2xl md:text-3xl tracking-wider text-white truncate mr-4 selection:bg-primary-500">
                {{ password }}
            </div>
            <div class="flex gap-2 shrink-0">
                <button @click="generatePassword" class="p-2 text-surface-400 hover:text-white bg-surface-800 hover:bg-surface-700 rounded-lg transition-colors" title="Generate New">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
                <button @click="copyPassword" class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white rounded-lg font-medium transition-colors shadow-sm">
                    Copy
                </button>
            </div>
        </div>

        <!-- Controls -->
        <div class="bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm space-y-8">
            
            <!-- Length Slider -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="text-sm font-semibold text-surface-900 dark:text-white">Password Length</label>
                    <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ length }}</span>
                </div>
                <input 
                    type="range" 
                    v-model.number="length" 
                    min="4" 
                    max="64" 
                    class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-primary-600"
                    @input="generatePassword"
                >
            </div>

            <div class="border-t border-surface-200 dark:border-surface-700"></div>

            <!-- Checkboxes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center p-4 border border-surface-200 dark:border-surface-700 rounded-xl cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors" :class="{'bg-primary-50 border-primary-200 dark:bg-primary-900/20 dark:border-primary-800': options.uppercase}">
                    <input type="checkbox" v-model="options.uppercase" @change="generatePassword" class="w-5 h-5 text-primary-600 rounded border-surface-300 focus:ring-primary-500">
                    <span class="ml-3 font-medium text-surface-900 dark:text-white">Uppercase (A-Z)</span>
                </label>
                
                <label class="flex items-center p-4 border border-surface-200 dark:border-surface-700 rounded-xl cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors" :class="{'bg-primary-50 border-primary-200 dark:bg-primary-900/20 dark:border-primary-800': options.lowercase}">
                    <input type="checkbox" v-model="options.lowercase" @change="generatePassword" class="w-5 h-5 text-primary-600 rounded border-surface-300 focus:ring-primary-500">
                    <span class="ml-3 font-medium text-surface-900 dark:text-white">Lowercase (a-z)</span>
                </label>
                
                <label class="flex items-center p-4 border border-surface-200 dark:border-surface-700 rounded-xl cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors" :class="{'bg-primary-50 border-primary-200 dark:bg-primary-900/20 dark:border-primary-800': options.numbers}">
                    <input type="checkbox" v-model="options.numbers" @change="generatePassword" class="w-5 h-5 text-primary-600 rounded border-surface-300 focus:ring-primary-500">
                    <span class="ml-3 font-medium text-surface-900 dark:text-white">Numbers (0-9)</span>
                </label>
                
                <label class="flex items-center p-4 border border-surface-200 dark:border-surface-700 rounded-xl cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors" :class="{'bg-primary-50 border-primary-200 dark:bg-primary-900/20 dark:border-primary-800': options.symbols}">
                    <input type="checkbox" v-model="options.symbols" @change="generatePassword" class="w-5 h-5 text-primary-600 rounded border-surface-300 focus:ring-primary-500">
                    <span class="ml-3 font-medium text-surface-900 dark:text-white">Symbols (!@#$%^&*)</span>
                </label>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const password = ref('');
const length = ref(16);

const options = ref({
    uppercase: true,
    lowercase: true,
    numbers: true,
    symbols: true
});

const generatePassword = () => {
    const chars = {
        uppercase: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        lowercase: 'abcdefghijklmnopqrstuvwxyz',
        numbers: '0123456789',
        symbols: '!@#$%^&*()_+~`|}{[]:;?><,./-='
    };

    let allowedChars = '';
    if (options.value.uppercase) allowedChars += chars.uppercase;
    if (options.value.lowercase) allowedChars += chars.lowercase;
    if (options.value.numbers) allowedChars += chars.numbers;
    if (options.value.symbols) allowedChars += chars.symbols;

    // Fallback if user unchecks everything
    if (!allowedChars) {
        options.value.lowercase = true;
        allowedChars = chars.lowercase;
    }

    let result = '';
    // Ensure at least one of each selected type
    if (options.value.uppercase) result += chars.uppercase[Math.floor(Math.random() * chars.uppercase.length)];
    if (options.value.lowercase) result += chars.lowercase[Math.floor(Math.random() * chars.lowercase.length)];
    if (options.value.numbers) result += chars.numbers[Math.floor(Math.random() * chars.numbers.length)];
    if (options.value.symbols) result += chars.symbols[Math.floor(Math.random() * chars.symbols.length)];

    // Fill the rest
    for (let i = result.length; i < length.value; i++) {
        const randomIndex = Math.floor(Math.random() * allowedChars.length);
        result += allowedChars[randomIndex];
    }

    // Shuffle the result so the guaranteed characters aren't always at the beginning
    password.value = result.split('').sort(() => 0.5 - Math.random()).join('');
};

const copyPassword = () => {
    if (!password.value) return;
    navigator.clipboard.writeText(password.value).then(() => {
        toast.success("Password copied to clipboard!");
    });
};

onMounted(() => {
    generatePassword();
});
</script>
