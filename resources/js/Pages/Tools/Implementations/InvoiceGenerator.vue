<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="print:hidden mb-8 flex justify-between items-center bg-surface-50 dark:bg-surface-900 p-4 rounded-xl border border-surface-200 dark:border-surface-700">
            <h3 class="font-bold text-surface-900 dark:text-white">Invoice Generator</h3>
            <div class="flex gap-2">
                <button @click="loadSampleData" class="px-3 py-1.5 text-sm bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 rounded-lg transition-colors">Load Sample</button>
                <button @click="printInvoice" class="px-4 py-1.5 text-sm bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print / Save PDF
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 print:block">
            <!-- Form Area (Hidden when printing) -->
            <div class="space-y-6 print:hidden">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Your Company Name</label>
                        <input type="text" v-model="invoice.companyName" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Your Email</label>
                        <input type="text" v-model="invoice.companyEmail" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 border-t border-surface-200 dark:border-surface-700 pt-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Client Name</label>
                        <input type="text" v-model="invoice.clientName" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Client Email / Address</label>
                        <input type="text" v-model="invoice.clientAddress" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 border-t border-surface-200 dark:border-surface-700 pt-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Invoice #</label>
                        <input type="text" v-model="invoice.number" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Date</label>
                        <input type="date" v-model="invoice.date" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Currency Symbol</label>
                        <input type="text" v-model="invoice.currency" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="border-t border-surface-200 dark:border-surface-700 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-medium text-surface-500">Items</label>
                        <button @click="addItem" class="text-xs text-primary-600 font-medium hover:text-primary-700">+ Add Item</button>
                    </div>
                    <div class="space-y-2 max-h-[250px] overflow-y-auto pr-2">
                        <div v-for="(item, index) in invoice.items" :key="index" class="flex gap-2 items-center bg-surface-50 dark:bg-surface-900 p-2 rounded-lg border border-surface-200 dark:border-surface-700">
                            <input type="text" v-model="item.description" placeholder="Description" class="flex-1 min-w-0 text-sm rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800">
                            <input type="number" v-model="item.qty" placeholder="Qty" class="w-16 text-sm rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800">
                            <input type="number" v-model="item.price" placeholder="Price" class="w-24 text-sm rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800">
                            <button @click="removeItem(index)" class="p-1 text-surface-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-surface-200 dark:border-surface-700 pt-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Tax (%)</label>
                        <input type="number" v-model="invoice.taxRate" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Notes</label>
                        <textarea v-model="invoice.notes" rows="1" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500 resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Preview/Print Area -->
            <div id="invoice-preview" class="bg-white border border-surface-200 shadow-sm rounded-xl p-8 text-surface-900 max-w-[800px] mx-auto w-full print:border-none print:shadow-none print:p-0 print:w-full">
                
                <!-- Header -->
                <div class="flex justify-between items-start mb-12">
                    <div>
                        <h1 class="text-3xl font-black text-surface-900 tracking-tight uppercase mb-2">INVOICE</h1>
                        <p class="text-surface-500 text-sm">#{{ invoice.number || 'INV-0001' }}</p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-surface-900">{{ invoice.companyName || 'Your Company Ltd.' }}</h2>
                        <p class="text-surface-500 text-sm mt-1">{{ invoice.companyEmail || 'hello@yourcompany.com' }}</p>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex justify-between mb-12 border-b border-surface-200 pb-8">
                    <div>
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider mb-2">Billed To</p>
                        <p class="text-surface-900 font-bold">{{ invoice.clientName || 'Client Name' }}</p>
                        <p class="text-surface-600 text-sm mt-1">{{ invoice.clientAddress || 'Client Address or Email' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-surface-400 uppercase tracking-wider mb-2">Date</p>
                        <p class="text-surface-900 font-medium">{{ formattedDate }}</p>
                    </div>
                </div>

                <!-- Items -->
                <table class="w-full mb-8 text-sm">
                    <thead>
                        <tr class="border-b border-surface-900 text-left">
                            <th class="py-3 font-bold text-surface-900 w-full">Description</th>
                            <th class="py-3 font-bold text-surface-900 text-center px-4">Qty</th>
                            <th class="py-3 font-bold text-surface-900 text-right px-4">Price</th>
                            <th class="py-3 font-bold text-surface-900 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in invoice.items" :key="index" class="border-b border-surface-100">
                            <td class="py-4 text-surface-800">{{ item.description || 'Item description' }}</td>
                            <td class="py-4 text-center text-surface-600">{{ item.qty || 0 }}</td>
                            <td class="py-4 text-right text-surface-600">{{ invoice.currency }}{{ (item.price || 0).toFixed(2) }}</td>
                            <td class="py-4 text-right text-surface-900 font-medium">{{ invoice.currency }}{{ itemTotal(item).toFixed(2) }}</td>
                        </tr>
                        <tr v-if="invoice.items.length === 0">
                            <td colspan="4" class="py-8 text-center text-surface-400 italic">No items added yet.</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="flex justify-end mb-12">
                    <div class="w-1/2">
                        <div class="flex justify-between py-2 text-sm text-surface-600 border-b border-surface-100">
                            <span>Subtotal</span>
                            <span>{{ invoice.currency }}{{ subtotal.toFixed(2) }}</span>
                        </div>
                        <div v-if="invoice.taxRate > 0" class="flex justify-between py-2 text-sm text-surface-600 border-b border-surface-100">
                            <span>Tax ({{ invoice.taxRate }}%)</span>
                            <span>{{ invoice.currency }}{{ taxAmount.toFixed(2) }}</span>
                        </div>
                        <div class="flex justify-between py-4 text-lg font-bold text-surface-900 border-b-2 border-surface-900">
                            <span>Total Due</span>
                            <span>{{ invoice.currency }}{{ grandTotal.toFixed(2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center text-sm text-surface-500 border-t border-surface-200 pt-8 mt-16">
                    <p v-if="invoice.notes">{{ invoice.notes }}</p>
                    <p v-else>Thank you for your business!</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const today = new Date().toISOString().split('T')[0];

const invoice = ref({
    companyName: '',
    companyEmail: '',
    clientName: '',
    clientAddress: '',
    number: 'INV-' + Math.floor(1000 + Math.random() * 9000),
    date: today,
    currency: '$',
    taxRate: 0,
    notes: 'Thank you for your business. Payment is due within 30 days.',
    items: [
        { description: '', qty: 1, price: 0 }
    ]
});

const formattedDate = computed(() => {
    if (!invoice.value.date) return '';
    return new Date(invoice.value.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
});

const addItem = () => {
    invoice.value.items.push({ description: '', qty: 1, price: 0 });
};

const removeItem = (index) => {
    invoice.value.items.splice(index, 1);
};

const itemTotal = (item) => {
    return (item.qty || 0) * (item.price || 0);
};

const subtotal = computed(() => {
    return invoice.value.items.reduce((sum, item) => sum + itemTotal(item), 0);
});

const taxAmount = computed(() => {
    return subtotal.value * ((invoice.value.taxRate || 0) / 100);
});

const grandTotal = computed(() => {
    return subtotal.value + taxAmount.value;
});

const loadSampleData = () => {
    invoice.value = {
        companyName: 'PixelCraft Digital',
        companyEmail: 'billing@pixelcraft.dev',
        clientName: 'Acme Corp.',
        clientAddress: '123 Business Avenue, NY 10001',
        number: 'INV-2023-089',
        date: today,
        currency: '$',
        taxRate: 10,
        notes: 'Please make checks payable to PixelCraft Digital. Thank you for your business!',
        items: [
            { description: 'Website Redesign', qty: 1, price: 1500 },
            { description: 'Logo Creation', qty: 1, price: 500 },
            { description: 'Web Hosting (1 Year)', qty: 1, price: 120 }
        ]
    };
};

const printInvoice = () => {
    window.print();
};
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoice-preview, #invoice-preview * {
        visibility: visible;
    }
    #invoice-preview {
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
