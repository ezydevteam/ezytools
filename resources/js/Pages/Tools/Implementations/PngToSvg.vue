<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">

        <div v-if="!selectedFile" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop" :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-primary-400': dragover }">
            <input type="file" @change="handleFileSelect" accept="image/png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-fuchsia-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:from-purple-900/50 dark:to-fuchsia-900/50 dark:text-purple-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Drop your PNG image here</h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">Click or drag a PNG file to convert to SVG</p>
        </div>

        <div v-else class="space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center overflow-hidden">
                        <img :src="originalPreview" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white">{{ selectedFile.name }}</h3>
                        <p class="text-xs text-surface-500">{{ formatBytes(selectedFile.size) }} • PNG • {{ imgWidth }}×{{ imgHeight }}px</p>
                    </div>
                </div>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">Start Over</button>
            </div>

            <!-- Conversion Flow Visual -->
            <div class="flex items-center justify-center gap-4 p-6 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <span class="text-lg font-black text-blue-600 dark:text-blue-400">PNG</span>
                    </div>
                    <span class="text-xs text-surface-500">Raster</span>
                </div>
                <svg class="w-8 h-8 text-purple-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-16 h-16 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <span class="text-lg font-black text-purple-600 dark:text-purple-400">SVG</span>
                    </div>
                    <span class="text-xs text-surface-500">Vector</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Conversion Mode</label>
                        <div class="space-y-3">
                            <button @click="conversionMode = 'embed'" :class="[conversionMode === 'embed' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 hover:border-primary-300']" class="w-full p-4 rounded-xl border text-left transition-all">
                                <h4 class="text-sm font-bold text-surface-900 dark:text-white">Embed (Recommended)</h4>
                                <p class="text-xs text-surface-500 mt-1">Wraps the PNG inside an SVG container. Preserves exact quality.</p>
                            </button>
                            <button @click="conversionMode = 'trace'" :class="[conversionMode === 'trace' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 hover:border-primary-300']" class="w-full p-4 rounded-xl border text-left transition-all">
                                <h4 class="text-sm font-bold text-surface-900 dark:text-white">Trace (Simplified)</h4>
                                <p class="text-xs text-surface-500 mt-1">Converts to high-contrast monochrome vector paths. Best for logos and icons.</p>
                            </button>
                        </div>
                    </div>

                    <div v-if="conversionMode === 'trace'" class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-2">
                                <label class="text-sm font-medium text-surface-700 dark:text-surface-300">Threshold</label>
                                <span class="text-sm font-bold text-purple-600 dark:text-purple-400">{{ threshold }}</span>
                            </div>
                            <input type="range" v-model="threshold" min="0" max="255" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-purple-600" @change="convertImage">
                        </div>
                    </div>

                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                        <h4 class="text-sm font-bold text-purple-800 dark:text-purple-300 mb-2">📐 Why SVG?</h4>
                        <ul class="text-xs text-purple-700 dark:text-purple-400 space-y-1">
                            <li>• Infinitely scalable without quality loss</li>
                            <li>• Editable with code or design tools</li>
                            <li>• Perfect for logos, icons, and illustrations</li>
                        </ul>
                    </div>

                    <button @click="downloadConverted" :disabled="isConverting" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg v-if="isConverting" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download SVG
                    </button>
                </div>

                <div class="flex flex-col h-full">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                    <div class="flex-1 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden relative flex items-center justify-center min-h-[250px]">
                        <div class="absolute inset-0 z-0 opacity-10" style="background-image: linear-gradient(45deg, #808080 25%, transparent 25%), linear-gradient(-45deg, #808080 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #808080 75%), linear-gradient(-45deg, transparent 75%, #808080 75%); background-size: 20px 20px; background-position: 0 0, 0 10px, 10px -10px, -10px 0px;"></div>
                        <div v-if="svgPreview" v-html="svgPreview" class="max-w-full max-h-[300px] z-10 flex items-center justify-center [&>svg]:max-w-full [&>svg]:max-h-[300px]"></div>
                        <div v-if="isConverting" class="absolute inset-0 bg-white/50 dark:bg-surface-800/50 flex items-center justify-center backdrop-blur-sm z-20">
                            <svg class="animate-spin h-8 w-8 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
const originalPreview = ref(null);
const svgContent = ref('');
const svgPreview = ref('');
const imgWidth = ref(0);
const imgHeight = ref(0);
const isConverting = ref(false);
const conversionMode = ref('embed');
const threshold = ref(128);

const handleDrop = (e) => {
    dragover.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'image/png') processFile(file);
    else alert('Please drop a valid PNG file.');
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

watch(conversionMode, () => {
    if (selectedFile.value) convertImage();
});

const convertImage = () => {
    isConverting.value = true;
    const reader = new FileReader();
    reader.readAsDataURL(selectedFile.value);
    reader.onload = (event) => {
        const img = new Image();
        img.src = event.target.result;
        img.onload = () => {
            imgWidth.value = img.width;
            imgHeight.value = img.height;

            if (conversionMode.value === 'embed') {
                // Embed mode: wrap PNG data inside SVG
                const svg = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="${img.width}" height="${img.height}" viewBox="0 0 ${img.width} ${img.height}">
  <image width="${img.width}" height="${img.height}" xlink:href="${event.target.result}" />
</svg>`;
                svgContent.value = svg;
                svgPreview.value = svg;
                isConverting.value = false;
            } else {
                // Trace mode: convert to monochrome paths
                traceImage(img);
            }
        };
    };
};

const traceImage = (img) => {
    const canvas = document.createElement('canvas');
    canvas.width = img.width;
    canvas.height = img.height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    const w = canvas.width;
    const h = canvas.height;
    const t = parseInt(threshold.value);

    // Create binary grid
    let pathData = '';
    for (let y = 0; y < h; y++) {
        let inRun = false;
        let runStart = 0;
        for (let x = 0; x <= w; x++) {
            const idx = (y * w + x) * 4;
            const isDark = x < w && ((data[idx] + data[idx + 1] + data[idx + 2]) / 3) < t && data[idx + 3] > 128;

            if (isDark && !inRun) {
                inRun = true;
                runStart = x;
            } else if (!isDark && inRun) {
                inRun = false;
                pathData += `M${runStart},${y}h${x - runStart}v1h-${x - runStart}z `;
            }
        }
    }

    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${w}" height="${h}" viewBox="0 0 ${w} ${h}">
  <path d="${pathData}" fill="#000000" />
</svg>`;

    svgContent.value = svg;
    svgPreview.value = svg;
    isConverting.value = false;
};

const downloadConverted = () => {
    if (!svgContent.value) return;
    const blob = new Blob([svgContent.value], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    const nameWithoutExt = selectedFile.value.name.split('.').slice(0, -1).join('.');
    a.download = `${nameWithoutExt}.svg`;
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
    originalPreview.value = null;
    svgContent.value = '';
    svgPreview.value = '';
    imgWidth.value = 0;
    imgHeight.value = 0;
};
</script>
