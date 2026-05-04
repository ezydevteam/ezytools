<template>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">PDF to Images</h1>
        <p class="text-surface-600 dark:text-surface-400 mb-8">Convert every page of your PDF into high-quality JPG or PNG images.</p>

        <div v-if="!file" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8">
            <PdfUploadZone
                @files-selected="onFileSelected"
                @error="error = $event"
                :max-size="tool.settings?.free_max_mb * 1024 * 1024 || 20485760"
            />
            <p v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</p>
        </div>

        <div v-else class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="p-6 md:p-8 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-xl flex items-center justify-center">
                        <PhotoIcon class="w-6 h-6" />
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
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-4">Output Format</label>
                    <div class="grid grid-cols-2 gap-4 md:w-1/2">
                        <button
                            @click="format = 'jpg'"
                            class="p-4 rounded-xl border-2 transition-all flex flex-col items-center gap-2 text-center"
                            :class="format === 'jpg' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 text-surface-600 dark:text-surface-400'"
                        >
                            <span class="font-bold text-lg">JPG</span>
                            <span class="text-xs opacity-80">Smaller file size</span>
                        </button>

                        <button
                            @click="format = 'png'"
                            class="p-4 rounded-xl border-2 transition-all flex flex-col items-center gap-2 text-center"
                            :class="format === 'png' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300 text-surface-600 dark:text-surface-400'"
                        >
                            <span class="font-bold text-lg">PNG</span>
                            <span class="text-xs opacity-80">Highest quality</span>
                        </button>
                    </div>
                </div>

                <div v-if="processError" class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-sm border border-red-100 dark:border-red-800">
                    {{ processError }}
                </div>

                <div class="flex justify-end pt-4 border-t border-surface-100 dark:border-surface-700">
                    <button
                        @click="convertToImages"
                        :disabled="isProcessing"
                        class="bg-gradient-to-r from-primary-600 to-purple-600 text-white shadow-lg hover:shadow-xl shadow-primary-500/30 transition-all duration-300 px-8 py-3 rounded-xl text-lg font-medium flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': isProcessing}"
                    >
                        <ArrowPathIcon v-if="isProcessing" class="w-5 h-5 animate-spin" />
                        <PhotoIcon v-else class="w-5 h-5" />
                        {{ isProcessing ? 'Converting...' : 'Convert to Images' }}
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
                <p class="text-surface-600 dark:text-surface-400 mb-8">All pages have been extracted into a ZIP file.</p>

                <div class="space-y-4">
                    <a :href="downloadUrl" download class="btn-primary w-full py-4 rounded-xl text-lg font-medium flex items-center justify-center gap-2">
                        <ArrowDownTrayIcon class="w-6 h-6" />
                        Download ZIP File
                    </a>

                    <button @click="resetFull" class="w-full py-3 text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 font-medium transition-colors">
                        Convert Another PDF
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
import { ArrowPathIcon, CheckCircleIcon, ArrowDownTrayIcon, PhotoIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object
});

const file = ref(null);
const error = ref('');
const processError = ref('');
const isProcessing = ref(false);
const downloadUrl = ref('');
const format = ref('jpg');

const onFileSelected = (files) => {
    error.value = '';
    processError.value = '';
    file.value = files[0];
};

const reset = () => {
    file.value = null;
    processError.value = '';
    format.value = 'jpg';
};

const resetFull = () => {
    reset();
    downloadUrl.value = '';
};

const convertToImages = async () => {
    if (!file.value) return;

    isProcessing.value = true;
    processError.value = '';

    const formData = new FormData();
    formData.append('file', file.value);
    formData.append('format', format.value);

    try {
        const response = await axios.post('/api/pdf/pdf-to-images', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.success) {
            downloadUrl.value = response.data.download_url;
        } else {
            processError.value = response.data.message || 'An error occurred while converting.';
        }
    } catch (err) {
        processError.value = err.response?.data?.message || 'Failed to connect to the server. Please try again.';
    } finally {
        isProcessing.value = false;
    }
};
</script>
