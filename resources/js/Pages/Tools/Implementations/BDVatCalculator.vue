<template>
    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Calculator Form -->
        <div class="space-y-6 bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Calculation Type</label>
                <div class="grid grid-cols-2 gap-2 bg-surface-100 dark:bg-surface-900 p-1 rounded-lg">
                    <button 
                        @click="mode = 'add'" 
                        :class="mode === 'add' ? 'bg-white dark:bg-surface-700 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500 hover:text-surface-700'"
                        class="py-2 px-4 rounded-md text-sm font-medium transition-all"
                    >
                        Add VAT (Exclusive)
                    </button>
                    <button 
                        @click="mode = 'remove'" 
                        :class="mode === 'remove' ? 'bg-white dark:bg-surface-700 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500 hover:text-surface-700'"
                        class="py-2 px-4 rounded-md text-sm font-medium transition-all"
                    >
                        Remove VAT (Inclusive)
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Amount (৳)</label>
                <input type="number" v-model.number="amount" min="0" class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-lg dark:bg-surface-900 dark:border-surface-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">VAT Rate (%)</label>
                <div class="flex flex-wrap gap-2 mb-3">
                    <button v-for="rate in standardRates" :key="rate" 
                            @click="vatRate = rate"
                            class="px-3 py-1.5 text-sm rounded-full border transition-colors"
                            :class="vatRate === rate ? 'bg-primary-100 border-primary-500 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-white dark:bg-surface-800 border-surface-300 text-surface-600 dark:text-surface-300 hover:bg-surface-50'">
                        {{ rate }}%
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-surface-500">Custom:</span>
                    <input type="number" v-model.number="vatRate" min="0" step="0.1" class="w-24 rounded-md border-surface-300 shadow-sm focus:border-primary-500 sm:text-sm dark:bg-surface-900 dark:border-surface-700 dark:text-white">
                    <span class="text-sm text-surface-500">%</span>
                </div>
            </div>

        </div>

        <!-- Result Card -->
        <div class="flex flex-col gap-4">
            <div class="bg-gradient-to-br from-green-500 to-green-700 p-6 rounded-xl shadow-lg text-white">
                <h3 class="text-green-100 font-medium mb-1 uppercase tracking-wide text-xs">
                    {{ mode === 'add' ? 'Total Amount (With VAT)' : 'Base Amount (Without VAT)' }}
                </h3>
                <div class="text-4xl font-bold mb-4">
                    ৳ {{ formatMoney(mode === 'add' ? results.netAmount : results.baseAmount) }}
                </div>
                
                <div class="space-y-2 text-sm border-t border-green-400/30 pt-4">
                    <div class="flex justify-between">
                        <span class="text-green-100">{{ mode === 'add' ? 'Base Amount' : 'Total Amount' }}</span>
                        <span>৳ {{ formatMoney(mode === 'add' ? results.baseAmount : results.netAmount) }}</span>
                    </div>
                    <div class="flex justify-between font-bold">
                        <span class="text-green-100">VAT Amount ({{ vatRate }}%)</span>
                        <span>৳ {{ formatMoney(results.vatAmount) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-surface-50 dark:bg-surface-800/50 p-4 rounded-xl border border-surface-200 dark:border-surface-700 text-sm text-surface-600 dark:text-surface-400">
                <p class="font-medium mb-1 text-surface-900 dark:text-white">Formula Used:</p>
                <div v-if="mode === 'add'" class="font-mono text-xs">
                    VAT = Amount × (Rate / 100)<br>
                    Total = Amount + VAT
                </div>
                <div v-else class="font-mono text-xs">
                    Base = Amount / (1 + (Rate / 100))<br>
                    VAT = Amount - Base
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const mode = ref('add'); // 'add' or 'remove'
const amount = ref(1000);
const vatRate = ref(15);
const standardRates = [15, 10, 7.5, 5, 2.4, 2]; // Common BD VAT rates

const results = computed(() => {
    const baseInput = Number(amount.value) || 0;
    const rate = Number(vatRate.value) || 0;

    if (mode.value === 'add') {
        // Price is exclusive of VAT
        const vatAmount = baseInput * (rate / 100);
        return {
            baseAmount: baseInput,
            vatAmount: vatAmount,
            netAmount: baseInput + vatAmount
        };
    } else {
        // Price is inclusive of VAT
        const baseAmount = baseInput / (1 + (rate / 100));
        const vatAmount = baseInput - baseAmount;
        return {
            baseAmount: baseAmount,
            vatAmount: vatAmount,
            netAmount: baseInput
        };
    }
});

const formatMoney = (value) => {
    return Number(value).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>
