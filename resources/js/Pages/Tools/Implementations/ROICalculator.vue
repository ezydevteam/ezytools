<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Amount Invested</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="investedAmount" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="10000" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Amount Returned</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="returnedAmount" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="12500" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Investment Duration (Optional)</label>
                    <div class="flex">
                        <input type="number" v-model="duration" class="block w-full rounded-l-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="1" min="0" step="0.5">
                        <select v-model="durationUnit" class="rounded-r-xl border-l-0 border-surface-300 dark:border-surface-600 bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 focus:ring-primary-500 focus:border-primary-500 font-medium">
                            <option value="years">Years</option>
                            <option value="months">Months</option>
                            <option value="days">Days</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center items-center text-center">
                <div v-if="isValidInput">
                    <span class="text-sm font-bold uppercase tracking-wider block mb-2" :class="statusColor">
                        Return on Investment (ROI)
                    </span>
                    <span class="text-6xl font-black block mb-2" :class="statusColor">
                        {{ formatROI(roiPercentage) }}%
                    </span>
                    
                    <div class="mt-6 w-full space-y-3">
                        <div class="flex justify-between items-center text-sm border-b border-surface-200 dark:border-surface-700 pb-2">
                            <span class="text-surface-600 dark:text-surface-400">Net Profit / Loss:</span>
                            <span class="font-bold" :class="statusColor">৳ {{ formatCurrency(netProfit) }}</span>
                        </div>
                        
                        <div v-if="annualizedROI !== null" class="flex justify-between items-center text-sm border-b border-surface-200 dark:border-surface-700 pb-2">
                            <span class="text-surface-600 dark:text-surface-400">Annualized ROI:</span>
                            <span class="font-bold text-surface-900 dark:text-white">{{ formatROI(annualizedROI) }}% / year</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center">
                    <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    <p>Enter invested and returned amounts to calculate ROI.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const investedAmount = ref(10000);
const returnedAmount = ref(12500);
const duration = ref(1);
const durationUnit = ref('years');

const isValidInput = computed(() => {
    return Number(investedAmount.value) > 0 && Number(returnedAmount.value) >= 0;
});

const netProfit = computed(() => {
    return Number(returnedAmount.value) - Number(investedAmount.value);
});

const roiPercentage = computed(() => {
    if (!isValidInput.value) return 0;
    return (netProfit.value / Number(investedAmount.value)) * 100;
});

const isProfit = computed(() => netProfit.value >= 0);

const statusColor = computed(() => {
    if (netProfit.value === 0) return 'text-surface-600 dark:text-surface-400';
    return isProfit.value ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
});

const durationInYears = computed(() => {
    let d = Number(duration.value) || 0;
    if (d <= 0) return null;
    if (durationUnit.value === 'months') return d / 12;
    if (durationUnit.value === 'days') return d / 365;
    return d; // years
});

const annualizedROI = computed(() => {
    if (!isValidInput.value || durationInYears.value === null) return null;
    
    // Formula: ((Returned / Invested) ^ (1 / Years)) - 1
    let ratio = Number(returnedAmount.value) / Number(investedAmount.value);
    
    // If ratio is 0 or negative (not possible with standard params, but just in case)
    if (ratio <= 0) return -100;
    
    return (Math.pow(ratio, 1 / durationInYears.value) - 1) * 100;
});

const formatCurrency = (val) => {
    return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatROI = (val) => {
    return Number(val).toFixed(2);
};
</script>
