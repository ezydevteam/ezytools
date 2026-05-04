<template>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">Watermark PDF</h1>
        <p class="text-surface-600 dark:text-surface-400 mb-8">Add a text watermark to every page of your PDF document.</p>

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
                        <DocumentTextIcon class="w-6 h-6" />
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
                <!-- Settings Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Text Input -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Watermark Text</label>
                        <input 
                            type="text" 
                            v-model="config.text" 
                            placeholder="CONFIDENTIAL"
                            class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-lg font-bold tracking-wider"
                        />
                    </div>

                    <!-- Size and Opacity -->
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Font Size</label>
                                <span class="text-sm text-surface-500">{{ config.size }}pt</span>
                            </div>
                            <input type="range" v-model="config.size" min="12" max="150" class="w-full accent-primary-500" />
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Opacity</label>
                                <span class="text-sm text-surface-500">{{ Math.round(config.opacity * 100) }}%</span>
                            </div>
                            <input type="range" v-model="config.opacity" min="0.1" max="1" step="0.1" class="w-full accent-primary-500" />
                        </div>
                    </div>

                    <!-- Rotation and Color -->
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Rotation</label>
                                <span class="text-sm text-surface-500">{{ config.rotation }}&deg;</span>
                            </div>
                            <input type="range" v-model="config.rotation" min="0" max="360" step="15" class="w-full accent-primary-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Color</label>
                            <div class="flex gap-3">
                                <button v-for="c in colors" :key="c.val" @click="config.color = c.val" class="w-10 h-10 rounded-full border-2 transition-transform hover:scale-110 flex items-center justify-center" :class="config.color === c.val ? 'border-primary-500 ring-2 ring-primary-200' : 'border-surface-200'" :style="{ backgroundColor: c.hex }">
                                    <CheckIcon v-if="config.color === c.val" class="w-5 h-5 text-white mix-blend-difference" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Box -->
                <div class="bg-surface-100 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 h-96 md:h-[600px] flex items-center justify-center p-4 md:p-8 relative">
                    <div class="relative h-full shadow-2xl bg-white transition-all overflow-hidden inline-block" style="aspect-ratio: 1/1.4;">
                        <!-- The underlying PDF Page -->
                        <VuePdfEmbed 
                            v-if="pdfSource" 
                            :source="pdfSource" 
                            :page="1" 
                            class="absolute inset-0 w-full h-full pointer-events-none"
                        />
                        
                        <!-- Watermark Overlay -->
                        <div class="absolute inset-0 flex items-center justify-center overflow-hidden pointer-events-none z-10">
                            <span 
                                class="font-bold whitespace-nowrap transition-all"
                                :style="{
                                    fontSize: `${config.size}px`,
                                    opacity: config.opacity,
                                    transform: `rotate(-${config.rotation}deg)`,
                                    color: colors.find(c => c.val === config.color)?.hex || '#000000'
                                }"
                            >
                                {{ config.text || 'CONFIDENTIAL' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="processError" class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-sm border border-red-100 dark:border-red-800">
                    {{ processError }}
                </div>

                <div class="flex justify-end pt-4 border-t border-surface-100 dark:border-surface-700">
                    <button 
                        @click="addWatermark" 
                        :disabled="isProcessing || !config.text"
                        class="bg-gradient-to-r from-blue-500 to-primary-600 hover:from-blue-600 hover:to-primary-700 text-white shadow-lg hover:shadow-xl shadow-blue-500/30 transition-all duration-300 px-8 py-3 rounded-xl text-lg font-medium flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': isProcessing || !config.text}"
                    >
                        <ArrowPathIcon v-if="isProcessing" class="w-5 h-5 animate-spin" />
                        <DocumentTextIcon v-else class="w-5 h-5" />
                        {{ isProcessing ? 'Processing...' : 'Add Watermark' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Download Modal Overlay -->
        <div v-if="downloadUrl" class="fixed inset-0 z-50 flex items-center justify-center bg-surface-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-surface-800 rounded-3xl shadow-2xl p-8 md:p-12 max-w-md w-full text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-emerald-500"></div>
                
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <CheckCircleIcon class="w-10 h-10" />
                </div>
                
                <h3 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Success!</h3>
                <p class="text-surface-600 dark:text-surface-400 mb-8">The watermark has been applied to all pages.</p>
                
                <div class="space-y-4">
                    <a :href="downloadUrl" download class="btn-primary w-full py-4 rounded-xl text-lg font-medium flex items-center justify-center gap-2">
                        <ArrowDownTrayIcon class="w-6 h-6" />
                        Download PDF
                    </a>
                    
                    <button @click="resetFull" class="w-full py-3 text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 font-medium transition-colors">
                        Watermark Another File
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
import { DocumentTextIcon, ArrowPathIcon, CheckCircleIcon, ArrowDownTrayIcon, CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object
});

const file = ref(null);
const error = ref('');
const processError = ref('');
const isProcessing = ref(false);
const downloadUrl = ref('');
const pdfSource = ref(null);

const colors = [
    { val: 'black', hex: '#000000' },
    { val: 'gray', hex: '#6B7280' },
    { val: 'red', hex: '#EF4444' },
    { val: 'blue', hex: '#3B82F6' },
];

const config = ref({
    text: 'CONFIDENTIAL',
    size: 60,
    opacity: 0.3,
    rotation: 45,
    color: 'gray'
});

const onFileSelected = (files) => {
    error.value = '';
    processError.value = '';
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

const addWatermark = async () => {
    if (!file.value || !config.value.text) return;
    
    isProcessing.value = true;
    processError.value = '';
    
    const formData = new FormData();
    formData.append('file', file.value);
    formData.append('config', JSON.stringify(config.value));
    
    try {
        const response = await axios.post('/api/pdf/watermark', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            downloadUrl.value = response.data.download_url;
        } else {
            processError.value = response.data.message || 'An error occurred while watermarking.';
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
