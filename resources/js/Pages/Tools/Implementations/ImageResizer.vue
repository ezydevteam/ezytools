<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div v-if="!selectedFile" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop" :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-primary-400': dragover }">
            <input type="file" @change="handleFileSelect" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-primary-900/50 dark:text-primary-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Click or drag image to resize</h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">Supports JPG, PNG, WEBP</p>
        </div>

        <div v-else class="space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white">Resize Dimensions</h3>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">Start Over</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Settings Area -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Width (px)</label>
                            <input type="number" v-model.number="width" @input="updateHeight" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Height (px)</label>
                            <input type="number" v-model.number="height" @input="updateWidth" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer mt-2">
                            <input type="checkbox" v-model="maintainAspectRatio" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                            <span class="text-sm text-surface-700 dark:text-surface-300">Maintain Aspect Ratio</span>
                        </label>
                    </div>
                    
                    <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-surface-600 dark:text-surface-400">Original Dimensions:</span>
                            <span class="font-medium text-surface-900 dark:text-white">{{ originalWidth }} x {{ originalHeight }}</span>
                        </div>
                    </div>

                    <button @click="resizeAndDownload" :disabled="isProcessing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download Resized Image
                    </button>
                </div>

                <!-- Preview Area -->
                <div class="flex flex-col h-full">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Original Preview</label>
                    <div class="flex-1 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden relative flex items-center justify-center">
                        <img v-if="originalPreview" :src="originalPreview" class="max-w-full max-h-[300px] object-contain" alt="Original Preview">
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
const originalPreview = ref(null);
const originalWidth = ref(0);
const originalHeight = ref(0);
const width = ref(0);
const height = ref(0);
const maintainAspectRatio = ref(true);
const aspectRatio = ref(1);
const isProcessing = ref(false);

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
    
    if (originalPreview.value) URL.revokeObjectURL(originalPreview.value);
    originalPreview.value = URL.createObjectURL(file);
    
    const img = new Image();
    img.src = originalPreview.value;
    img.onload = () => {
        originalWidth.value = img.width;
        originalHeight.value = img.height;
        width.value = img.width;
        height.value = img.height;
        aspectRatio.value = img.width / img.height;
    };
};

const updateHeight = () => {
    if (maintainAspectRatio.value) {
        height.value = Math.round(width.value / aspectRatio.value);
    }
};

const updateWidth = () => {
    if (maintainAspectRatio.value) {
        width.value = Math.round(height.value * aspectRatio.value);
    }
};

watch(maintainAspectRatio, (val) => {
    if (val) updateHeight();
});

const resizeAndDownload = () => {
    if (!selectedFile.value) return;
    
    isProcessing.value = true;
    
    const img = new Image();
    img.src = originalPreview.value;
    img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = width.value;
        canvas.height = height.value;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            const ext = selectedFile.value.name.split('.').pop();
            const nameWithoutExt = selectedFile.value.name.replace(`.${ext}`, '');
            
            a.download = `${nameWithoutExt}-${width.value}x${height.value}.${ext}`;
            a.click();
            URL.revokeObjectURL(url);
            isProcessing.value = false;
        }, selectedFile.value.type);
    };
};

const resetTool = () => {
    selectedFile.value = null;
    if (originalPreview.value) {
        URL.revokeObjectURL(originalPreview.value);
        originalPreview.value = null;
    }
    width.value = 0;
    height.value = 0;
};
</script>
