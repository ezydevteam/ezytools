<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div v-if="!selectedFile" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop" :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-primary-400': dragover }">
            <input type="file" @change="handleFileSelect" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-primary-900/50 dark:text-primary-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Click or drag image to compress</h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">Supports JPG, PNG, WEBP (Max 10MB)</p>
        </div>

        <div v-else class="space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">Compression Settings</h3>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">Start Over</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Settings Area -->
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between mb-2">
                            <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Compression Level (Quality)</label>
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ quality }}%</span>
                        </div>
                        <input type="range" v-model="quality" min="10" max="100" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-primary-600" @change="compressImage">
                        <div class="flex justify-between text-xs text-surface-500 mt-1">
                            <span>Smallest File</span>
                            <span>Best Quality</span>
                        </div>
                    </div>

                    <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-surface-600 dark:text-surface-400">Original Size:</span>
                            <span class="font-medium text-surface-900 dark:text-white">{{ formatBytes(originalSize) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-surface-600 dark:text-surface-400">Compressed Size:</span>
                            <span class="font-bold text-green-600 dark:text-green-400">
                                <svg v-if="isCompressing" class="animate-spin h-4 w-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <template v-else>{{ formatBytes(compressedSize) }}</template>
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-surface-200 dark:border-surface-700">
                            <span class="text-sm text-surface-600 dark:text-surface-400">Space Saved:</span>
                            <span class="font-black text-primary-600 dark:text-primary-400">{{ savedPercentage }}%</span>
                        </div>
                    </div>

                    <button @click="downloadCompressed" :disabled="isCompressing || !compressedUrl" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download Compressed Image
                    </button>
                </div>

                <!-- Preview Area -->
                <div class="flex flex-col h-full">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                    <div class="flex-1 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden relative flex items-center justify-center">
                        <img v-if="compressedUrl" :src="compressedUrl" class="max-w-full max-h-[300px] object-contain" alt="Compressed Preview">
                        <div v-if="isCompressing" class="absolute inset-0 bg-white/50 dark:bg-surface-800/50 flex items-center justify-center backdrop-blur-sm">
                            <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const dragover = ref(false);
const selectedFile = ref(null);
const quality = ref(80);
const originalSize = ref(0);
const compressedSize = ref(0);
const compressedUrl = ref(null);
const compressedBlob = ref(null);
const isCompressing = ref(false);

const handleDrop = (e) => {
    dragover.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        processFile(file);
    } else {
        alert('Please drop a valid image file.');
    }
};

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file) {
        processFile(file);
    }
};

const processFile = (file) => {
    selectedFile.value = file;
    originalSize.value = file.size;
    compressImage();
};

const compressImage = () => {
    if (!selectedFile.value) return;
    
    isCompressing.value = true;
    
    const reader = new FileReader();
    reader.readAsDataURL(selectedFile.value);
    reader.onload = (event) => {
        const img = new Image();
        img.src = event.target.result;
        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            
            // Keep original format or fallback to JPEG if not supported
            let format = selectedFile.value.type;
            if (format !== 'image/jpeg' && format !== 'image/webp') {
                format = 'image/jpeg'; // PNG compression is lossless in Canvas, so we convert to JPEG for compression
            }

            canvas.toBlob((blob) => {
                compressedBlob.value = blob;
                compressedSize.value = blob.size;
                
                if (compressedUrl.value) {
                    URL.revokeObjectURL(compressedUrl.value);
                }
                compressedUrl.value = URL.createObjectURL(blob);
                isCompressing.value = false;
            }, format, quality.value / 100);
        };
    };
};

const savedPercentage = computed(() => {
    if (originalSize.value === 0 || compressedSize.value === 0) return 0;
    const saved = originalSize.value - compressedSize.value;
    if (saved <= 0) return 0;
    return Math.round((saved / originalSize.value) * 100);
});

const formatBytes = (bytes, decimals = 2) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
};

const downloadCompressed = () => {
    if (!compressedBlob.value) return;
    
    const url = URL.createObjectURL(compressedBlob.value);
    const a = document.createElement('a');
    a.href = url;
    
    // Maintain extension
    const extension = selectedFile.value.name.split('.').pop();
    const nameWithoutExt = selectedFile.value.name.replace(`.${extension}`, '');
    let finalExtension = extension;
    
    // If we converted PNG to JPEG for compression
    if (selectedFile.value.type === 'image/png' && compressedBlob.value.type === 'image/jpeg') {
        finalExtension = 'jpg';
    }

    a.download = `${nameWithoutExt}-compressed.${finalExtension}`;
    a.click();
    URL.revokeObjectURL(url);
};

const resetTool = () => {
    selectedFile.value = null;
    quality.value = 80;
    originalSize.value = 0;
    compressedSize.value = 0;
    if (compressedUrl.value) {
        URL.revokeObjectURL(compressedUrl.value);
        compressedUrl.value = null;
    }
    compressedBlob.value = null;
};
</script>
