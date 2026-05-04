<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="max-w-xl mx-auto">
            <!-- Input -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 text-center">Select any Date in History or Future</label>
                <input type="date" v-model="targetDate" class="block w-full max-w-sm mx-auto text-center text-lg py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
            </div>

            <!-- Result -->
            <div v-if="isValidInput" class="text-center">
                <div class="p-8 bg-surface-50 dark:bg-surface-900 rounded-2xl border border-surface-200 dark:border-surface-700 transform transition-all">
                    <span class="text-sm font-bold uppercase tracking-wider block mb-2 text-surface-500 dark:text-surface-400">
                        That day is a
                    </span>
                    <span class="text-5xl font-black block mb-4 text-primary-600 dark:text-primary-400">
                        {{ dayOfWeek }}
                    </span>
                    
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full mb-6" :class="isWeekend ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'">
                        <span class="text-sm font-bold">{{ isWeekend ? '🎉 It\'s a Weekend!' : '💼 It\'s a Weekday' }}</span>
                    </div>

                    <div class="text-sm text-surface-600 dark:text-surface-400">
                        <p><strong>Formatted:</strong> {{ formattedDate }}</p>
                        <p class="mt-1"><strong>Relative:</strong> {{ relativeTime }}</p>
                    </div>
                </div>
            </div>
            
            <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center p-8">
                <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <p>Please select a valid date.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const today = new Date();
const targetDate = ref(today.toISOString().split('T')[0]);

const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

const isValidInput = computed(() => {
    return targetDate.value !== '';
});

const dateObj = computed(() => {
    if (!isValidInput.value) return null;
    return new Date(targetDate.value);
});

const dayOfWeek = computed(() => {
    if (!dateObj.value) return '';
    return days[dateObj.value.getDay()];
});

// Assuming standard global weekend (Sat/Sun) for simplicity, or we can make it Friday/Saturday for BD context.
const isWeekend = computed(() => {
    if (!dateObj.value) return false;
    const day = dateObj.value.getDay();
    // 5 = Friday, 6 = Saturday
    return day === 5 || day === 6; 
});

const formattedDate = computed(() => {
    if (!dateObj.value) return '';
    return dateObj.value.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
});

const relativeTime = computed(() => {
    if (!dateObj.value) return '';
    
    const now = new Date();
    now.setHours(0, 0, 0, 0);
    
    const target = new Date(dateObj.value);
    target.setHours(0, 0, 0, 0);
    
    const diffTime = target - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Tomorrow';
    if (diffDays === -1) return 'Yesterday';
    
    if (diffDays > 0) {
        if (diffDays < 30) return `In ${diffDays} days`;
        if (diffDays < 365) return `In ${Math.floor(diffDays / 30)} months`;
        return `In ${Math.floor(diffDays / 365)} years`;
    } else {
        const absDays = Math.abs(diffDays);
        if (absDays < 30) return `${absDays} days ago`;
        if (absDays < 365) return `${Math.floor(absDays / 30)} months ago`;
        return `${Math.floor(absDays / 365)} years ago`;
    }
});
</script>
