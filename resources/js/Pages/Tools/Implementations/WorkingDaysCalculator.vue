<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Start Date</label>
                    <input type="date" v-model="startDate" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">End Date</label>
                    <input type="date" v-model="endDate" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Weekend Days</label>
                    <div class="flex flex-wrap gap-2">
                        <label v-for="(day, index) in weekDays" :key="index" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border cursor-pointer transition-colors" :class="[weekends.includes(index) ? 'bg-primary-50 border-primary-200 text-primary-700 dark:bg-primary-900/30 dark:border-primary-800 dark:text-primary-400' : 'bg-surface-50 border-surface-200 text-surface-600 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-400 hover:bg-surface-100 dark:hover:bg-surface-700']">
                            <input type="checkbox" :value="index" v-model="weekends" class="sr-only">
                            <span class="text-xs font-medium">{{ day.short }}</span>
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-surface-500">Default is Friday & Saturday (Bangladesh standard).</p>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center text-center">
                <div v-if="isValidInput">
                    <span class="text-sm font-bold uppercase tracking-wider block mb-2 text-surface-500 dark:text-surface-400">
                        Total Working Days
                    </span>
                    <span class="text-6xl font-black block mb-6 text-primary-600 dark:text-primary-400">
                        {{ results.workingDays }}
                    </span>
                    
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div class="p-4 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Days</span>
                            <span class="text-lg font-bold text-surface-900 dark:text-white">{{ results.totalDays }}</span>
                        </div>
                        <div class="p-4 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Weekend Days</span>
                            <span class="text-lg font-bold text-red-600 dark:text-red-400">{{ results.weekendDays }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center">
                    <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p>Select both start and end dates to calculate working days.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const weekDays = [
    { short: 'Sun', long: 'Sunday' },
    { short: 'Mon', long: 'Monday' },
    { short: 'Tue', long: 'Tuesday' },
    { short: 'Wed', long: 'Wednesday' },
    { short: 'Thu', long: 'Thursday' },
    { short: 'Fri', long: 'Friday' },
    { short: 'Sat', long: 'Saturday' }
];

const today = new Date();
const nextMonth = new Date();
nextMonth.setMonth(nextMonth.getMonth() + 1);

const formatDateForInput = (date) => {
    return date.toISOString().split('T')[0];
};

const startDate = ref(formatDateForInput(today));
const endDate = ref(formatDateForInput(nextMonth));
// 5 = Friday, 6 = Saturday
const weekends = ref([5, 6]);

const isValidInput = computed(() => {
    return startDate.value && endDate.value && new Date(startDate.value) <= new Date(endDate.value);
});

const results = computed(() => {
    if (!isValidInput.value) return {};

    const start = new Date(startDate.value);
    const end = new Date(endDate.value);
    
    // Include end day
    end.setHours(23, 59, 59, 999);
    start.setHours(0, 0, 0, 0);

    const diffTime = Math.abs(end - start);
    const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    let workingDays = 0;
    let weekendDays = 0;
    
    // Loop through each day
    let currentDate = new Date(start);
    while (currentDate <= end) {
        if (weekends.value.includes(currentDate.getDay())) {
            weekendDays++;
        } else {
            workingDays++;
        }
        currentDate.setDate(currentDate.getDate() + 1);
    }

    return {
        totalDays,
        workingDays,
        weekendDays
    };
});
</script>
