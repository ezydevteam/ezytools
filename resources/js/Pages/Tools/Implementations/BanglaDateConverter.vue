<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">English Date (Gregorian)</label>
                    <input type="date" v-model="englishDate" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>
                
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30 text-sm text-blue-800 dark:text-blue-300">
                    <p><strong>Note:</strong> This uses the revised official calendar system of Bangladesh (modified in 2019).</p>
                    <ul class="list-disc ml-5 mt-2 space-y-1">
                        <li>Baishakh to Ashwin (1st 6 months): 31 Days</li>
                        <li>Kartik to Falgun (Next 5 months): 30 Days</li>
                        <li>Chaitra (Last month): 30 Days (31 Days in Leap Year)</li>
                    </ul>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center text-center">
                <div v-if="englishDate">
                    <span class="text-sm font-bold uppercase tracking-wider block mb-2 text-surface-500 dark:text-surface-400">
                        Bangla Date (বঙ্গাব্দ)
                    </span>
                    <span class="text-4xl font-black block mb-4 text-primary-600 dark:text-primary-400">
                        {{ banglaDate.day }} {{ banglaDate.monthName }} {{ banglaDate.year }}
                    </span>
                    <span class="text-xl font-medium block mb-6 text-surface-700 dark:text-surface-300">
                        {{ banglaDate.season }} কাল
                    </span>
                    
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 text-sm font-bold border border-primary-100 dark:border-primary-800/50">
                        {{ englishToBanglaNumber(banglaDate.day) }} {{ banglaDate.monthName }}, {{ englishToBanglaNumber(banglaDate.year) }}
                    </div>
                </div>
                <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center">
                    <svg class="w-12 h-12 mb-3 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p>Select an English date to convert.</p>
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

const englishDate = ref(formatDateForInput(new Date()));

const banglaMonths = [
    { name: 'বৈশাখ (Baishakh)', days: 31, season: 'গ্রীষ্ম (Summer)' },
    { name: 'জ্যৈষ্ঠ (Joishtho)', days: 31, season: 'গ্রীষ্ম (Summer)' },
    { name: 'আষাঢ় (Ashar)', days: 31, season: 'বর্ষা (Monsoon)' },
    { name: 'শ্রাবণ (Srabon)', days: 31, season: 'বর্ষা (Monsoon)' },
    { name: 'ভাদ্র (Bhadro)', days: 31, season: 'শরৎ (Autumn)' },
    { name: 'আশ্বিন (Ashwin)', days: 31, season: 'শরৎ (Autumn)' },
    { name: 'কার্তিক (Kartik)', days: 30, season: 'হেমন্ত (Late Autumn)' },
    { name: 'অগ্রহায়ণ (Agrahayan)', days: 30, season: 'হেমন্ত (Late Autumn)' },
    { name: 'পৌষ (Poush)', days: 30, season: 'শীত (Winter)' },
    { name: 'মাঘ (Magh)', days: 30, season: 'শীত (Winter)' },
    { name: 'ফাল্গুন (Falgun)', days: 30, season: 'বসন্ত (Spring)' }, // 30 normally
    { name: 'চৈত্র (Chaitra)', days: 30, season: 'বসন্ত (Spring)' } // 30 normally, 31 in leap year
];

const isLeapYear = (year) => {
    return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
};

const englishToBanglaNumber = (numStr) => {
    const banglaDigits = { '0':'০', '1':'১', '2':'২', '3':'৩', '4':'৪', '5':'৫', '6':'৬', '7':'৭', '8':'৮', '9':'৯' };
    return String(numStr).replace(/[0-9]/g, w => banglaDigits[w]);
};

// Bangladesh revised calendar logic (implemented since 2019/1426 BS)
// Baishakh 1 is ALWAYS April 14.
const getBanglaDate = (dateString) => {
    if (!dateString) return null;
    
    const d = new Date(dateString);
    const year = d.getFullYear();
    const month = d.getMonth() + 1; // 1-12
    const day = d.getDate();
    
    // Check if the Gregorian year is leap year. If yes, Chaitra of previous BS year has 31 days.
    // However, Chaitra starts around mid-March and ends mid-April. 
    // Wait, the leap year rule: if Gregorian year is leap year, Falgun (which ends mid-March) gets 31 days? 
    // Actually, in the new BD rule (2019), Falgun is 30 days. Chaitra is 30 days. 
    // BUT in a Gregorian leap year, Falgun is 31 days. Let's use the standard new rule:
    // Baishakh-Ashwin = 31. Kartik-Magh = 30. Falgun = 29/30? 
    // Wait, the new rule: 1st 6 months = 31 days. Next 5 months = 30 days. Falgun (11th month) = 29 days? No, Falgun is 30 days in new rule, and 31 in leap year?
    // Let's stick to the simplest BD official rule: 
    // Baishakh (1) to Ashwin (6) = 31 days.
    // Kartik (7) to Magh (10) = 30 days.
    // Falgun (11) = 30 days. (31 days in Gregorian leap year).
    // Chaitra (12) = 30 days.
    
    // Find the difference in days from April 14 of the given year.
    let baseDate = new Date(year, 3, 14); // April 14
    let bsYear = year - 593;
    
    if (d < baseDate) {
        bsYear -= 1;
        baseDate = new Date(year - 1, 3, 14); // April 14 of prev year
    }
    
    // Calculate days passed since Baishakh 1 (baseDate)
    const diffTime = d - baseDate;
    let daysPassed = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    let bsMonthIndex = 0;
    
    // Determine month lengths for this BS year
    // Falgun falls in Feb/March of the NEXT Gregorian year. 
    // So if bsYear + 594 is a leap year, Falgun has 31 days.
    const isGregorianLeapNextYear = isLeapYear(bsYear + 594);
    
    const monthLengths = [
        31, 31, 31, 31, 31, 31, // 1-6
        30, 30, 30, 30, // 7-10
        isGregorianLeapNextYear ? 31 : 30, // 11 Falgun
        30 // 12 Chaitra
    ];
    
    while (daysPassed >= monthLengths[bsMonthIndex]) {
        daysPassed -= monthLengths[bsMonthIndex];
        bsMonthIndex++;
    }
    
    return {
        day: daysPassed + 1,
        monthName: banglaMonths[bsMonthIndex].name,
        season: banglaMonths[bsMonthIndex].season,
        year: bsYear
    };
};

const banglaDate = computed(() => getBanglaDate(englishDate.value));
</script>
