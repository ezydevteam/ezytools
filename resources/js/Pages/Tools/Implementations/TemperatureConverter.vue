<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="max-w-3xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
                
                <!-- From -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">From</label>
                        <select v-model="fromUnit" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="c">Celsius (°C)</option>
                            <option value="f">Fahrenheit (°F)</option>
                            <option value="k">Kelvin (K)</option>
                        </select>
                    </div>
                    <div>
                        <input type="number" v-model="inputValue" class="block w-full text-lg font-bold rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0">
                    </div>
                </div>

                <!-- Swap Button -->
                <div class="md:col-span-1 flex justify-center mt-6 md:mt-0">
                    <button @click="swapUnits" class="p-4 bg-surface-100 hover:bg-surface-200 dark:bg-surface-700 dark:hover:bg-surface-600 text-primary-600 dark:text-primary-400 rounded-full transition-colors group">
                        <svg class="w-6 h-6 transform group-hover:rotate-180 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </button>
                </div>

                <!-- To -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">To</label>
                        <select v-model="toUnit" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="c">Celsius (°C)</option>
                            <option value="f">Fahrenheit (°F)</option>
                            <option value="k">Kelvin (K)</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" :value="outputValue" readonly class="block w-full text-lg font-bold rounded-xl border-transparent bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 focus:ring-0 transition-colors cursor-default">
                    </div>
                </div>
            </div>

            <!-- Formula Display -->
            <div class="mt-8 p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 text-center">
                <p class="text-sm text-surface-600 dark:text-surface-400">
                    <span class="font-bold">{{ inputValue || 0 }}</span> {{ unitLabels[fromUnit] }} = <span class="font-bold text-surface-900 dark:text-white">{{ outputValue }}</span> {{ unitLabels[toUnit] }}
                </p>
            </div>
            
            <div class="mt-4 grid grid-cols-3 gap-2">
                <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg text-center border border-blue-100 dark:border-blue-800">
                    <span class="block text-xs text-blue-600 dark:text-blue-400 font-medium">Freezing Point of Water</span>
                    <span class="block font-bold text-surface-900 dark:text-white mt-1">0 °C | 32 °F</span>
                </div>
                <div class="bg-orange-50 dark:bg-orange-900/20 p-3 rounded-lg text-center border border-orange-100 dark:border-orange-800">
                    <span class="block text-xs text-orange-600 dark:text-orange-400 font-medium">Boiling Point of Water</span>
                    <span class="block font-bold text-surface-900 dark:text-white mt-1">100 °C | 212 °F</span>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/20 p-3 rounded-lg text-center border border-purple-100 dark:border-purple-800">
                    <span class="block text-xs text-purple-600 dark:text-purple-400 font-medium">Absolute Zero</span>
                    <span class="block font-bold text-surface-900 dark:text-white mt-1">-273.15 °C | 0 K</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const fromUnit = ref('c');
const toUnit = ref('f');
const inputValue = ref(0);

const unitLabels = {
    c: '°C', f: '°F', k: 'K'
};

const outputValue = computed(() => {
    if (inputValue.value === '' || inputValue.value === null) return 0;
    
    const val = parseFloat(inputValue.value);
    let result = 0;
    
    if (fromUnit.value === toUnit.value) {
        result = val;
    } else if (fromUnit.value === 'c' && toUnit.value === 'f') {
        result = (val * 9/5) + 32;
    } else if (fromUnit.value === 'c' && toUnit.value === 'k') {
        result = val + 273.15;
    } else if (fromUnit.value === 'f' && toUnit.value === 'c') {
        result = (val - 32) * 5/9;
    } else if (fromUnit.value === 'f' && toUnit.value === 'k') {
        result = (val - 32) * 5/9 + 273.15;
    } else if (fromUnit.value === 'k' && toUnit.value === 'c') {
        result = val - 273.15;
    } else if (fromUnit.value === 'k' && toUnit.value === 'f') {
        result = (val - 273.15) * 9/5 + 32;
    }
    
    return Number(result.toFixed(4)).toString(); // Max 4 decimal places
});

const swapUnits = () => {
    const temp = fromUnit.value;
    fromUnit.value = toUnit.value;
    toUnit.value = temp;
};
</script>
