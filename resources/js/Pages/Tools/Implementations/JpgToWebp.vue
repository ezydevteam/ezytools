<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">

        <div v-if="!selectedFile" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop" :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-primary-400': dragover }">
            <input type="file" @change="handleFileSelect" accept="image/jpeg,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:from-green-900/50 dark:to-emerald-900/50 dark:text-green-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Drop your JPG image here</h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">Click or drag a JPEG/JPG file to convert to WebP</p>
        </div>

        <div v-else class="space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center overflow-hidden">
                        <img :src="originalPreview" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white">{{ selectedFile.name }}</h3>
                        <p class="text-xs text-surface-500">{{ formatBytes(selectedFile.size) }} • JPG</p>
                    </div>
                </div>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">Start Over</button>
            </div>

            <!-- Conversion Flow Visual -->
            <div class="flex items-center justify-center gap-4 p-6 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <span class="text-lg font-black text-red-600 dark:text-red-400">JPG</span>
                    </div>
                    <span class="text-xs text-surface-500">{{ formatBytes(selectedFile.size) }}</span>
                </div>
                <svg class="w-8 h-8 text-green-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <span class="text-lg font-black text-green-600 dark:text-green-400">WEBP</span>
                    </div>
                    <span class="text-xs text-surface-500">{{ convertedSize ? formatBytes(convertedSize) : '...' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between mb-2">
                            <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Quality</label>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ quality }}%</span>
                        </div>
                        <input type="range" v-model="quality" min="10" max="100" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-green-600" @change="convertImage">
                        <div class="flex justify-between text-xs text-surface-500 mt-1">
                            <span>Smallest File</span>
                            <span>Best Quality</span>
                        </div>
                    </div>

                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                        <h4 class="text-sm font-bold text-green-800 dark:text-green-300 mb-2">🚀 Why WebP?</h4>
                        <ul class="text-xs text-green-700 dark:text-green-400 space-y-1">
                            <li>• 25-35% smaller than JPEG at same quality</li>
                            <li>• Faster web page load times</li>
                            <li>• Supported by all modern browsers</li>
                        </ul>
                    </div>

                    <div v-if="convertedSize && selectedFile" class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-surface-600 dark:text-surface-400">Space Saved:</span>
                            <span class="font-black text-lg" :class="savedPercentage > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500'">{{ savedPercentage }}%</span>
                        </div>
                    </div>

                    <button @click="downloadConverted" :disabled="isConverting" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg v-if="isConverting" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download WebP
                    </button>
                </div>

                <div class="flex flex-col h-full">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                    <div class="flex-1 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden relative flex items-center justify-center min-h-[250px]">
                        <img v-if="convertedUrl" :src="convertedUrl" class="max-w-full max-h-[300px] object-contain z-10" alt="Converted Preview">
                        <div v-if="isConverting" class="absolute inset-0 bg-white/50 dark:bg-surface-800/50 flex items-center justify-center backdrop-blur-sm z-20">
                            <svg class="animate-spin h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
const originalPreview = ref(null);
const convertedUrl = ref(null);
const convertedBlob = ref(null);
const convertedSize = ref(0);
const quality = ref(85);
const isConverting = ref(false);

const savedPercentage = computed(() => {
    if (!selectedFile.value || !convertedSize.value) return 0;
    const saved = selectedFile.value.size - convertedSize.value;
    return Math.round((saved / selectedFile.value.size) * 100);
});

const handleDrop = (e) => {
    dragover.value = false;
    const file = e.dataTransfer.files[0];
    if (file && (file.type === 'image/jpeg' || file.type === 'image/jpg')) processFile(file);
    else alert('Please drop a valid JPG/JPEG file.');
};

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file) processFile(file);
};

const processFile = (file) => {
    selectedFile.value = file;
    if (originalPreview.value) URL.revokeObjectURL(originalPreview.value);
    originalPreview.value = URL.createObjectURL(file);
    convertImage();
};

const convertImage = () => {
    isConverting.value = true;
    const img = new Image();
    img.src = originalPreview.value;
    img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);

        canvas.toBlob((blob) => {
            convertedBlob.value = blob;
            convertedSize.value = blob.size;
            if (convertedUrl.value) URL.revokeObjectURL(convertedUrl.value);
            convertedUrl.value = URL.createObjectURL(blob);
            isConverting.value = false;
        }, 'image/webp', quality.value / 100);
    };
};

const downloadConverted = () => {
    if (!convertedBlob.value) return;
    const url = URL.createObjectURL(convertedBlob.value);
    const a = document.createElement('a');
    a.href = url;
    const nameWithoutExt = selectedFile.value.name.split('.').slice(0, -1).join('.');
    a.download = `${nameWithoutExt}.webp`;
    a.click();
    URL.revokeObjectURL(url);
};

const formatBytes = (bytes, decimals = 2) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
};

const resetTool = () => {
    selectedFile.value = null;
    if (originalPreview.value) URL.revokeObjectURL(originalPreview.value);
    if (convertedUrl.value) URL.revokeObjectURL(convertedUrl.value);
    originalPreview.value = null;
    convertedUrl.value = null;
    convertedBlob.value = null;
    convertedSize.value = 0;
};
</script>
