<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Type 1: What is X% of Y? -->
            <div class="p-6 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-100 dark:border-surface-700">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-4">What is X% of Y?</h3>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">What is</span>
                    <input type="number" v-model="t1_x" class="w-20 rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 text-center" placeholder="X">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">% of</span>
                    <input type="number" v-model="t1_y" class="w-24 rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 text-center" placeholder="Y">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">?</span>
                </div>
                <div class="text-center p-3 bg-white dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-600">
                    <span class="text-sm text-surface-500 dark:text-surface-400 block mb-1">Result</span>
                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ t1_result }}</span>
                </div>
            </div>

            <!-- Type 2: X is what % of Y? -->
            <div class="p-6 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-100 dark:border-surface-700">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-4">X is what % of Y?</h3>
                <div class="flex items-center gap-2 mb-4">
                    <input type="number" v-model="t2_x" class="w-24 rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 text-center" placeholder="X">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">is what % of</span>
                    <input type="number" v-model="t2_y" class="w-24 rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 text-center" placeholder="Y">
                    <span class="text-sm font-medium text-surface-700 dark:text-surface-300">?</span>
                </div>
                <div class="text-center p-3 bg-white dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-600">
                    <span class="text-sm text-surface-500 dark:text-surface-400 block mb-1">Result</span>
                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ t2_result }}%</span>
                </div>
            </div>

            <!-- Type 3: % Increase/Decrease -->
            <div class="p-6 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-100 dark:border-surface-700">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-4">Percentage Change</h3>
                <div class="flex flex-col gap-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300 w-12">From</span>
                        <input type="number" v-model="t3_x" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 text-center" placeholder="Original Value">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-surface-700 dark:text-surface-300 w-12">To</span>
                        <input type="number" v-model="t3_y" class="w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white px-3 py-1.5 focus:ring-primary-500 focus:border-primary-500 text-center" placeholder="New Value">
                    </div>
                </div>
                <div class="text-center p-3 bg-white dark:bg-surface-800 rounded-lg border border-surface-200 dark:border-surface-600">
                    <span class="text-sm text-surface-500 dark:text-surface-400 block mb-1">Change</span>
                    <span class="text-2xl font-bold" :class="t3_color">{{ t3_result }}%</span>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Type 1
const t1_x = ref(10);
const t1_y = ref(150);
const t1_result = computed(() => {
    if (t1_x.value === '' || t1_y.value === '') return '-';
    return Number(((t1_x.value / 100) * t1_y.value).toFixed(2));
});

// Type 2
const t2_x = ref(30);
const t2_y = ref(150);
const t2_result = computed(() => {
    if (t2_x.value === '' || t2_y.value === '' || Number(t2_y.value) === 0) return '-';
    return Number(((t2_x.value / t2_y.value) * 100).toFixed(2));
});

// Type 3
const t3_x = ref(50);
const t3_y = ref(75);
const t3_result = computed(() => {
    if (t3_x.value === '' || t3_y.value === '' || Number(t3_x.value) === 0) return '-';
    let change = ((t3_y.value - t3_x.value) / t3_x.value) * 100;
    return (change > 0 ? '+' : '') + Number(change.toFixed(2));
});
const t3_color = computed(() => {
    if (t3_result.value === '-') return 'text-primary-600 dark:text-primary-400';
    return t3_result.value.toString().startsWith('+') ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
});
</script>
