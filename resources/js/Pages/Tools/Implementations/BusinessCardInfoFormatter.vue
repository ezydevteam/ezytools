<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="print:hidden mb-8 flex justify-between items-center bg-surface-50 dark:bg-surface-900 p-4 rounded-xl border border-surface-200 dark:border-surface-700">
            <h3 class="font-bold text-surface-900 dark:text-white">Business Card Formatter</h3>
            <div class="flex gap-2">
                <button @click="loadSampleData" class="px-3 py-1.5 text-sm bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 rounded-lg transition-colors">Load Sample</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Form Area -->
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">First Name</label>
                        <input type="text" v-model="card.firstName" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Last Name</label>
                        <input type="text" v-model="card.lastName" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Job Title</label>
                        <input type="text" v-model="card.title" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Company / Organization</label>
                        <input type="text" v-model="card.company" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Phone Number</label>
                        <input type="text" v-model="card.phone" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-500 mb-1">Email Address</label>
                        <input type="email" v-model="card.email" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-surface-500 mb-1">Website URL</label>
                    <input type="text" v-model="card.website" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-surface-500 mb-1">Full Address</label>
                    <textarea v-model="card.address" rows="2" class="w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500 resize-none"></textarea>
                </div>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col space-y-6">
                <!-- Visual Preview -->
                <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex items-center justify-center min-h-[250px]">
                    <div class="w-full max-w-[400px] bg-white border border-surface-200 shadow-md rounded-lg p-6 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-2 h-full bg-primary-500"></div>
                        <div class="pl-4">
                            <h2 class="text-xl font-bold text-surface-900 leading-tight">{{ card.firstName || 'First' }} {{ card.lastName || 'Last' }}</h2>
                            <p class="text-sm font-medium text-primary-600 mb-4">{{ card.title || 'Job Title' }} <span v-if="card.company" class="text-surface-400">@ {{ card.company }}</span></p>
                            
                            <div class="space-y-2 mt-4 text-xs text-surface-600">
                                <div v-if="card.phone" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    {{ card.phone }}
                                </div>
                                <div v-if="card.email" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    {{ card.email }}
                                </div>
                                <div v-if="card.website" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                                    {{ card.website }}
                                </div>
                                <div v-if="card.address" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span class="truncate">{{ card.address }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VCard / QR Output -->
                <div class="flex-1 border border-surface-200 dark:border-surface-700 rounded-xl overflow-hidden flex flex-col">
                    <div class="bg-surface-100 dark:bg-surface-900 px-4 py-2 flex justify-between items-center border-b border-surface-200 dark:border-surface-700">
                        <span class="text-xs font-bold text-surface-700 dark:text-surface-300">Generated vCard</span>
                        <div class="flex gap-1">
                            <button @click="downloadVCard" class="text-xs px-2 py-1 bg-white dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">.VCF</button>
                            <button @click="copyToClipboard" class="text-xs px-2 py-1 bg-white dark:bg-surface-800 rounded border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors" :class="copied ? 'text-green-500' : ''">{{ copied ? 'Copied' : 'Copy' }}</button>
                        </div>
                    </div>
                    <div class="p-4 bg-[#1e1e1e] text-[#d4d4d4] flex-1 overflow-auto">
                        <pre class="font-mono text-xs whitespace-pre-wrap break-all">{{ vCardData }}</pre>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const card = ref({
    firstName: '',
    lastName: '',
    title: '',
    company: '',
    phone: '',
    email: '',
    website: '',
    address: ''
});

const copied = ref(false);

const vCardData = computed(() => {
    let vcf = [];
    vcf.push('BEGIN:VCARD');
    vcf.push('VERSION:3.0');
    vcf.push(`N:${card.value.lastName};${card.value.firstName};;;`);
    vcf.push(`FN:${card.value.firstName} ${card.value.lastName}`.trim());
    
    if (card.value.company) vcf.push(`ORG:${card.value.company}`);
    if (card.value.title) vcf.push(`TITLE:${card.value.title}`);
    if (card.value.phone) vcf.push(`TEL;TYPE=WORK,VOICE:${card.value.phone}`);
    if (card.value.email) vcf.push(`EMAIL;TYPE=PREF,INTERNET:${card.value.email}`);
    if (card.value.website) vcf.push(`URL:${card.value.website}`);
    if (card.value.address) vcf.push(`ADR;TYPE=WORK:;;${card.value.address.replace(/\n/g, ' ')};;;;`);
    
    vcf.push('END:VCARD');
    return vcf.join('\n');
});

const loadSampleData = () => {
    card.value = {
        firstName: 'John',
        lastName: 'Doe',
        title: 'Software Engineer',
        company: 'EzyTools Tech',
        phone: '+1 (555) 123-4567',
        email: 'john.doe@example.com',
        website: 'https://example.com',
        address: '123 Tech Lane\nSilicon Valley, CA 94025'
    };
};

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(vCardData.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};

const downloadVCard = () => {
    const blob = new Blob([vCardData.value], { type: 'text/vcard' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    const name = card.value.firstName ? `${card.value.firstName.toLowerCase()}_${card.value.lastName.toLowerCase()}` : 'contact';
    a.download = `${name}.vcf`;
    a.click();
    URL.revokeObjectURL(url);
};
</script>
