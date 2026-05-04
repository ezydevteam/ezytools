<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Principal Amount (P)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="principal" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="10000" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Annual Interest Rate (R)</label>
                    <div class="relative">
                        <input type="number" v-model="rate" class="block w-full pr-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="5" min="0" step="0.1">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">%</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Time Period (T)</label>
                    <div class="flex">
                        <input type="number" v-model="time" class="block w-full rounded-l-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="1" min="0" step="0.5">
                        <select v-model="timeUnit" class="rounded-r-xl border-l-0 border-surface-300 dark:border-surface-600 bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 focus:ring-primary-500 focus:border-primary-500 font-medium">
                            <option value="years">Years</option>
                            <option value="months">Months</option>
                            <option value="days">Days</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center">
                <div class="space-y-6">
                    <div>
                        <span class="text-sm text-surface-500 dark:text-surface-400 block mb-1">Total Interest Earned</span>
                        <span class="text-3xl font-bold text-green-600 dark:text-green-400">+ ৳ {{ formatCurrency(interest) }}</span>
                    </div>
                    
                    <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                        <div class="flex justify-between items-center mb-2 text-surface-600 dark:text-surface-400">
                            <span>Principal Amount:</span>
                            <span class="font-medium">৳ {{ formatCurrency(principal) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-surface-900 dark:text-white">Total Value:</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">৳ {{ formatCurrency(totalAmount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const principal = ref(10000);
const rate = ref(5);
const time = ref(1);
const timeUnit = ref('years');

const timeInYears = computed(() => {
    let t = Number(time.value) || 0;
    if (timeUnit.value === 'months') return t / 12;
    if (timeUnit.value === 'days') return t / 365;
    return t; // years
});

const interest = computed(() => {
    let p = Number(principal.value) || 0;
    let r = Number(rate.value) || 0;
    
    // Simple Interest Formula: I = (P * R * T) / 100
    return (p * r * timeInYears.value) / 100;
});

const totalAmount = computed(() => {
    let p = Number(principal.value) || 0;
    return p + interest.value;
});

const formatCurrency = (val) => {
    return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>
