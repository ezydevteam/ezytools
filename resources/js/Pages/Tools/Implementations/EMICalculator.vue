<template>
    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Input Form -->
        <div class="lg:col-span-5 space-y-6 bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <h3 class="font-semibold text-lg text-surface-900 dark:text-white border-b border-surface-200 dark:border-surface-700 pb-3">Loan Details</h3>
            
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Loan Amount (৳)</label>
                <input type="number" v-model.number="loanAmount" min="0" step="1000" class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-lg dark:bg-surface-900 dark:border-surface-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Interest Rate (% P.A.)</label>
                <input type="number" v-model.number="interestRate" min="0" step="0.1" class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-lg dark:bg-surface-900 dark:border-surface-700 dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Loan Tenure</label>
                <div class="flex gap-2">
                    <input type="number" v-model.number="tenure" min="1" class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-lg dark:bg-surface-900 dark:border-surface-700 dark:text-white">
                    <select v-model="tenureType" class="w-1/3 rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 dark:text-white">
                        <option value="years">Years</option>
                        <option value="months">Months</option>
                    </select>
                </div>
            </div>

            <div class="pt-4">
                <button @click="reset" class="w-full text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 text-sm font-medium">Reset Values</button>
            </div>
        </div>

        <!-- Results Display -->
        <div class="lg:col-span-7 flex flex-col gap-6">
            
            <div class="bg-gradient-to-br from-primary-600 to-purple-700 p-8 rounded-xl shadow-lg text-white text-center">
                <h3 class="text-primary-100 font-medium mb-1 uppercase tracking-wide text-sm">Monthly EMI</h3>
                <div class="text-4xl md:text-5xl font-bold">
                    ৳ {{ formatMoney(results.emi) }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm text-center">
                    <h4 class="text-sm font-medium text-surface-500 uppercase tracking-wide mb-2">Principal Amount</h4>
                    <div class="text-2xl font-bold text-surface-900 dark:text-white">৳ {{ formatMoney(loanAmount) }}</div>
                </div>
                
                <div class="bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm text-center">
                    <h4 class="text-sm font-medium text-surface-500 uppercase tracking-wide mb-2">Total Interest</h4>
                    <div class="text-2xl font-bold text-orange-500">৳ {{ formatMoney(results.totalInterest) }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm flex justify-between items-center">
                <span class="text-lg font-semibold text-surface-700 dark:text-surface-300">Total Payment (Prin + Int)</span>
                <span class="text-2xl font-extrabold text-green-600 dark:text-green-500">৳ {{ formatMoney(results.totalPayment) }}</span>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const loanAmount = ref(1000000);
const interestRate = ref(9);
const tenure = ref(5);
const tenureType = ref('years');

const results = computed(() => {
    const principal = Number(loanAmount.value) || 0;
    const rate = Number(interestRate.value) || 0;
    const time = Number(tenure.value) || 0;

    if (principal === 0 || rate === 0 || time === 0) {
        return { emi: 0, totalInterest: 0, totalPayment: principal };
    }

    const months = tenureType.value === 'years' ? time * 12 : time;
    const monthlyRate = rate / (12 * 100);
    
    // EMI Formula: P x R x (1+R)^N / [(1+R)^N-1]
    const emi = (principal * monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1);
    
    const totalPayment = emi * months;
    const totalInterest = totalPayment - principal;

    return {
        emi: Math.round(emi),
        totalInterest: Math.round(totalInterest),
        totalPayment: Math.round(totalPayment)
    };
});

const reset = () => {
    loanAmount.value = 1000000;
    interestRate.value = 9;
    tenure.value = 5;
    tenureType.value = 'years';
};

const formatMoney = (value) => {
    return value.toLocaleString('en-IN'); // Using Indian formatting which matches BD style (lakhs/crores)
};
</script>
