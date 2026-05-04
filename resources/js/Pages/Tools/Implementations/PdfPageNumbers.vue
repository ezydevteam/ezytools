<template>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">Add Page Numbers</h1>
        <p class="text-surface-600 dark:text-surface-400 mb-8">Insert page numbers into your PDF document easily.</p>

        <div v-if="!file" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8">
            <PdfUploadZone 
                @files-selected="onFileSelected" 
                @error="error = $event"
                :max-size="tool.settings?.free_max_mb * 1024 * 1024 || 10485760" 
            />
            <p v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</p>
        </div>

        <div v-else class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="p-6 md:p-8 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-xl flex items-center justify-center">
                        <HashtagIcon class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">{{ file.name }}</h3>
                        <p class="text-sm text-surface-500">{{ (file.size / 1024 / 1024).toFixed(2) }} MB</p>
                    </div>
                </div>
                <button @click="reset" class="text-red-500 hover:text-red-600 text-sm font-medium px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">
                    Remove File
                </button>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Position</label>
                        <div class="grid grid-cols-3 gap-2 p-2 bg-surface-100 dark:bg-surface-900 rounded-xl">
                            <button v-for="pos in positions" :key="pos.value" @click="config.position = pos.value" class="p-3 text-sm rounded-lg transition-colors border-2" :class="config.position === pos.value ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-300 font-bold' : 'border-transparent text-surface-600 dark:text-surface-400 hover:bg-surface-200 dark:hover:bg-surface-800'">
                                {{ pos.label }}
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Format</label>
                            <select v-model="config.format" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                <option value="1">1, 2, 3</option>
                                <option value="1 of n">1 of 3</option>
                                <option value="Page 1">Page 1</option>
                                <option value="Page 1 of n">Page 1 of 3</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Font Size</label>
                                <span class="text-sm text-surface-500">{{ config.size }}pt</span>
                            </div>
                            <input type="range" v-model="config.size" min="8" max="48" class="w-full accent-primary-500" />
                        </div>
                    </div>
                </div>

                <div v-if="processError" class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-sm border border-red-100 dark:border-red-800">
                    {{ processError }}
                </div>

                <div class="flex justify-end pt-4 border-t border-surface-100 dark:border-surface-700">
                    <button 
                        @click="addNumbers" 
                        :disabled="isProcessing"
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg hover:shadow-xl shadow-blue-500/30 transition-all duration-300 px-8 py-3 rounded-xl text-lg font-medium flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': isProcessing}"
                    >
                        <ArrowPathIcon v-if="isProcessing" class="w-5 h-5 animate-spin" />
                        <HashtagIcon v-else class="w-5 h-5" />
                        {{ isProcessing ? 'Processing...' : 'Add Page Numbers' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Download Modal -->
        <div v-if="downloadUrl" class="fixed inset-0 z-50 flex items-center justify-center bg-surface-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-surface-800 rounded-3xl shadow-2xl p-8 md:p-12 max-w-md w-full text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-emerald-500"></div>
                
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <CheckCircleIcon class="w-10 h-10" />
                </div>
                
                <h3 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Success!</h3>
                <p class="text-surface-600 dark:text-surface-400 mb-8">Page numbers have been successfully added.</p>
                
                <div class="space-y-4">
                    <a :href="downloadUrl" download class="btn-primary w-full py-4 rounded-xl text-lg font-medium flex items-center justify-center gap-2">
                        <ArrowDownTrayIcon class="w-6 h-6" />
                        Download PDF
                    </a>
                    
                    <button @click="resetFull" class="w-full py-3 text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 font-medium transition-colors">
                        Process Another File
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import PdfUploadZone from '@/Components/PDF/PdfUploadZone.vue';
import { ArrowPathIcon, CheckCircleIcon, ArrowDownTrayIcon, HashtagIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object
});

const file = ref(null);
const error = ref('');
const processError = ref('');
const isProcessing = ref(false);
const downloadUrl = ref('');

const positions = [
    { label: 'Top Left', value: 'top-left' },
    { label: 'Top Center', value: 'top-center' },
    { label: 'Top Right', value: 'top-right' },
    { label: 'Bottom Left', value: 'bottom-left' },
    { label: 'Bottom Center', value: 'bottom-center' },
    { label: 'Bottom Right', value: 'bottom-right' }
];

const config = ref({
    position: 'bottom-center',
    format: 'Page 1 of n',
    size: 12
});

const onFileSelected = (files) => {
    error.value = '';
    processError.value = '';
    file.value = files[0];
};

const reset = () => {
    file.value = null;
    processError.value = '';
};

const resetFull = () => {
    reset();
    downloadUrl.value = '';
};

const addNumbers = async () => {
    if (!file.value) return;
    
    isProcessing.value = true;
    processError.value = '';
    
    const formData = new FormData();
    formData.append('file', file.value);
    formData.append('config', JSON.stringify(config.value));
    
    try {
        const response = await axios.post('/api/pdf/add-page-numbers', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            downloadUrl.value = response.data.download_url;
        } else {
            processError.value = response.data.message || 'An error occurred.';
        }
    } catch (err) {
        processError.value = err.response?.data?.message || 'Failed to connect to the server. Please try again.';
    } finally {
        isProcessing.value = false;
    }
};
</script>
