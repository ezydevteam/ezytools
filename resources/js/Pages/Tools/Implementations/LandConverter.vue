<template>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm flex flex-col md:flex-row items-center gap-6">
            
            <!-- From -->
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">From</label>
                <div class="relative">
                    <input 
                        type="number" 
                        v-model="value" 
                        @input="calculate"
                        class="block w-full pl-4 pr-24 py-4 rounded-xl border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-xl font-medium dark:bg-surface-900 dark:border-surface-700 dark:text-white" 
                        placeholder="0.00"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center">
                        <select v-model="fromUnit" @change="calculate" class="h-full rounded-r-xl border-transparent bg-transparent py-0 pl-2 pr-7 text-surface-500 focus:border-transparent focus:ring-0 sm:text-sm dark:bg-surface-800 dark:text-surface-300 font-semibold cursor-pointer border-l border-surface-200 dark:border-surface-700">
                            <option v-for="(name, key) in units" :key="key" :value="key">{{ name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Swap Button -->
            <button @click="swap" class="p-3 bg-surface-100 hover:bg-primary-100 text-surface-500 hover:text-primary-600 dark:bg-surface-700 dark:hover:bg-primary-900/50 dark:text-surface-400 dark:hover:text-primary-400 rounded-full transition-colors mt-6 md:mt-0 shrink-0 shadow-sm border border-surface-200 dark:border-surface-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
            </button>

            <!-- To -->
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">To</label>
                <div class="relative">
                    <input 
                        type="number" 
                        readonly
                        :value="result" 
                        class="block w-full pl-4 pr-24 py-4 rounded-xl border-surface-300 bg-surface-50 shadow-inner focus:border-surface-300 focus:ring-0 text-xl font-medium text-primary-600 dark:bg-surface-900/50 dark:border-surface-700 dark:text-primary-400" 
                        placeholder="0.00"
                    >
                    <div class="absolute inset-y-0 right-0 flex items-center">
                        <select v-model="toUnit" @change="calculate" class="h-full rounded-r-xl border-transparent bg-transparent py-0 pl-2 pr-7 text-surface-500 focus:border-transparent focus:ring-0 sm:text-sm dark:bg-surface-800 dark:text-surface-300 font-semibold cursor-pointer border-l border-surface-200 dark:border-surface-700">
                            <option v-for="(name, key) in units" :key="key" :value="key">{{ name }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Table -->
        <div class="mt-8 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm overflow-hidden">
            <div class="px-4 py-3 bg-surface-50 dark:bg-surface-900/50 border-b border-surface-200 dark:border-surface-700 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-surface-700 dark:text-surface-300">1 {{ units[fromUnit] }} Equivalents</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4">
                <div v-for="(name, key) in units" :key="'table'+key" class="p-3 bg-surface-50 dark:bg-surface-900 rounded-lg text-center">
                    <div class="text-xs text-surface-500 mb-1 tracking-wider">{{ name }}</div>
                    <div class="font-mono text-sm font-semibold text-surface-900 dark:text-white">{{ getEquivalent(key) }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const value = ref(1);
const result = ref(0);
const fromUnit = ref('shatak');
const toUnit = ref('sqft');

// Conversion rates relative to 1 Square Foot
const rates = {
    sqft: 1,
    sqm: 10.7639,
    shatak: 435.6, // 1 Decimal/Shatak = 435.6 sq ft
    katha: 720,    // 1 Katha = 720 sq ft
    bigha: 14400,  // 1 Bigha = 20 Katha = 14400 sq ft
    acre: 43560,   // 1 Acre = 100 Shatak = 43560 sq ft
    hectare: 107639.104, // 1 Hectare = 2.471 Acres
    kani: 17280    // Standard Kani ~ 40 decimals = 17424, but traditional Kani varies. Using 40 decimals (17424 sq ft)
};

// Override Kani with more accurate standard
rates.kani = 435.6 * 40; 

const units = {
    shatak: 'Shatak / Decimal (শতক)',
    katha: 'Katha (কাঠা)',
    bigha: 'Bigha (বিঘা)',
    acre: 'Acre (একর)',
    kani: 'Kani (কানি - 40 Dec)',
    sqft: 'Square Feet (বর্গফুট)',
    sqm: 'Square Meter (বর্গমিটার)',
    hectare: 'Hectare (হেক্টর)'
};

const calculate = () => {
    if (!value.value) {
        result.value = '';
        return;
    }
    
    // Convert input to sqft first
    const sqft = value.value * rates[fromUnit.value];
    
    // Convert sqft to target unit
    let final = sqft / rates[toUnit.value];
    
    result.value = Number(final.toFixed(6));
};

const getEquivalent = (targetUnit) => {
    const sqft = 1 * rates[fromUnit.value];
    const final = sqft / rates[targetUnit];
    
    if (final < 0.0001) return final.toExponential(4);
    return Number(final.toFixed(4));
};

const swap = () => {
    const temp = fromUnit.value;
    fromUnit.value = toUnit.value;
    toUnit.value = temp;
    calculate();
};

onMounted(() => {
    calculate();
});
</script>
