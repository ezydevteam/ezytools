<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Inputs -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Original Price</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">৳</span>
                        </div>
                        <input type="number" v-model="originalPrice" class="block w-full pl-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0.00" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Discount Amount</label>
                    <div class="flex">
                        <input type="number" v-model="discountValue" class="block w-full rounded-l-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0.00" min="0">
                        <select v-model="discountType" class="rounded-r-xl border-l-0 border-surface-300 dark:border-surface-600 bg-surface-100 dark:bg-surface-800 text-surface-700 dark:text-surface-300 focus:ring-primary-500 focus:border-primary-500 font-medium">
                            <option value="percent">% Off</option>
                            <option value="fixed">Fixed (৳)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Tax/VAT (Optional)</label>
                    <div class="relative">
                        <input type="number" v-model="taxRate" class="block w-full pr-8 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="0" min="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-surface-500 dark:text-surface-400 font-medium">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-center">
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-surface-600 dark:text-surface-400">
                        <span>Original Price:</span>
                        <span class="font-medium">৳ {{ formatCurrency(originalPrice || 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-green-600 dark:text-green-400 font-medium">
                        <span>Total Savings:</span>
                        <span>- ৳ {{ formatCurrency(savings) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-surface-600 dark:text-surface-400">
                        <span>Price after Discount:</span>
                        <span class="font-medium">৳ {{ formatCurrency(priceAfterDiscount) }}</span>
                    </div>
                    <div v-if="taxRate > 0" class="flex justify-between items-center text-surface-600 dark:text-surface-400">
                        <span>Tax/VAT ({{ taxRate }}%):</span>
                        <span class="font-medium">+ ৳ {{ formatCurrency(taxAmount) }}</span>
                    </div>
                    
                    <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-surface-900 dark:text-white">Final Price:</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">৳ {{ formatCurrency(finalPrice) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const originalPrice = ref(1000);
const discountValue = ref(10);
const discountType = ref('percent'); // 'percent' or 'fixed'
const taxRate = ref(0);

const savings = computed(() => {
    let p = Number(originalPrice.value) || 0;
    let d = Number(discountValue.value) || 0;
    
    if (discountType.value === 'percent') {
        return p * (d / 100);
    } else {
        return Math.min(p, d); // Can't save more than the item costs
    }
});

const priceAfterDiscount = computed(() => {
    return Math.max(0, (Number(originalPrice.value) || 0) - savings.value);
});

const taxAmount = computed(() => {
    let tr = Number(taxRate.value) || 0;
    return priceAfterDiscount.value * (tr / 100);
});

const finalPrice = computed(() => {
    return priceAfterDiscount.value + taxAmount.value;
});

const formatCurrency = (val) => {
    return Number(val).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
</script>
