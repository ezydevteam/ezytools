<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">

        <div v-if="!selectedFile" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop" :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-primary-400': dragover }">
            <input type="file" @change="handleFileSelect" accept="image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-teal-100 to-cyan-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:from-teal-900/50 dark:to-cyan-900/50 dark:text-teal-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Drop your WebP image here</h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">Click or drag a WebP file to convert</p>
        </div>

        <div v-else class="space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center overflow-hidden">
                        <img :src="originalPreview" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white">{{ selectedFile.name }}</h3>
                        <p class="text-xs text-surface-500">{{ formatBytes(selectedFile.size) }} • WebP</p>
                    </div>
                </div>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">Start Over</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Convert To</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="setFormat('image/jpeg')" :class="[targetFormat === 'image/jpeg' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-4 rounded-xl border text-center transition-all">
                                <span class="text-lg font-black">JPG</span>
                                <p class="text-xs text-surface-500 mt-1">Best for photos</p>
                            </button>
                            <button @click="setFormat('image/png')" :class="[targetFormat === 'image/png' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-4 rounded-xl border text-center transition-all">
                                <span class="text-lg font-black">PNG</span>
                                <p class="text-xs text-surface-500 mt-1">Supports transparency</p>
                            </button>
                        </div>
                    </div>

                    <!-- Conversion Flow Visual -->
                    <div class="flex items-center justify-center gap-4 p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-14 h-14 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                <span class="text-sm font-black text-teal-600 dark:text-teal-400">WEBP</span>
                            </div>
                            <span class="text-[10px] text-surface-500">{{ formatBytes(selectedFile.size) }}</span>
                        </div>
                        <svg class="w-6 h-6 text-primary-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center" :class="targetFormat === 'image/jpeg' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-blue-100 dark:bg-blue-900/30'">
                                <span class="text-sm font-black" :class="targetFormat === 'image/jpeg' ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400'">{{ targetFormat === 'image/jpeg' ? 'JPG' : 'PNG' }}</span>
                            </div>
                            <span class="text-[10px] text-surface-500">{{ convertedSize ? formatBytes(convertedSize) : '...' }}</span>
                        </div>
                    </div>

                    <div v-if="targetFormat === 'image/jpeg'">
                        <div class="flex justify-between mb-2">
                            <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Quality</label>
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ quality }}%</span>
                        </div>
                        <input type="range" v-model="quality" min="10" max="100" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-primary-600" @change="convertImage">
                    </div>

                    <button @click="downloadConverted" :disabled="isConverting" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg v-if="isConverting" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download {{ targetFormat === 'image/jpeg' ? 'JPG' : 'PNG' }}
                    </button>
                </div>

                <div class="flex flex-col h-full">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                    <div class="flex-1 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden relative flex items-center justify-center min-h-[250px]">
                        <div class="absolute inset-0 z-0 opacity-10" style="background-image: linear-gradient(45deg, #808080 25%, transparent 25%), linear-gradient(-45deg, #808080 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #808080 75%), linear-gradient(-45deg, transparent 75%, #808080 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;"></div>
                        <img v-if="convertedUrl" :src="convertedUrl" class="max-w-full max-h-[300px] object-contain z-10" alt="Converted Preview">
                        <div v-if="isConverting" class="absolute inset-0 bg-white/50 dark:bg-surface-800/50 flex items-center justify-center backdrop-blur-sm z-20">
                            <svg class="animate-spin h-8 w-8 text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const dragover = ref(false);
const selectedFile = ref(null);
const originalPreview = ref(null);
const targetFormat = ref('image/jpeg');
const quality = ref(90);
const convertedUrl = ref(null);
const convertedBlob = ref(null);
const convertedSize = ref(0);
const isConverting = ref(false);

const handleDrop = (e) => {
    dragover.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'image/webp') processFile(file);
    else alert('Please drop a valid WebP file.');
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

const setFormat = (format) => {
    targetFormat.value = format;
    if (selectedFile.value) convertImage();
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

        if (targetFormat.value === 'image/jpeg') {
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        ctx.drawImage(img, 0, 0);

        const q = targetFormat.value === 'image/jpeg' ? quality.value / 100 : undefined;
        canvas.toBlob((blob) => {
            convertedBlob.value = blob;
            convertedSize.value = blob.size;
            if (convertedUrl.value) URL.revokeObjectURL(convertedUrl.value);
            convertedUrl.value = URL.createObjectURL(blob);
            isConverting.value = false;
        }, targetFormat.value, q);
    };
};

const downloadConverted = () => {
    if (!convertedBlob.value) return;
    const url = URL.createObjectURL(convertedBlob.value);
    const a = document.createElement('a');
    a.href = url;
    const nameWithoutExt = selectedFile.value.name.split('.').slice(0, -1).join('.');
    const ext = targetFormat.value === 'image/jpeg' ? 'jpg' : 'png';
    a.download = `${nameWithoutExt}.${ext}`;
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
