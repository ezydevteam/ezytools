<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Cost Price (CP)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="costPrice" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0.00" min="0">
                    </div>
                    <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Total amount paid to produce or buy the product.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Selling Price (SP)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="sellingPrice" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0.00" min="0">
                    </div>
                    <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">Total amount received from selling the product.</p>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center items-center text-center">
                <div v-if="isValidInput">
                    <div class="mb-4">
                        <span class="text-sm font-bold uppercase tracking-wider block mb-1" :class="statusColor">
                            {{ statusText }}
                        </span>
                        <span class="text-4xl font-black block" :class="statusColor">
                            ৳ {{ formatCurrency(Math.abs(difference)) }}
                        </span>
                    </div>
                    
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full" :class="bgStatusColor">
                        <span class="text-sm font-bold" :class="statusColor">{{ statusText }} Percentage:</span>
                        <span class="text-lg font-bold" :class="statusColor">{{ percentage }}%</span>
                    </div>

                    <div class="mt-6 text-sm text-surface-600 dark:text-surface-400">
                        Margin: <span class="font-bold text-surface-900 dark:text-white">{{ margin }}%</span>
                        <span class="mx-2 text-surface-300 dark:text-surface-600">|</span>
                        Markup: <span class="font-bold text-surface-900 dark:text-white">{{ markup }}%</span>
                    </div>
                </div>
                <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center">
                    <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    <p>Enter both Cost and Selling prices to calculate profit/loss.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const costPrice = ref(500);
const sellingPrice = ref(650);

const isValidInput = computed(() => {
    return Number(costPrice.value) > 0 && Number(sellingPrice.value) >= 0;
});

const difference = computed(() => {
    return Number(sellingPrice.value) - Number(costPrice.value);
});

const isProfit = computed(() => difference.value >= 0);

const statusText = computed(() => {
    if (difference.value === 0) return 'Break Even';
    return isProfit.value ? 'Profit' : 'Loss';
});

const statusColor = computed(() => {
    if (difference.value === 0) return 'text-surface-600 dark:text-surface-400';
    return isProfit.value ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
});

const bgStatusColor = computed(() => {
    if (difference.value === 0) return 'bg-surface-200 dark:bg-surface-700';
    return isProfit.value ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30';
});

const percentage = computed(() => {
    if (Number(costPrice.value) === 0) return 0;
    return Number(((Math.abs(difference.value) / Number(costPrice.value)) * 100).toFixed(2));
});

const margin = computed(() => {
    if (Number(sellingPrice.value) === 0) return 0;
    return Number(((difference.value / Number(sellingPrice.value)) * 100).toFixed(2));
});

const markup = computed(() => {
    return percentage.value; // Markup is the profit percentage based on cost
});

const formatCurrency = (val) => {
    return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>
