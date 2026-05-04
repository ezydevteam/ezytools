<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Gross Monthly Salary</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="grossSalary" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="50000" min="0">
                    </div>
                </div>

                <!-- Deductions -->
                <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                    <h3 class="text-md font-bold text-surface-900 dark:text-white mb-4">Monthly Deductions</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Income Tax (TDS)</label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                                    </div>
                                    <input type="number" v-model="taxAmount" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0" min="0">
                                </div>
                                <span class="text-sm text-surface-500 dark:text-surface-400 w-16 text-center">OR</span>
                                <div class="relative flex-1">
                                    <input type="number" v-model="taxPercent" class="block w-full pr-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0" min="0" step="0.5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-surface-500 dark:text-surface-400 font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Provident Fund (PF)</label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                                    </div>
                                    <input type="number" v-model="pfAmount" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0" min="0">
                                </div>
                                <span class="text-sm text-surface-500 dark:text-surface-400 w-16 text-center">OR</span>
                                <div class="relative flex-1">
                                    <input type="number" v-model="pfPercent" class="block w-full pr-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0" min="0" step="0.5">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-surface-500 dark:text-surface-400 font-medium">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center">
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-surface-600 dark:text-surface-400">
                        <span>Gross Salary:</span>
                        <span class="font-medium">৳ {{ formatCurrency(grossSalary || 0) }}</span>
                    </div>
                    <div v-if="calculatedTax > 0" class="flex justify-between items-center text-red-600 dark:text-red-400">
                        <span>Income Tax:</span>
                        <span>- ৳ {{ formatCurrency(calculatedTax) }}</span>
                    </div>
                    <div v-if="calculatedPf > 0" class="flex justify-between items-center text-red-600 dark:text-red-400">
                        <span>Provident Fund:</span>
                        <span>- ৳ {{ formatCurrency(calculatedPf) }}</span>
                    </div>
                    
                    <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-surface-900 dark:text-white">Net Salary (In Hand):</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">৳ {{ formatCurrency(netSalary) }}</span>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-100 dark:border-primary-800/30">
                        <h4 class="text-sm font-bold text-primary-800 dark:text-primary-300 mb-3">Annual Summary</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-surface-600 dark:text-surface-400">
                                <span>Annual Gross:</span>
                                <span class="font-medium">৳ {{ formatCurrency((grossSalary || 0) * 12) }}</span>
                            </div>
                            <div class="flex justify-between text-surface-600 dark:text-surface-400">
                                <span>Total Deductions:</span>
                                <span class="font-medium text-red-600 dark:text-red-400">৳ {{ formatCurrency(totalDeductions * 12) }}</span>
                            </div>
                            <div class="flex justify-between font-bold text-surface-900 dark:text-white pt-2 border-t border-primary-200 dark:border-primary-800/50">
                                <span>Annual Net Income:</span>
                                <span>৳ {{ formatCurrency(netSalary * 12) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const grossSalary = ref(50000);

// Tax inputs
const taxAmount = ref(0);
const taxPercent = ref(0);

// PF inputs
const pfAmount = ref(0);
const pfPercent = ref(0);

// Two-way binding for Tax
watch(taxAmount, (val) => {
    if (document.activeElement.type === 'number' && grossSalary.value > 0) {
        taxPercent.value = Number(((val / grossSalary.value) * 100).toFixed(2));
    }
});
watch(taxPercent, (val) => {
    if (document.activeElement.type === 'number' && grossSalary.value > 0) {
        taxAmount.value = Number(((grossSalary.value * val) / 100).toFixed(0));
    }
});
watch(grossSalary, (val) => {
    if (val > 0) {
        taxAmount.value = Number(((val * taxPercent.value) / 100).toFixed(0));
        pfAmount.value = Number(((val * pfPercent.value) / 100).toFixed(0));
    }
});

// Two-way binding for PF
watch(pfAmount, (val) => {
    if (document.activeElement.type === 'number' && grossSalary.value > 0) {
        pfPercent.value = Number(((val / grossSalary.value) * 100).toFixed(2));
    }
});
watch(pfPercent, (val) => {
    if (document.activeElement.type === 'number' && grossSalary.value > 0) {
        pfAmount.value = Number(((grossSalary.value * val) / 100).toFixed(0));
    }
});

const calculatedTax = computed(() => Number(taxAmount.value) || 0);
const calculatedPf = computed(() => Number(pfAmount.value) || 0);

const totalDeductions = computed(() => calculatedTax.value + calculatedPf.value);

const netSalary = computed(() => {
    return Math.max(0, (Number(grossSalary.value) || 0) - totalDeductions.value);
});

const formatCurrency = (val) => {
    return Number(val).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
};
</script>
