<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="print:hidden mb-8 flex justify-between items-center bg-surface-50 dark:bg-surface-900 p-4 rounded-xl border border-surface-200 dark:border-surface-700">
            <h3 class="font-bold text-surface-900 dark:text-white">Salary Slip Generator</h3>
            <div class="flex gap-2">
                <button @click="loadSampleData" class="px-3 py-1.5 text-sm bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 rounded-lg transition-colors">Load Sample</button>
                <button @click="printSlip" class="px-4 py-1.5 text-sm bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print / Save PDF
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 print:block">
            <!-- Form Area (Hidden when printing) -->
            <div class="lg:col-span-5 space-y-6 print:hidden max-h-[800px] overflow-y-auto pr-2">
                
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-surface-900 dark:text-white border-b border-surface-200 dark:border-surface-700 pb-2">Company Information</h4>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Company Name</label>
                        <input type="text" v-model="slip.companyName" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Company Address</label>
                        <input type="text" v-model="slip.companyAddress" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-surface-900 dark:text-white border-b border-surface-200 dark:border-surface-700 pb-2">Employee Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Employee Name</label>
                            <input type="text" v-model="slip.employeeName" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Employee ID</label>
                            <input type="text" v-model="slip.employeeId" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Designation</label>
                            <input type="text" v-model="slip.designation" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Department</label>
                            <input type="text" v-model="slip.department" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Salary Month</label>
                            <input type="month" v-model="slip.month" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-500 mb-1">Currency</label>
                            <input type="text" v-model="slip.currency" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-end border-b border-surface-200 dark:border-surface-700 pb-2">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white">Earnings</h4>
                        <button @click="addEarning" class="text-xs text-primary-600 font-medium">+ Add Earning</button>
                    </div>
                    <div v-for="(earning, index) in slip.earnings" :key="'e'+index" class="flex gap-2">
                        <input type="text" v-model="earning.name" placeholder="Earning Name" class="flex-1 text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900">
                        <input type="number" v-model="earning.amount" placeholder="Amount" class="w-32 text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900">
                        <button @click="slip.earnings.splice(index, 1)" class="p-2 text-surface-400 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-end border-b border-surface-200 dark:border-surface-700 pb-2">
                        <h4 class="text-sm font-bold text-surface-900 dark:text-white">Deductions</h4>
                        <button @click="addDeduction" class="text-xs text-red-600 font-medium">+ Add Deduction</button>
                    </div>
                    <div v-for="(deduction, index) in slip.deductions" :key="'d'+index" class="flex gap-2">
                        <input type="text" v-model="deduction.name" placeholder="Deduction Name" class="flex-1 text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900">
                        <input type="number" v-model="deduction.amount" placeholder="Amount" class="w-32 text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900">
                        <button @click="slip.deductions.splice(index, 1)" class="p-2 text-surface-400 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Preview/Print Area -->
            <div class="lg:col-span-7">
                <div id="salary-slip-preview" class="bg-white border border-surface-200 shadow-sm p-8 text-surface-900 w-full mx-auto print:border-none print:shadow-none print:p-0 print:w-full">
                    
                    <!-- Header -->
                    <div class="text-center mb-8 border-b-2 border-surface-900 pb-6">
                        <h1 class="text-2xl font-black text-surface-900 uppercase tracking-wide">{{ slip.companyName || 'Company Name' }}</h1>
                        <p class="text-surface-600 text-sm mt-1">{{ slip.companyAddress || 'Company Address line' }}</p>
                        <h2 class="text-lg font-bold text-surface-900 mt-4 bg-surface-100 py-1 inline-block px-6 border border-surface-300">SALARY SLIP FOR {{ formattedMonth }}</h2>
                    </div>

                    <!-- Employee Details -->
                    <table class="w-full mb-8 text-sm border border-surface-300">
                        <tbody>
                            <tr class="border-b border-surface-300">
                                <td class="py-2 px-4 font-bold bg-surface-50 border-r border-surface-300 w-1/4">Employee Name</td>
                                <td class="py-2 px-4 border-r border-surface-300 w-1/4">{{ slip.employeeName || '-' }}</td>
                                <td class="py-2 px-4 font-bold bg-surface-50 border-r border-surface-300 w-1/4">Employee ID</td>
                                <td class="py-2 px-4 w-1/4">{{ slip.employeeId || '-' }}</td>
                            </tr>
                            <tr class="border-b border-surface-300">
                                <td class="py-2 px-4 font-bold bg-surface-50 border-r border-surface-300">Designation</td>
                                <td class="py-2 px-4 border-r border-surface-300">{{ slip.designation || '-' }}</td>
                                <td class="py-2 px-4 font-bold bg-surface-50 border-r border-surface-300">Department</td>
                                <td class="py-2 px-4">{{ slip.department || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Salary Details -->
                    <div class="flex border border-surface-300 mb-8 text-sm">
                        
                        <!-- Earnings Column -->
                        <div class="w-1/2 border-r border-surface-300">
                            <div class="bg-surface-100 p-2 font-bold text-center border-b border-surface-300 uppercase">Earnings</div>
                            <div class="p-4 space-y-2 min-h-[200px]">
                                <div v-for="(earning, index) in slip.earnings" :key="index" class="flex justify-between">
                                    <span>{{ earning.name || 'Earning' }}</span>
                                    <span>{{ formatMoney(earning.amount) }}</span>
                                </div>
                            </div>
                            <div class="bg-surface-50 p-2 font-bold flex justify-between border-t border-surface-300">
                                <span>Total Earnings</span>
                                <span>{{ formatMoney(totalEarnings) }}</span>
                            </div>
                        </div>

                        <!-- Deductions Column -->
                        <div class="w-1/2">
                            <div class="bg-surface-100 p-2 font-bold text-center border-b border-surface-300 uppercase">Deductions</div>
                            <div class="p-4 space-y-2 min-h-[200px]">
                                <div v-for="(deduction, index) in slip.deductions" :key="index" class="flex justify-between">
                                    <span>{{ deduction.name || 'Deduction' }}</span>
                                    <span>{{ formatMoney(deduction.amount) }}</span>
                                </div>
                            </div>
                            <div class="bg-surface-50 p-2 font-bold flex justify-between border-t border-surface-300">
                                <span>Total Deductions</span>
                                <span>{{ formatMoney(totalDeductions) }}</span>
                            </div>
                        </div>

                    </div>

                    <!-- Net Salary -->
                    <div class="bg-surface-100 border border-surface-300 p-4 flex justify-between items-center mb-16">
                        <span class="text-lg font-bold text-surface-900 uppercase">Net Salary</span>
                        <span class="text-2xl font-black text-surface-900">{{ formatMoney(netSalary) }}</span>
                    </div>

                    <!-- Signatures -->
                    <div class="flex justify-between items-end px-8 mt-12 text-sm font-bold text-surface-800">
                        <div class="text-center w-48 border-t border-surface-400 pt-2">
                            Employer Signature
                        </div>
                        <div class="text-center w-48 border-t border-surface-400 pt-2">
                            Employee Signature
                        </div>
                    </div>
                    
                    <p class="text-center text-xs text-surface-500 mt-12 italic">This is a computer-generated document and requires no stamp.</p>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const d = new Date();
const currentMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;

const slip = ref({
    companyName: '',
    companyAddress: '',
    employeeName: '',
    employeeId: '',
    designation: '',
    department: '',
    month: currentMonth,
    currency: '$',
    earnings: [
        { name: 'Basic Pay', amount: 0 },
        { name: 'House Rent Allowance', amount: 0 }
    ],
    deductions: [
        { name: 'Provident Fund', amount: 0 },
        { name: 'Tax Deducted at Source', amount: 0 }
    ]
});

const formattedMonth = computed(() => {
    if (!slip.value.month) return '';
    const [year, month] = slip.value.month.split('-');
    const date = new Date(year, month - 1);
    return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' }).toUpperCase();
});

const formatMoney = (val) => {
    const num = parseFloat(val);
    if (isNaN(num)) return slip.value.currency + '0.00';
    return slip.value.currency + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const totalEarnings = computed(() => {
    return slip.value.earnings.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
});

const totalDeductions = computed(() => {
    return slip.value.deductions.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
});

const netSalary = computed(() => {
    return totalEarnings.value - totalDeductions.value;
});

const addEarning = () => slip.value.earnings.push({ name: '', amount: 0 });
const addDeduction = () => slip.value.deductions.push({ name: '', amount: 0 });

const loadSampleData = () => {
    slip.value = {
        companyName: 'TechNova Solutions Ltd.',
        companyAddress: 'Level 4, IT Park, Block B',
        employeeName: 'Sarah Jenkins',
        employeeId: 'EMP-4092',
        designation: 'Senior Developer',
        department: 'Engineering',
        month: currentMonth,
        currency: '$',
        earnings: [
            { name: 'Basic Salary', amount: 4500 },
            { name: 'House Rent Allowance', amount: 1500 },
            { name: 'Medical Allowance', amount: 300 },
            { name: 'Transport Allowance', amount: 200 }
        ],
        deductions: [
            { name: 'Provident Fund', amount: 450 },
            { name: 'Income Tax', amount: 800 },
            { name: 'Health Insurance', amount: 120 }
        ]
    };
};

const printSlip = () => {
    window.print();
};
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #salary-slip-preview, #salary-slip-preview * {
        visibility: visible;
    }
    #salary-slip-preview {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
    }
}
</style>
