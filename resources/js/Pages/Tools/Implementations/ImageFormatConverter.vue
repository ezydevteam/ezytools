<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div v-if="!selectedFile" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop" :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-primary-400': dragover }">
            <input type="file" @change="handleFileSelect" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-primary-900/50 dark:text-primary-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            </div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Click or drag image to convert</h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">Supports JPG, PNG, WEBP, GIF</p>
        </div>

        <div v-else class="space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center overflow-hidden">
                        <img :src="originalPreview" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white">{{ selectedFile.name }}</h3>
                        <p class="text-xs text-surface-500">{{ formatBytes(selectedFile.size) }} • {{ selectedFile.type.split('/')[1].toUpperCase() }}</p>
                    </div>
                </div>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">Start Over</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Settings Area -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Convert To Format</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="targetFormat = 'image/jpeg'" :class="[targetFormat === 'image/jpeg' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-4 rounded-xl border text-center transition-all font-bold tracking-wide">
                                JPG / JPEG
                            </button>
                            <button @click="targetFormat = 'image/png'" :class="[targetFormat === 'image/png' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-4 rounded-xl border text-center transition-all font-bold tracking-wide">
                                PNG
                            </button>
                            <button @click="targetFormat = 'image/webp'" :class="[targetFormat === 'image/webp' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-4 rounded-xl border text-center transition-all font-bold tracking-wide">
                                WEBP
                            </button>
                            <button @click="targetFormat = 'image/gif'" :class="[targetFormat === 'image/gif' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-4 rounded-xl border text-center transition-all font-bold tracking-wide">
                                GIF
                            </button>
                        </div>
                    </div>

                    <div v-if="targetFormat === 'image/jpeg' || targetFormat === 'image/webp'" class="pt-4 border-t border-surface-200 dark:border-surface-700">
                        <div class="flex justify-between mb-2">
                            <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Quality</label>
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ quality }}%</span>
                        </div>
                        <input type="range" v-model="quality" min="10" max="100" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-primary-600" @change="convertImage">
                    </div>

                    <button @click="convertAndDownload" :disabled="isConverting" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg v-if="isConverting" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Convert & Download
                    </button>
                </div>

                <!-- Preview Area -->
                <div class="flex flex-col h-full">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                    <div class="flex-1 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden relative flex items-center justify-center min-h-[250px]">
                        <!-- Checkered background for transparency preview -->
                        <div class="absolute inset-0 z-0 opacity-10" style="background-image: linear-gradient(45deg, #808080 25%, transparent 25%), linear-gradient(-45deg, #808080 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #808080 75%), linear-gradient(-45deg, transparent 75%, #808080 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;"></div>
                        
                        <img v-if="convertedUrl" :src="convertedUrl" class="max-w-full max-h-[300px] object-contain z-10" alt="Converted Preview">
                        
                        <div v-if="isConverting" class="absolute inset-0 bg-white/50 dark:bg-surface-800/50 flex items-center justify-center backdrop-blur-sm z-20">
                            <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const dragover = ref(false);
const selectedFile = ref(null);
const targetFormat = ref('image/webp');
const quality = ref(90);
const originalPreview = ref(null);
const convertedUrl = ref(null);
const convertedBlob = ref(null);
const isConverting = ref(false);

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
    
    // Create original preview
    if (originalPreview.value) URL.revokeObjectURL(originalPreview.value);
    originalPreview.value = URL.createObjectURL(file);
    
    // Set default target format based on input
    if (file.type === 'image/jpeg') targetFormat.value = 'image/png';
    else if (file.type === 'image/png') targetFormat.value = 'image/jpeg';
    else targetFormat.value = 'image/webp';
    
    convertImage();
};

watch(targetFormat, () => {
    if (selectedFile.value) convertImage();
});

const convertImage = () => {
    if (!selectedFile.value) return;
    
    isConverting.value = true;
    
    const img = new Image();
    img.src = originalPreview.value;
    img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        
        const ctx = canvas.getContext('2d');
        
        // If converting to JPEG, fill with white background first (since JPEG doesn't support transparency)
        if (targetFormat.value === 'image/jpeg') {
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
        
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob((blob) => {
            convertedBlob.value = blob;
            
            if (convertedUrl.value) {
                URL.revokeObjectURL(convertedUrl.value);
            }
            convertedUrl.value = URL.createObjectURL(blob);
            isConverting.value = false;
        }, targetFormat.value, quality.value / 100);
    };
};

const formatBytes = (bytes, decimals = 2) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
};

const convertAndDownload = () => {
    if (!convertedBlob.value) {
        convertImage();
        setTimeout(convertAndDownload, 500); // Wait for conversion if not ready
        return;
    }
    
    const url = URL.createObjectURL(convertedBlob.value);
    const a = document.createElement('a');
    a.href = url;
    
    // Determine new extension
    let ext = 'jpg';
    if (targetFormat.value === 'image/png') ext = 'png';
    if (targetFormat.value === 'image/webp') ext = 'webp';
    if (targetFormat.value === 'image/gif') ext = 'gif';
    
    const nameWithoutExt = selectedFile.value.name.split('.').slice(0, -1).join('.');
    a.download = `${nameWithoutExt}-converted.${ext}`;
    a.click();
    URL.revokeObjectURL(url);
};

const resetTool = () => {
    selectedFile.value = null;
    if (originalPreview.value) URL.revokeObjectURL(originalPreview.value);
    if (convertedUrl.value) URL.revokeObjectURL(convertedUrl.value);
    originalPreview.value = null;
    convertedUrl.value = null;
    convertedBlob.value = null;
};
</script>
