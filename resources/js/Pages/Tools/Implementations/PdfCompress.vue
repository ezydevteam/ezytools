<template>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">Compress PDF</h1>
        <p class="text-surface-600 dark:text-surface-400 mb-8">Reduce the file size of your PDF document without losing essential quality.</p>

        <div v-if="!file" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8">
            <PdfUploadZone 
                @files-selected="onFileSelected" 
                @error="error = $event"
                :max-size="tool.settings?.free_max_mb * 1024 * 1024 || 50485760" 
            />
            <p v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</p>
        </div>

        <div v-else class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="p-6 md:p-8 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-xl flex items-center justify-center">
                        <ArrowsPointingInIcon class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">{{ file.name }}</h3>
                        <p class="text-sm text-surface-500">Original Size: <span class="font-bold">{{ (file.size / 1024 / 1024).toFixed(2) }} MB</span></p>
                    </div>
                </div>
                <button @click="reset" class="text-red-500 hover:text-red-600 text-sm font-medium px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">
                    Remove File
                </button>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-4">Compression Level</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button 
                            @click="quality = 'screen'"
                            class="p-4 rounded-xl border-2 transition-all flex flex-col items-center gap-2 text-center"
                            :class="quality === 'screen' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 text-surface-600 dark:text-surface-400'"
                        >
                            <span class="font-bold text-lg">Maximum</span>
                            <span class="text-xs opacity-80">Smallest file size, lowest quality (72 DPI). Best for email.</span>
                        </button>
                        
                        <button 
                            @click="quality = 'ebook'"
                            class="p-4 rounded-xl border-2 transition-all flex flex-col items-center gap-2 text-center"
                            :class="quality === 'ebook' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 text-surface-600 dark:text-surface-400'"
                        >
                            <span class="font-bold text-lg">Recommended</span>
                            <span class="text-xs opacity-80">Good balance of quality and file size (150 DPI).</span>
                        </button>

                        <button 
                            @click="quality = 'printer'"
                            class="p-4 rounded-xl border-2 transition-all flex flex-col items-center gap-2 text-center"
                            :class="quality === 'printer' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 text-surface-600 dark:text-surface-400'"
                        >
                            <span class="font-bold text-lg">Low</span>
                            <span class="text-xs opacity-80">High quality, larger file size (300 DPI). Best for printing.</span>
                        </button>
                    </div>
                </div>

                <div v-if="processError" class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-sm border border-red-100 dark:border-red-800">
                    {{ processError }}
                </div>

                <div class="flex justify-end pt-4 border-t border-surface-100 dark:border-surface-700">
                    <button 
                        @click="compressPdf" 
                        :disabled="isProcessing"
                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white shadow-lg hover:shadow-xl shadow-green-500/30 transition-all duration-300 px-8 py-3 rounded-xl text-lg font-medium flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': isProcessing}"
                    >
                        <ArrowPathIcon v-if="isProcessing" class="w-5 h-5 animate-spin" />
                        <ArrowsPointingInIcon v-else class="w-5 h-5" />
                        {{ isProcessing ? 'Compressing...' : 'Compress PDF' }}
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
                <p class="text-surface-600 dark:text-surface-400 mb-8">
                    Your PDF was compressed from <strong>{{ originalSizeMb }} MB</strong> down to <strong>{{ compressedSizeMb }} MB</strong>!
                </p>
                
                <div class="space-y-4">
                    <a :href="downloadUrl" download class="btn-primary w-full py-4 rounded-xl text-lg font-medium flex items-center justify-center gap-2">
                        <ArrowDownTrayIcon class="w-6 h-6" />
                        Download Compressed PDF
                    </a>
                    
                    <button @click="resetFull" class="w-full py-3 text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 font-medium transition-colors">
                        Compress Another File
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
import { ArrowPathIcon, CheckCircleIcon, ArrowDownTrayIcon, ArrowsPointingInIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object
});

const file = ref(null);
const error = ref('');
const processError = ref('');
const isProcessing = ref(false);
const downloadUrl = ref('');
const quality = ref('ebook');
const originalSizeMb = ref('0');
const compressedSizeMb = ref('0');

const onFileSelected = (files) => {
    error.value = '';
    processError.value = '';
    file.value = files[0];
};

const reset = () => {
    file.value = null;
    processError.value = '';
    quality.value = 'ebook';
};

const resetFull = () => {
    reset();
    downloadUrl.value = '';
};

const compressPdf = async () => {
    if (!file.value) return;
    
    isProcessing.value = true;
    processError.value = '';
    
    const formData = new FormData();
    formData.append('file', file.value);
    formData.append('quality', quality.value);
    
    try {
        const response = await axios.post('/api/pdf/compress', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            originalSizeMb.value = (file.value.size / 1024 / 1024).toFixed(2);
            compressedSizeMb.value = response.data.new_size_mb;
            downloadUrl.value = response.data.download_url;
        } else {
            processError.value = response.data.message || 'An error occurred while compressing.';
        }
    } catch (err) {
        processError.value = err.response?.data?.message || 'Failed to connect to the server. Please try again.';
    } finally {
        isProcessing.value = false;
    }
};
</script>
