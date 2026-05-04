<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Initial Deposit (Principal)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="principal" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="10000" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Regular Monthly Addition (Optional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="monthlyAddition" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Annual Interest Rate</label>
                    <div class="relative">
                        <input type="number" v-model="rate" class="block w-full pr-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="5" min="0" step="0.1">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">%</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Time (Years)</label>
                        <input type="number" v-model="time" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="10" min="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Compound Frequency</label>
                        <select v-model="compoundFrequency" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option :value="1">Annually</option>
                            <option :value="2">Semi-Annually</option>
                            <option :value="4">Quarterly</option>
                            <option :value="12">Monthly</option>
                            <option :value="365">Daily</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center">
                <div class="space-y-6">
                    <div>
                        <span class="text-sm text-surface-500 dark:text-surface-400 block mb-1">Total Interest Earned</span>
                        <span class="text-3xl font-bold text-green-600 dark:text-green-400">+ ৳ {{ formatCurrency(totalInterest) }}</span>
                    </div>
                    
                    <div class="pt-4 border-t border-surface-200 dark:border-surface-700 space-y-2">
                        <div class="flex justify-between items-center text-surface-600 dark:text-surface-400">
                            <span>Initial Principal:</span>
                            <span class="font-medium">৳ {{ formatCurrency(principal) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-surface-600 dark:text-surface-400">
                            <span>Total Deposits (Additions):</span>
                            <span class="font-medium">+ ৳ {{ formatCurrency(totalDeposits) }}</span>
                        </div>
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                            <span class="text-lg font-bold text-surface-900 dark:text-white">Future Value:</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">৳ {{ formatCurrency(futureValue) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const principal = ref(10000);
const monthlyAddition = ref(0);
const rate = ref(5);
const time = ref(10);
const compoundFrequency = ref(12);

const futureValue = computed(() => {
    let p = Number(principal.value) || 0;
    let pmt = Number(monthlyAddition.value) || 0;
    let r = (Number(rate.value) || 0) / 100;
    let t = Number(time.value) || 0;
    let n = Number(compoundFrequency.value) || 1;
    
    // Compound interest for principal
    // A = P(1 + r/n)^(nt)
    let amountPrincipal = p * Math.pow(1 + (r / n), n * t);
    
    // Future value of a series (if there are monthly additions)
    // We assume additions are made at the end of each period, and compound frequency is aligned with deposit frequency (monthly)
    // To be perfectly accurate for different compound frequencies with monthly deposits requires a more complex formula,
    // but for standard personal finance estimations, if they add monthly, we calculate future value of those monthly deposits:
    let amountAdditions = 0;
    if (pmt > 0) {
        // Standard FV of Annuity formula, assuming monthly compounding for the additions part to match monthly deposits
        let monthlyRate = r / 12;
        let totalMonths = t * 12;
        if (monthlyRate > 0) {
            amountAdditions = pmt * ((Math.pow(1 + monthlyRate, totalMonths) - 1) / monthlyRate);
        } else {
            amountAdditions = pmt * totalMonths;
        }
    }
    
    return amountPrincipal + amountAdditions;
});

const totalDeposits = computed(() => {
    let pmt = Number(monthlyAddition.value) || 0;
    let t = Number(time.value) || 0;
    return pmt * (t * 12);
});

const totalInterest = computed(() => {
    let p = Number(principal.value) || 0;
    return futureValue.value - p - totalDeposits.value;
});

const formatCurrency = (val) => {
    return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>
