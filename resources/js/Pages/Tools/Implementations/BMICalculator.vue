<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <!-- Unit Toggle -->
                <div class="flex p-1 bg-surface-100 dark:bg-surface-900 rounded-xl">
                    <button @click="unitSystem = 'metric'" :class="[unitSystem === 'metric' ? 'bg-white dark:bg-surface-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white']" class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">Metric (cm, kg)</button>
                    <button @click="unitSystem = 'imperial'" :class="[unitSystem === 'imperial' ? 'bg-white dark:bg-surface-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white']" class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">Imperial (ft, lbs)</button>
                </div>

                <!-- Metric Inputs -->
                <div v-if="unitSystem === 'metric'" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Height (cm)</label>
                        <input type="number" v-model="heightCm" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="170" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Weight (kg)</label>
                        <input type="number" v-model="weightKg" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="70" min="0">
                    </div>
                </div>

                <!-- Imperial Inputs -->
                <div v-else class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Height</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative">
                                <input type="number" v-model="heightFt" class="block w-full pr-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="5" min="0">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-surface-500 font-medium">ft</span>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="number" v-model="heightIn" class="block w-full pr-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="7" min="0" max="11">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-surface-500 font-medium">in</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Weight (lbs)</label>
                        <input type="number" v-model="weightLbs" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="150" min="0">
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center items-center text-center">
                <div v-if="bmi > 0">
                    <span class="text-sm font-bold uppercase tracking-wider block mb-2 text-surface-500 dark:text-surface-400">Your BMI Is</span>
                    <span class="text-6xl font-black block mb-2" :class="categoryColor">
                        {{ bmi }}
                    </span>
                    <div class="inline-flex items-center px-4 py-2 rounded-full" :class="categoryBgColor">
                        <span class="text-lg font-bold" :class="categoryColor">{{ categoryName }}</span>
                    </div>

                    <div class="mt-8 w-full">
                        <div class="h-4 w-full rounded-full bg-surface-200 dark:bg-surface-700 flex overflow-hidden">
                            <div class="h-full bg-blue-400 w-[18.5%]" title="Underweight (< 18.5)"></div>
                            <div class="h-full bg-green-500 w-[25%]" title="Normal (18.5 - 24.9)"></div>
                            <div class="h-full bg-yellow-400 w-[20%]" title="Overweight (25 - 29.9)"></div>
                            <div class="h-full bg-red-500 w-[36.5%]" title="Obese (30+)"></div>
                        </div>
                        <div class="flex justify-between text-[10px] sm:text-xs text-surface-500 font-medium mt-1">
                            <span>0</span>
                            <span class="pl-2">18.5</span>
                            <span class="pl-2">25</span>
                            <span class="pl-2">30</span>
                            <span>40+</span>
                        </div>
                        
                        <!-- Indicator pointer -->
                        <div class="relative w-full h-2 mt-1">
                            <div class="absolute w-3 h-3 bg-surface-900 dark:bg-white rotate-45 transform -translate-x-1/2 -top-1" :style="{ left: pointerPosition + '%' }"></div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center">
                    <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p>Enter your height and weight to calculate your BMI.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const unitSystem = ref('metric');

// Metric
const heightCm = ref(170);
const weightKg = ref(70);

// Imperial
const heightFt = ref(5);
const heightIn = ref(7);
const weightLbs = ref(154);

const bmi = computed(() => {
    let h, w;
    if (unitSystem.value === 'metric') {
        h = Number(heightCm.value) / 100; // cm to meters
        w = Number(weightKg.value);
        if (h > 0 && w > 0) {
            return Number((w / (h * h)).toFixed(1));
        }
    } else {
        let inches = (Number(heightFt.value) * 12) + Number(heightIn.value);
        w = Number(weightLbs.value);
        if (inches > 0 && w > 0) {
            return Number(((w / (inches * inches)) * 703).toFixed(1));
        }
    }
    return 0;
});

const pointerPosition = computed(() => {
    if (bmi.value <= 0) return 0;
    // Map BMI roughly to percentage 0-40+
    // Underweight (0-18.5) maps to 0-18.5%
    // Normal (18.5-25) maps to 18.5-43.5%
    // Overweight (25-30) maps to 43.5-63.5%
    // Obese (30+) maps to 63.5-100%
    
    let b = bmi.value;
    if (b > 40) b = 40;
    
    // Simplistic mapping for the visual bar:
    // 0-18.5 takes 18.5% of width
    // 18.5-25 (diff 6.5) takes 25% of width
    // 25-30 (diff 5) takes 20% of width
    // 30-40 (diff 10) takes 36.5% of width
    
    if (b <= 18.5) return (b / 18.5) * 18.5;
    if (b <= 25) return 18.5 + ((b - 18.5) / 6.5) * 25;
    if (b <= 30) return 43.5 + ((b - 25) / 5) * 20;
    return 63.5 + ((b - 30) / 10) * 36.5;
});

const categoryName = computed(() => {
    let b = bmi.value;
    if (b === 0) return '';
    if (b < 18.5) return 'Underweight';
    if (b < 25) return 'Normal Weight';
    if (b < 30) return 'Overweight';
    return 'Obese';
});

const categoryColor = computed(() => {
    let b = bmi.value;
    if (b === 0) return '';
    if (b < 18.5) return 'text-blue-600 dark:text-blue-400';
    if (b < 25) return 'text-green-600 dark:text-green-400';
    if (b < 30) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-red-600 dark:text-red-400';
});

const categoryBgColor = computed(() => {
    let b = bmi.value;
    if (b === 0) return '';
    if (b < 18.5) return 'bg-blue-100 dark:bg-blue-900/30';
    if (b < 25) return 'bg-green-100 dark:bg-green-900/30';
    if (b < 30) return 'bg-yellow-100 dark:bg-yellow-900/30';
    return 'bg-red-100 dark:bg-red-900/30';
});
</script>
