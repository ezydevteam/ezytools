<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Date of Birth</label>
                    <input type="date" v-model="dob" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Calculate Age At</label>
                    <input type="date" v-model="targetDate" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center text-center">
                <div v-if="isValidInput">
                    <span class="text-sm font-bold uppercase tracking-wider block mb-2 text-surface-500 dark:text-surface-400">
                        Age
                    </span>
                    <span class="text-4xl font-black block mb-2 text-primary-600 dark:text-primary-400">
                        {{ age.years }} years
                    </span>
                    <span class="text-xl font-bold block mb-6 text-surface-700 dark:text-surface-300">
                        {{ age.months }} months, {{ age.days }} days
                    </span>
                    
                    <div class="grid grid-cols-3 gap-2 text-left mb-4">
                        <div class="p-3 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 text-center">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Months</span>
                            <span class="text-sm font-bold text-surface-900 dark:text-white">{{ formatNumber(age.totalMonths) }}</span>
                        </div>
                        <div class="p-3 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 text-center">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Weeks</span>
                            <span class="text-sm font-bold text-surface-900 dark:text-white">{{ formatNumber(age.totalWeeks) }}</span>
                        </div>
                        <div class="p-3 bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 text-center">
                            <span class="block text-xs text-surface-500 dark:text-surface-400 mb-1">Total Days</span>
                            <span class="text-sm font-bold text-surface-900 dark:text-white">{{ formatNumber(age.totalDays) }}</span>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-xl border border-primary-100 dark:border-primary-800/30 text-sm font-medium">
                        Your next birthday is in {{ nextBirthday.months }} months and {{ nextBirthday.days }} days.
                    </div>
                </div>
                <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center">
                    <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" /></svg>
                    <p>Enter your Date of Birth to calculate age.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const formatDateForInput = (date) => {
    return date.toISOString().split('T')[0];
};

const dob = ref('1990-01-01');
const targetDate = ref(formatDateForInput(new Date()));

const isValidInput = computed(() => {
    return dob.value && targetDate.value && new Date(dob.value) <= new Date(targetDate.value);
});

const calculateAge = (d1, d2) => {
    let years = d2.getFullYear() - d1.getFullYear();
    let months = d2.getMonth() - d1.getMonth();
    let days = d2.getDate() - d1.getDate();

    if (days < 0) {
        months--;
        let prevMonth = new Date(d2.getFullYear(), d2.getMonth(), 0).getDate();
        days += prevMonth;
    }
    
    if (months < 0) {
        years--;
        months += 12;
    }
    
    return { years, months, days };
};

const age = computed(() => {
    if (!isValidInput.value) return {};
    const d1 = new Date(dob.value);
    const d2 = new Date(targetDate.value);
    
    const { years, months, days } = calculateAge(d1, d2);
    
    const diffTime = Math.abs(d2 - d1);
    const totalDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    const totalWeeks = Math.floor(totalDays / 7);
    const totalMonths = (years * 12) + months;
    
    return { years, months, days, totalDays, totalWeeks, totalMonths };
});

const nextBirthday = computed(() => {
    if (!isValidInput.value) return {};
    const d1 = new Date(dob.value);
    const d2 = new Date(targetDate.value);
    
    let nextBday = new Date(d2.getFullYear(), d1.getMonth(), d1.getDate());
    
    if (nextBday < d2) {
        nextBday.setFullYear(d2.getFullYear() + 1);
    }
    
    return calculateAge(d2, nextBday);
});

const formatNumber = (val) => {
    return Number(val).toLocaleString('en-US');
};
</script>
