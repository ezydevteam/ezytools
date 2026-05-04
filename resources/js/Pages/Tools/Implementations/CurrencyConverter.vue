<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="max-w-3xl mx-auto">
            <div v-if="loading" class="text-center py-12">
                <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <p class="mt-4 text-surface-500">Loading live exchange rates...</p>
            </div>
            <div v-else-if="error" class="text-center py-12 bg-red-50 dark:bg-red-900/20 rounded-xl">
                <svg class="h-8 w-8 text-red-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="mt-4 text-red-600">{{ error }}</p>
                <button @click="fetchRates" class="mt-4 px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200">Retry</button>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
                
                <!-- From -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Amount</label>
                        <select v-model="fromUnit" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option v-for="(rate, curr) in rates" :key="'from_'+curr" :value="curr">{{ curr }}</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" v-model="inputValue" class="block w-full text-lg font-bold rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0">
                    </div>
                </div>

                <!-- Swap Button -->
                <div class="md:col-span-1 flex justify-center mt-6 md:mt-0">
                    <button @click="swapUnits" class="p-4 bg-surface-100 hover:bg-surface-200 dark:bg-surface-700 dark:hover:bg-surface-600 text-primary-600 dark:text-primary-400 rounded-full transition-colors group shadow-sm">
                        <svg class="w-6 h-6 transform group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </button>
                </div>

                <!-- To -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Converted To</label>
                        <select v-model="toUnit" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option v-for="(rate, curr) in rates" :key="'to_'+curr" :value="curr">{{ curr }}</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" :value="outputValue" readonly class="block w-full text-lg font-bold rounded-xl border-transparent bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 focus:ring-0 transition-colors cursor-default">
                    </div>
                </div>

                <div class="md:col-span-5 mt-4 p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 text-center">
                    <p class="text-sm text-surface-600 dark:text-surface-400">
                        <span class="font-bold">{{ inputValue || 0 }}</span> {{ fromUnit }} = <span class="font-bold text-surface-900 dark:text-white">{{ outputValue }}</span> {{ toUnit }}
                    </p>
                    <p class="text-xs text-surface-400 mt-2">Live exchange rates provided by ExchangeRate-API. Last updated: {{ lastUpdated }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const fromUnit = ref('USD');
const toUnit = ref('EUR');
const inputValue = ref(100);
const rates = ref({});
const loading = ref(true);
const error = ref('');
const lastUpdated = ref('');

const fetchRates = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await fetch('https://open.er-api.com/v6/latest/USD');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        if (data.result === 'success') {
            rates.value = data.rates;
            
            // Re-order rates to put popular ones first
            const popular = ['USD', 'EUR', 'GBP', 'BDT', 'INR', 'CAD', 'AUD', 'JPY'];
            const orderedRates = {};
            popular.forEach(c => {
                if (rates.value[c]) orderedRates[c] = rates.value[c];
            });
            Object.keys(rates.value).forEach(c => {
                if (!popular.includes(c)) orderedRates[c] = rates.value[c];
            });
            rates.value = orderedRates;
            
            lastUpdated.value = new Date(data.time_last_update_utc).toLocaleString();
        } else {
            throw new Error('Failed to load rates');
        }
    } catch (err) {
        error.value = 'Failed to fetch live exchange rates. Please check your internet connection.';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchRates();
});

const outputValue = computed(() => {
    if (inputValue.value === '' || inputValue.value === null || !rates.value[fromUnit.value] || !rates.value[toUnit.value]) return 0;
    
    // Convert from fromUnit to USD, then USD to toUnit
    const valInUSD = inputValue.value / rates.value[fromUnit.value];
    const result = valInUSD * rates.value[toUnit.value];
    
    return result.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});

const swapUnits = () => {
    const temp = fromUnit.value;
    fromUnit.value = toUnit.value;
    toUnit.value = temp;
};
</script>
