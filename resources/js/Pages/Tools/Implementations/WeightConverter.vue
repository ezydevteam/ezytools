<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="max-w-3xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
                
                <!-- From -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">From</label>
                        <select v-model="fromUnit" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <optgroup label="Metric">
                                <option value="mg">Milligrams (mg)</option>
                                <option value="g">Grams (g)</option>
                                <option value="kg">Kilograms (kg)</option>
                                <option value="t">Metric Tonnes (t)</option>
                            </optgroup>
                            <optgroup label="Imperial / US">
                                <option value="oz">Ounces (oz)</option>
                                <option value="lb">Pounds (lb)</option>
                                <option value="st">Stones (st)</option>
                                <option value="ton_us">US Tons (Short)</option>
                                <option value="ton_uk">UK Tons (Long)</option>
                            </optgroup>
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
                            <optgroup label="Metric">
                                <option value="mg">Milligrams (mg)</option>
                                <option value="g">Grams (g)</option>
                                <option value="kg">Kilograms (kg)</option>
                                <option value="t">Metric Tonnes (t)</option>
                            </optgroup>
                            <optgroup label="Imperial / US">
                                <option value="oz">Ounces (oz)</option>
                                <option value="lb">Pounds (lb)</option>
                                <option value="st">Stones (st)</option>
                                <option value="ton_us">US Tons (Short)</option>
                                <option value="ton_uk">UK Tons (Long)</option>
                            </optgroup>
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
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const fromUnit = ref('kg');
const toUnit = ref('lb');
const inputValue = ref(1);

const unitLabels = {
    mg: 'Milligrams', g: 'Grams', kg: 'Kilograms', t: 'Metric Tonnes',
    oz: 'Ounces', lb: 'Pounds', st: 'Stones', ton_us: 'US Tons', ton_uk: 'UK Tons'
};

// Conversion rates relative to 1 Kilogram
const rates = {
    kg: 1,
    g: 1000,
    mg: 1000000,
    t: 0.001,
    lb: 2.20462262,
    oz: 35.2739619,
    st: 0.15747304,
    ton_us: 0.00110231,
    ton_uk: 0.0009842065
};

const outputValue = computed(() => {
    if (inputValue.value === '' || inputValue.value === null) return 0;
    
    const valInKg = inputValue.value / rates[fromUnit.value];
    const result = valInKg * rates[toUnit.value];
    
    return Number(result.toPrecision(7)).toString();
});

const swapUnits = () => {
    const temp = fromUnit.value;
    fromUnit.value = toUnit.value;
    toUnit.value = temp;
};
</script>
