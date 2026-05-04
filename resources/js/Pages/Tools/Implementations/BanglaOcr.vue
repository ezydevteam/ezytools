<template>
    <div>
        <!-- Inline Alert Message -->
        <div v-if="alertMessage" :class="[
            'mb-4 p-4 rounded-xl border flex items-center gap-3 transition-all',
            alertType === 'error' ? 'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300' : 'bg-green-50 border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300'
        ]">
            <svg v-if="alertType === 'error'" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <svg v-else class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium">{{ alertMessage }}</p>
            <button @click="alertMessage = ''" class="ml-auto opacity-70 hover:opacity-100 p-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                Upload Image for Bangla OCR
            </label>
            <div
                class="relative flex flex-col items-center justify-center p-8 border-2 border-dashed rounded-xl transition-colors cursor-pointer"
                :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-800/50 hover:bg-surface-100 dark:hover:bg-surface-800'"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop"
                @click="$refs.fileInput.click()"
            >
                <input
                    ref="fileInput"
                    type="file"
                    class="hidden"
                    accept="image/*"
                    @change="handleFileSelect"
                />
                
                <div v-if="previewUrl" class="mb-4 text-center">
                    <img :src="previewUrl" class="max-h-64 object-contain rounded-lg mx-auto" alt="Preview" />
                </div>
                
                <div v-else class="text-center">
                    <svg class="mx-auto h-12 w-12 text-surface-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" />
                    </svg>
                    <p class="mt-2 text-sm text-surface-600 dark:text-surface-400">
                        <span class="font-semibold text-primary-600 dark:text-primary-400">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-surface-500 mt-1">PNG, JPG, JPEG formats</p>
                </div>
            </div>
        </div>

        <div v-if="isProcessing" class="my-6 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-100 dark:border-primary-900 text-center">
            <svg class="animate-spin h-8 w-8 text-primary-600 dark:text-primary-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm font-medium text-primary-800 dark:text-primary-200">{{ statusText }}</p>
            <div v-if="progress > 0" class="mt-2 w-full bg-surface-200 dark:bg-surface-700 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full transition-all duration-300" :style="{ width: progress + '%' }"></div>
            </div>
        </div>

        <div class="mb-4" v-if="text">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                Extracted Text
            </label>
            <textarea
                v-model="text"
                rows="8"
                class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-lg dark:bg-surface-800 dark:border-surface-600 dark:text-white transition-colors"
            ></textarea>
        </div>

        <div class="flex gap-2 mb-6" v-if="text">
            <button @click="copyText" class="px-4 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm min-w-[100px]">
                {{ isCopied ? 'Copied!' : 'Copy Text' }}
            </button>
            <button @click="text = ''; previewUrl = null" class="px-4 py-2 bg-surface-200 hover:bg-surface-300 dark:bg-surface-700 dark:hover:bg-surface-600 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-medium transition-colors">
                Clear
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    tool: Object,
    settings: Object,
});

const isDragging = ref(false);
const isProcessing = ref(false);
const statusText = ref('Initializing...');
const progress = ref(0);
const previewUrl = ref(null);
const text = ref('');
const fileInput = ref(null);
const isCopied = ref(false);

const alertMessage = ref('');
const alertType = ref('success');
let alertTimeout = null;

const showAlert = (message, type = 'success') => {
    alertMessage.value = message;
    alertType.value = type;
    if (alertTimeout) clearTimeout(alertTimeout);
    alertTimeout = setTimeout(() => {
        alertMessage.value = '';
    }, 5000);
};

const toast = {
    success: (msg) => showAlert(msg, 'success'),
    error: (msg) => showAlert(msg, 'error')
};

onMounted(() => {
    // Dynamically load Tesseract.js script
    if (!window.Tesseract) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js';
        script.async = true;
        document.head.appendChild(script);
    }
});

const handleDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        processImage(file);
    } else {
        toast.error("Please drop a valid image file.");
    }
};

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file) {
        processImage(file);
    }
};

const processImage = async (file) => {
    if (!window.Tesseract) {
        toast.error("OCR engine is still loading. Please try again in a moment.");
        return;
    }

    previewUrl.value = URL.createObjectURL(file);
    text.value = '';
    isProcessing.value = true;
    progress.value = 0;
    statusText.value = 'Preparing image...';

    try {
        const worker = await window.Tesseract.createWorker('ben+eng', 1, {
            logger: m => {
                if (m.status === 'recognizing text') {
                    statusText.value = 'Extracting text...';
                    progress.value = Math.round(m.progress * 100);
                } else if (m.status === 'loading language traineddata' || m.status === 'loading tesseract core') {
                    statusText.value = 'Loading language data...';
                } else {
                    statusText.value = m.status;
                }
            }
        });

        const ret = await worker.recognize(file);
        text.value = ret.data.text;
        await worker.terminate();
        
        toast.success("Text extracted successfully!");
    } catch (error) {
        console.error(error);
        toast.error("Failed to extract text from the image.");
    } finally {
        isProcessing.value = false;
    }
};

const copyText = () => {
    if (!text.value) return;
    navigator.clipboard.writeText(text.value).then(() => {
        isCopied.value = true;
        setTimeout(() => isCopied.value = false, 2000);
    });
};
</script>
