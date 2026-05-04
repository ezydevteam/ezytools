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
                
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="includeEndDay" v-model="includeEndDay" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                    <label for="includeEndDay" class="text-sm text-surface-600 dark:text-surface-400 cursor-pointer">Include end day in calculation (+1 day)</label>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center text-center">
                <div v-if="isValidInput">
                    <span class="text-sm font-bold uppercase tracking-wider block mb-2 text-surface-500 dark:text-surface-400">
                        Difference
                    </span>
                    <span class="text-3xl sm:text-4xl font-black block mb-6 text-primary-600 dark:text-primary-400">
                        {{ results.years > 0 ? results.years + 'y ' : '' }}{{ results.months > 0 ? results.months + 'm ' : '' }}{{ results.days }}d
                    </span>
                    
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div class="p-4 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Days</span>
                            <span class="text-lg font-bold text-surface-900 dark:text-white">{{ formatNumber(results.totalDays) }}</span>
                        </div>
                        <div class="p-4 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Weeks</span>
                            <span class="text-lg font-bold text-surface-900 dark:text-white">{{ formatNumber(results.totalWeeks) }}</span>
                        </div>
                        <div class="p-4 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Months</span>
                            <span class="text-lg font-bold text-surface-900 dark:text-white">{{ formatNumber(results.totalMonths) }}</span>
                        </div>
                        <div class="p-4 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Hours</span>
                            <span class="text-lg font-bold text-surface-900 dark:text-white">{{ formatNumber(results.totalHours) }}</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center">
                    <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p>Select both start and end dates to calculate the difference.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// Initialize with today and today + 1 month
const today = new Date();
const nextMonth = new Date();
nextMonth.setMonth(nextMonth.getMonth() + 1);

const formatDateForInput = (date) => {
    return date.toISOString().split('T')[0];
};

const startDate = ref(formatDateForInput(today));
const endDate = ref(formatDateForInput(nextMonth));
const includeEndDay = ref(false);

const isValidInput = computed(() => {
    return startDate.value && endDate.value;
});

const results = computed(() => {
    if (!isValidInput.value) return {};

    const start = new Date(startDate.value);
    const end = new Date(endDate.value);
    
    // Swap if end is before start
    let isNegative = false;
    let sDate = new Date(start);
    let eDate = new Date(end);
    
    if (sDate > eDate) {
        let temp = sDate;
        sDate = eDate;
        eDate = temp;
        isNegative = true;
    }

    if (includeEndDay.value) {
        eDate.setDate(eDate.getDate() + 1);
    }

    // Total units
    const diffTime = Math.abs(eDate - sDate);
    const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const totalHours = totalDays * 24;
    const totalWeeks = (totalDays / 7).toFixed(1);
    
    // Years, Months, Days logic
    let years = eDate.getFullYear() - sDate.getFullYear();
    let months = eDate.getMonth() - sDate.getMonth();
    let days = eDate.getDate() - sDate.getDate();

    if (days < 0) {
        months--;
        // Get days in previous month
        let prevMonth = new Date(eDate.getFullYear(), eDate.getMonth(), 0).getDate();
        days += prevMonth;
    }
    
    if (months < 0) {
        years--;
        months += 12;
    }
    
    const totalMonthsExact = (years * 12) + months + (days / 30.44); // Approx

    return {
        years,
        months,
        days,
        totalDays,
        totalWeeks: Number(totalWeeks),
        totalMonths: Number(totalMonthsExact.toFixed(1)),
        totalHours,
        isNegative
    };
});

const formatNumber = (val) => {
    return Number(val).toLocaleString('en-US');
};
</script>
