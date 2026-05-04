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
                                <option value="mm">Millimeters (mm)</option>
                                <option value="cm">Centimeters (cm)</option>
                                <option value="m">Meters (m)</option>
                                <option value="km">Kilometers (km)</option>
                            </optgroup>
                            <optgroup label="Imperial / US">
                                <option value="in">Inches (in)</option>
                                <option value="ft">Feet (ft)</option>
                                <option value="yd">Yards (yd)</option>
                                <option value="mi">Miles (mi)</option>
                            </optgroup>
                            <optgroup label="Other">
                                <option value="nm">Nautical Miles (nmi)</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <input type="number" v-model="inputValue" @input="calculate" class="block w-full text-lg font-bold rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0">
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
                                <option value="mm">Millimeters (mm)</option>
                                <option value="cm">Centimeters (cm)</option>
                                <option value="m">Meters (m)</option>
                                <option value="km">Kilometers (km)</option>
                            </optgroup>
                            <optgroup label="Imperial / US">
                                <option value="in">Inches (in)</option>
                                <option value="ft">Feet (ft)</option>
                                <option value="yd">Yards (yd)</option>
                                <option value="mi">Miles (mi)</option>
                            </optgroup>
                            <optgroup label="Other">
                                <option value="nm">Nautical Miles (nmi)</option>
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
import { ref, computed, watch } from 'vue';

const fromUnit = ref('m');
const toUnit = ref('ft');
const inputValue = ref(1);

const unitLabels = {
    mm: 'Millimeters', cm: 'Centimeters', m: 'Meters', km: 'Kilometers',
    in: 'Inches', ft: 'Feet', yd: 'Yards', mi: 'Miles', nm: 'Nautical Miles'
};

// Conversion rates relative to 1 Meter
const rates = {
    m: 1,
    mm: 1000,
    cm: 100,
    km: 0.001,
    in: 39.3700787,
    ft: 3.2808399,
    yd: 1.0936133,
    mi: 0.0006213712,
    nm: 0.0005399568
};

const outputValue = computed(() => {
    if (inputValue.value === '' || inputValue.value === null) return 0;
    
    const valInMeters = inputValue.value / rates[fromUnit.value];
    const result = valInMeters * rates[toUnit.value];
    
    // Format to avoid extreme decimals but keep precision
    return Number(result.toPrecision(7)).toString();
});

const swapUnits = () => {
    const temp = fromUnit.value;
    fromUnit.value = toUnit.value;
    toUnit.value = temp;
};

const calculate = () => {};
</script>
