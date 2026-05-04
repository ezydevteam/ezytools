<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="print:hidden mb-8 flex justify-between items-center bg-surface-50 dark:bg-surface-900 p-4 rounded-xl border border-surface-200 dark:border-surface-700">
            <h3 class="font-bold text-surface-900 dark:text-white">Cash Receipt Generator</h3>
            <div class="flex gap-2">
                <button @click="printReceipt" class="px-4 py-1.5 text-sm bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print / Save PDF
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 print:block">
            <!-- Form Area -->
            <div class="space-y-6 print:hidden">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Receipt Number</label>
                        <input type="text" v-model="receipt.number" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Date</label>
                        <input type="date" v-model="receipt.date" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-surface-500 mb-1">Received From</label>
                    <input type="text" v-model="receipt.receivedFrom" placeholder="Name or Company" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Amount</label>
                        <input type="number" v-model="receipt.amount" placeholder="0.00" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Currency</label>
                        <input type="text" v-model="receipt.currency" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-surface-500 mb-1">For Payment Of</label>
                    <input type="text" v-model="receipt.paymentFor" placeholder="e.g. Rent, Services rendered, etc." class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-surface-500 mb-1">Payment Method</label>
                    <select v-model="receipt.method" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                        <option value="Cash">Cash</option>
                        <option value="Check">Check</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Mobile Banking">Mobile Banking (bKash, Nagad, etc.)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-surface-500 mb-1">Receiver Name / Organization</label>
                    <input type="text" v-model="receipt.receiver" placeholder="Your Name or Company" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <!-- Preview/Print Area -->
            <div id="receipt-preview" class="bg-white border border-surface-200 shadow-sm rounded-xl p-8 text-surface-900 max-w-[600px] mx-auto w-full print:border-none print:shadow-none print:p-0 print:w-full relative overflow-hidden">
                
                <!-- Watermark / Background Styling -->
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary-50 rounded-full opacity-50 pointer-events-none"></div>
                <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-surface-50 rounded-full opacity-50 pointer-events-none"></div>

                <div class="relative z-10 border-4 border-double border-surface-200 p-8">
                    <!-- Header -->
                    <div class="flex justify-between items-end mb-8 border-b-2 border-surface-900 pb-4">
                        <div>
                            <h1 class="text-3xl font-black text-surface-900 tracking-tight uppercase">Cash Receipt</h1>
                        </div>
                        <div class="text-right">
                            <p class="text-surface-900 font-bold text-lg">No. {{ receipt.number || '001' }}</p>
                            <p class="text-surface-600 text-sm">Date: {{ formattedDate }}</p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="space-y-6 text-base leading-relaxed">
                        <div class="flex items-end gap-2">
                            <span class="font-bold whitespace-nowrap min-w-[120px]">Received From:</span>
                            <div class="flex-1 border-b border-surface-400 pb-1 px-2 italic">{{ receipt.receivedFrom || '_________________________' }}</div>
                        </div>

                        <div class="flex items-end gap-2">
                            <span class="font-bold whitespace-nowrap min-w-[120px]">The Sum of:</span>
                            <div class="flex-1 border-b border-surface-400 pb-1 px-2 font-bold text-xl">{{ receipt.currency }}{{ formattedAmount }}</div>
                        </div>

                        <div class="flex items-end gap-2">
                            <span class="font-bold whitespace-nowrap min-w-[120px]">For Payment Of:</span>
                            <div class="flex-1 border-b border-surface-400 pb-1 px-2">{{ receipt.paymentFor || '_________________________' }}</div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-between items-end mt-16 pt-4">
                        <div class="bg-surface-100 px-4 py-2 rounded-lg border border-surface-200">
                            <span class="text-xs font-bold text-surface-500 block uppercase">Payment Method</span>
                            <span class="font-medium">{{ receipt.method }}</span>
                        </div>
                        <div class="text-center w-64">
                            <div class="border-b border-surface-400 pb-8 mb-2"></div>
                            <p class="font-bold text-sm">{{ receipt.receiver || 'Authorized Signature' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const today = new Date().toISOString().split('T')[0];

const receipt = ref({
    number: 'REC-1001',
    date: today,
    receivedFrom: '',
    amount: '',
    currency: '$',
    paymentFor: '',
    method: 'Cash',
    receiver: ''
});

const formattedDate = computed(() => {
    if (!receipt.value.date) return '___/___/_____';
    return new Date(receipt.value.date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
});

const formattedAmount = computed(() => {
    const amt = parseFloat(receipt.value.amount);
    if (isNaN(amt)) return '0.00';
    return amt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});

const printReceipt = () => {
    window.print();
};
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #receipt-preview, #receipt-preview * {
        visibility: visible;
    }
    #receipt-preview {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 20px;
        box-shadow: none;
    }
}
</style>
