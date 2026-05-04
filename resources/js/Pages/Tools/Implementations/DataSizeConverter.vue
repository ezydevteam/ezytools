<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="max-w-3xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
                
                <!-- From -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">From</label>
                        <select v-model="fromUnit" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="b">Bits (b)</option>
                            <option value="B">Bytes (B)</option>
                            <option value="KB">Kilobytes (KB)</option>
                            <option value="MB">Megabytes (MB)</option>
                            <option value="GB">Gigabytes (GB)</option>
                            <option value="TB">Terabytes (TB)</option>
                            <option value="PB">Petabytes (PB)</option>
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
                            <option value="b">Bits (b)</option>
                            <option value="B">Bytes (B)</option>
                            <option value="KB">Kilobytes (KB)</option>
                            <option value="MB">Megabytes (MB)</option>
                            <option value="GB">Gigabytes (GB)</option>
                            <option value="TB">Terabytes (TB)</option>
                            <option value="PB">Petabytes (PB)</option>
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
                <p class="text-xs text-surface-400 mt-2">Note: Uses base-1024 binary standard (1 KB = 1024 Bytes)</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const fromUnit = ref('MB');
const toUnit = ref('GB');
const inputValue = ref(1024);

const unitLabels = {
    b: 'Bits', B: 'Bytes', KB: 'Kilobytes', MB: 'Megabytes',
    GB: 'Gigabytes', TB: 'Terabytes', PB: 'Petabytes'
};

// Rates relative to Bytes
const rates = {
    b: 1 / 8,
    B: 1,
    KB: 1024,
    MB: Math.pow(1024, 2),
    GB: Math.pow(1024, 3),
    TB: Math.pow(1024, 4),
    PB: Math.pow(1024, 5)
};

const outputValue = computed(() => {
    if (inputValue.value === '' || inputValue.value === null) return 0;
    
    const valInBytes = inputValue.value * rates[fromUnit.value];
    const result = valInBytes / rates[toUnit.value];
    
    // Auto format extremely large or small numbers
    if (result > 1e12 || result < 1e-12) {
        return result.toExponential(4);
    }
    
    return Number(result.toPrecision(10)).toString();
});

const swapUnits = () => {
    const temp = fromUnit.value;
    fromUnit.value = toUnit.value;
    toUnit.value = temp;
};
</script>
