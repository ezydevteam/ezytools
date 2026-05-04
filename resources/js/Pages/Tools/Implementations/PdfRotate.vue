<template>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">Rotate PDF</h1>
        <p class="text-surface-600 dark:text-surface-400 mb-8">Rotate your PDF pages to the correct orientation.</p>

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
                        <ArrowPathIcon class="w-6 h-6" />
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

            <div class="p-6 md:p-8 space-y-8">
                <!-- Preview Box -->
                <div class="bg-surface-100 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 h-96 md:h-[500px] flex items-center justify-center p-4 md:p-8 relative">
                    <div class="relative h-full shadow-2xl bg-white transition-transform duration-500 overflow-hidden inline-block" style="aspect-ratio: 1/1.4;" :style="{ transform: `rotate(${rotation}deg)` }">
                        <VuePdfEmbed 
                            v-if="pdfSource" 
                            :source="pdfSource" 
                            :page="1" 
                            class="absolute inset-0 w-full h-full pointer-events-none"
                        />
                    </div>
                </div>

                <!-- Rotation Controls -->
                <div class="flex flex-col items-center justify-center gap-4">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Rotate all pages</label>
                    <div class="flex gap-4">
                        <button @click="rotation -= 90" class="p-4 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all flex flex-col items-center gap-2">
                            <ArrowUturnLeftIcon class="w-6 h-6 text-surface-600 dark:text-surface-300" />
                            <span class="text-sm font-medium">Left (-90&deg;)</span>
                        </button>
                        <button @click="rotation += 90" class="p-4 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all flex flex-col items-center gap-2">
                            <ArrowUturnRightIcon class="w-6 h-6 text-surface-600 dark:text-surface-300" />
                            <span class="text-sm font-medium">Right (+90&deg;)</span>
                        </button>
                    </div>
                </div>

                <div v-if="processError" class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-sm border border-red-100 dark:border-red-800">
                    {{ processError }}
                </div>

                <div class="flex justify-end pt-4 border-t border-surface-100 dark:border-surface-700">
                    <button 
                        @click="rotatePdf" 
                        :disabled="isProcessing || rotation % 360 === 0"
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-lg hover:shadow-xl shadow-blue-500/30 transition-all duration-300 px-8 py-3 rounded-xl text-lg font-medium flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': isProcessing || rotation % 360 === 0}"
                    >
                        <ArrowPathIcon v-if="isProcessing" class="w-5 h-5 animate-spin" />
                        <ArrowPathIcon v-else class="w-5 h-5" />
                        {{ isProcessing ? 'Processing...' : 'Apply Rotation' }}
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
                <p class="text-surface-600 dark:text-surface-400 mb-8">Your PDF has been rotated successfully.</p>
                
                <div class="space-y-4">
                    <a :href="downloadUrl" download class="btn-primary w-full py-4 rounded-xl text-lg font-medium flex items-center justify-center gap-2">
                        <ArrowDownTrayIcon class="w-6 h-6" />
                        Download PDF
                    </a>
                    
                    <button @click="resetFull" class="w-full py-3 text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 font-medium transition-colors">
                        Rotate Another File
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onBeforeUnmount } from 'vue';
import axios from 'axios';
import VuePdfEmbed from 'vue-pdf-embed';
import PdfUploadZone from '@/Components/PDF/PdfUploadZone.vue';
import { ArrowPathIcon, CheckCircleIcon, ArrowDownTrayIcon, ArrowUturnLeftIcon, ArrowUturnRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object
});

const file = ref(null);
const error = ref('');
const processError = ref('');
const isProcessing = ref(false);
const downloadUrl = ref('');
const pdfSource = ref(null);
const rotation = ref(0);

const onFileSelected = (files) => {
    error.value = '';
    processError.value = '';
    rotation.value = 0;
    file.value = files[0];
    
    if (pdfSource.value) {
        URL.revokeObjectURL(pdfSource.value);
    }
    pdfSource.value = URL.createObjectURL(files[0]);
};

const reset = () => {
    if (pdfSource.value) {
        URL.revokeObjectURL(pdfSource.value);
    }
    file.value = null;
    pdfSource.value = null;
    rotation.value = 0;
    processError.value = '';
};

onBeforeUnmount(() => {
    if (pdfSource.value) {
        URL.revokeObjectURL(pdfSource.value);
    }
});

const resetFull = () => {
    reset();
    downloadUrl.value = '';
};

const rotatePdf = async () => {
    if (!file.value || rotation.value % 360 === 0) return;
    
    isProcessing.value = true;
    processError.value = '';
    
    // Normalize rotation to 0, 90, 180, 270
    let rot = rotation.value % 360;
    if (rot < 0) rot += 360;
    
    const formData = new FormData();
    formData.append('file', file.value);
    formData.append('rotation', rot);
    
    try {
        const response = await axios.post('/api/pdf/rotate', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            downloadUrl.value = response.data.download_url;
        } else {
            processError.value = response.data.message || 'An error occurred while rotating.';
        }
    } catch (err) {
        processError.value = err.response?.data?.message || 'Failed to connect to the server. Please try again.';
    } finally {
        isProcessing.value = false;
    }
};
</script>

<style>
.textLayer,
.annotationLayer {
    display: none !important;
}
</style>
