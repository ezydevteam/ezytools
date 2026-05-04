<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="max-w-3xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-center">
                
                <!-- From -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">From</label>
                        <select v-model="fromUnit" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="kmh">Kilometers per hour (km/h)</option>
                            <option value="mph">Miles per hour (mph)</option>
                            <option value="ms">Meters per second (m/s)</option>
                            <option value="kn">Knots (kn)</option>
                            <option value="mach">Mach</option>
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
                            <option value="kmh">Kilometers per hour (km/h)</option>
                            <option value="mph">Miles per hour (mph)</option>
                            <option value="ms">Meters per second (m/s)</option>
                            <option value="kn">Knots (kn)</option>
                            <option value="mach">Mach</option>
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

const fromUnit = ref('kmh');
const toUnit = ref('mph');
const inputValue = ref(100);

const unitLabels = {
    kmh: 'Kilometers per hour',
    mph: 'Miles per hour',
    ms: 'Meters per second',
    kn: 'Knots',
    mach: 'Mach (Speed of Sound)'
};

// Rates relative to km/h
const rates = {
    kmh: 1,
    mph: 0.621371,
    ms: 0.277778,
    kn: 0.539957,
    mach: 0.000809848 // Approx at sea level standard temp
};

const outputValue = computed(() => {
    if (inputValue.value === '' || inputValue.value === null) return 0;
    
    // Convert input to km/h first
    const valInKmh = inputValue.value / rates[fromUnit.value];
    
    // Convert km/h to target
    const result = valInKmh * rates[toUnit.value];
    
    return Number(result.toPrecision(7)).toString();
});

const swapUnits = () => {
    const temp = fromUnit.value;
    fromUnit.value = toUnit.value;
    toUnit.value = temp;
};
</script>
