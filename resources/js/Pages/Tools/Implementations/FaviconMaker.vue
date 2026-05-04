<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">

        <div v-if="!selectedFile" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop" :class="{ 'bg-primary-50 dark:bg-primary-900/20 border-primary-400': dragover }">
            <input type="file" @change="handleFileSelect" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-primary-100 to-purple-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:from-primary-900/50 dark:to-purple-900/50 dark:text-primary-400">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload image to create Favicon</h3>
            <p class="text-sm text-surface-500 dark:text-surface-400">Supports JPG, PNG, WEBP, SVG</p>
        </div>

        <div v-else class="space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-surface-100 dark:bg-surface-700 flex items-center justify-center overflow-hidden">
                        <img :src="originalPreview" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900 dark:text-white">{{ selectedFile.name }}</h3>
                        <p class="text-xs text-surface-500">{{ formatBytes(selectedFile.size) }}</p>
                    </div>
                </div>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium transition-colors">Start Over</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Settings -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Favicon Sizes</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button v-for="s in availableSizes" :key="s" @click="toggleSize(s)"
                                :class="[selectedSizes.includes(s) ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']"
                                class="p-3 rounded-xl border text-center transition-all font-bold text-sm">
                                {{ s }}×{{ s }}
                            </button>
                        </div>

                        <!-- Custom Size -->
                        <div class="mt-3 flex items-center gap-2">
                            <input type="number" v-model.number="customSizeInput" min="8" max="512" placeholder="Custom size" class="flex-1 text-sm py-2 px-3 rounded-xl border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 text-surface-700 dark:text-surface-300">
                            <button @click="addCustomSize" class="px-4 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                                Add
                            </button>
                        </div>
                        <p v-if="customSizeError" class="text-xs text-red-500 mt-1">{{ customSizeError }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Output Format</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="outputFormat = 'png'" :class="[outputFormat === 'png' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-3 rounded-xl border text-center transition-all font-bold text-sm">PNG</button>
                            <button @click="outputFormat = 'ico'" :class="[outputFormat === 'ico' ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400 ring-1 ring-primary-500' : 'bg-surface-50 dark:bg-surface-900 border-surface-200 dark:border-surface-700 text-surface-700 dark:text-surface-300 hover:border-primary-300']" class="p-3 rounded-xl border text-center transition-all font-bold text-sm">ICO</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">Background</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="bgMode" value="transparent" class="accent-primary-600">
                                <span class="text-sm text-surface-700 dark:text-surface-300">Transparent</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" v-model="bgMode" value="color" class="accent-primary-600">
                                <span class="text-sm text-surface-700 dark:text-surface-300">Color</span>
                            </label>
                            <input v-if="bgMode === 'color'" type="color" v-model="bgColor" class="w-8 h-8 rounded cursor-pointer border-0 p-0">
                        </div>
                    </div>

                    <button @click="downloadZip" :disabled="isProcessing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <svg v-if="isProcessing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download as ZIP
                    </button>
                </div>

                <!-- Preview -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Live Preview</label>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="s in sortedSizes" :key="s" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 p-4 flex flex-col items-center justify-center gap-3 relative group">
                            <div class="relative" :style="{ width: Math.min(s, 128) + 'px', height: Math.min(s, 128) + 'px' }">
                                <div class="absolute inset-0 rounded-lg" style="background-image: linear-gradient(45deg, #ccc 25%, transparent 25%), linear-gradient(-45deg, #ccc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #ccc 75%), linear-gradient(-45deg, transparent 75%, #ccc 75%); background-size: 10px 10px; background-position: 0 0, 0 5px, 5px -5px, -5px 0px;"></div>
                                <canvas :ref="el => { if (el) previewRefs[s] = el }" :width="s" :height="s" :style="{ width: Math.min(s, 128) + 'px', height: Math.min(s, 128) + 'px' }" class="relative z-10 rounded-lg"></canvas>
                            </div>
                            <span class="text-xs font-bold text-surface-500 dark:text-surface-400">{{ s }}×{{ s }}px</span>
                            <!-- Remove custom size -->
                            <button v-if="!availableSizes.includes(s)" @click="removeCustomSize(s)" class="absolute top-1 right-1 p-1 bg-red-500 hover:bg-red-600 text-white rounded-md opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, nextTick, computed } from 'vue';

const dragover = ref(false);
const selectedFile = ref(null);
const originalPreview = ref(null);
const availableSizes = [16, 32, 48, 64, 128, 256];
const selectedSizes = ref([16, 32, 48]);
const customSizeInput = ref(null);
const customSizeError = ref('');
const outputFormat = ref('png');
const bgMode = ref('transparent');
const bgColor = ref('#ffffff');
const isProcessing = ref(false);
const previewRefs = ref({});
const loadedImage = ref(null);

const sortedSizes = computed(() => [...selectedSizes.value].sort((a, b) => a - b));

const handleDrop = (e) => {
    dragover.value = false;
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) processFile(file);
};

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (file) processFile(file);
};

const processFile = (file) => {
    selectedFile.value = file;
    if (originalPreview.value) URL.revokeObjectURL(originalPreview.value);
    originalPreview.value = URL.createObjectURL(file);

    const img = new Image();
    img.src = originalPreview.value;
    img.onload = () => {
        loadedImage.value = img;
        nextTick(() => renderPreviews());
    };
};

const addCustomSize = () => {
    customSizeError.value = '';
    const s = customSizeInput.value;
    if (!s || s < 8 || s > 512) {
        customSizeError.value = 'Size must be between 8 and 512';
        return;
    }
    if (selectedSizes.value.includes(s)) {
        customSizeError.value = 'This size already exists';
        return;
    }
    selectedSizes.value.push(s);
    customSizeInput.value = null;
    nextTick(() => renderPreviews());
};

const removeCustomSize = (s) => {
    const idx = selectedSizes.value.indexOf(s);
    if (idx > -1 && selectedSizes.value.length > 1) {
        selectedSizes.value.splice(idx, 1);
    }
};

const toggleSize = (s) => {
    const idx = selectedSizes.value.indexOf(s);
    if (idx > -1) { if (selectedSizes.value.length > 1) selectedSizes.value.splice(idx, 1); }
    else selectedSizes.value.push(s);
    nextTick(() => renderPreviews());
};

const renderPreviews = () => {
    if (!loadedImage.value) return;
    const img = loadedImage.value;

    selectedSizes.value.forEach(s => {
        nextTick(() => {
            const canvas = previewRefs.value[s];
            if (!canvas) return;
            canvas.width = s;
            canvas.height = s;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, s, s);

            if (bgMode.value === 'color') {
                ctx.fillStyle = bgColor.value;
                ctx.fillRect(0, 0, s, s);
            }

            // Fit image into square, preserving aspect ratio
            const aspect = img.width / img.height;
            let dw = s, dh = s, dx = 0, dy = 0;
            if (aspect > 1) { dh = s / aspect; dy = (s - dh) / 2; }
            else { dw = s * aspect; dx = (s - dw) / 2; }

            ctx.drawImage(img, dx, dy, dw, dh);
        });
    });
};

watch([bgMode, bgColor], () => nextTick(() => renderPreviews()));

const generateCanvas = (size) => {
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');

    if (bgMode.value === 'color') {
        ctx.fillStyle = bgColor.value;
        ctx.fillRect(0, 0, size, size);
    }

    const img = loadedImage.value;
    const aspect = img.width / img.height;
    let dw = size, dh = size, dx = 0, dy = 0;
    if (aspect > 1) { dh = size / aspect; dy = (size - dh) / 2; }
    else { dw = size * aspect; dx = (size - dw) / 2; }
    ctx.drawImage(img, dx, dy, dw, dh);

    return canvas;
};

const createIco = (pngData, size) => {
    const headerSize = 6;
    const entrySize = 16;
    const dataOffset = headerSize + entrySize;
    const buffer = new ArrayBuffer(dataOffset + pngData.length);
    const view = new DataView(buffer);

    view.setUint16(0, 0, true);
    view.setUint16(2, 1, true);
    view.setUint16(4, 1, true);

    view.setUint8(6, size >= 256 ? 0 : size);
    view.setUint8(7, size >= 256 ? 0 : size);
    view.setUint8(8, 0);
    view.setUint8(9, 0);
    view.setUint16(10, 1, true);
    view.setUint16(12, 32, true);
    view.setUint32(14, pngData.length, true);
    view.setUint32(18, dataOffset, true);

    const arr = new Uint8Array(buffer);
    arr.set(pngData, dataOffset);
    return buffer;
};

// Minimal ZIP builder (no dependencies)
const buildZip = (files) => {
    // files: [{ name: string, data: Uint8Array }]
    const localHeaders = [];
    const centralHeaders = [];
    let offset = 0;

    for (const file of files) {
        const nameBytes = new TextEncoder().encode(file.name);

        // Local file header
        const local = new ArrayBuffer(30 + nameBytes.length + file.data.length);
        const lv = new DataView(local);
        lv.setUint32(0, 0x04034b50, true); // signature
        lv.setUint16(4, 20, true); // version needed
        lv.setUint16(6, 0, true); // flags
        lv.setUint16(8, 0, true); // compression (store)
        lv.setUint16(10, 0, true); // mod time
        lv.setUint16(12, 0, true); // mod date
        lv.setUint32(14, crc32(file.data), true); // crc32
        lv.setUint32(18, file.data.length, true); // compressed size
        lv.setUint32(22, file.data.length, true); // uncompressed size
        lv.setUint16(26, nameBytes.length, true); // name length
        lv.setUint16(28, 0, true); // extra length

        const localArr = new Uint8Array(local);
        localArr.set(nameBytes, 30);
        localArr.set(file.data, 30 + nameBytes.length);
        localHeaders.push(localArr);

        // Central directory header
        const central = new ArrayBuffer(46 + nameBytes.length);
        const cv = new DataView(central);
        cv.setUint32(0, 0x02014b50, true);
        cv.setUint16(4, 20, true);
        cv.setUint16(6, 20, true);
        cv.setUint16(8, 0, true);
        cv.setUint16(10, 0, true);
        cv.setUint16(12, 0, true);
        cv.setUint16(14, 0, true);
        cv.setUint32(16, crc32(file.data), true);
        cv.setUint32(20, file.data.length, true);
        cv.setUint32(24, file.data.length, true);
        cv.setUint16(28, nameBytes.length, true);
        cv.setUint16(30, 0, true);
        cv.setUint16(32, 0, true);
        cv.setUint16(34, 0, true);
        cv.setUint16(36, 0, true);
        cv.setUint32(38, 0, true);
        cv.setUint32(42, offset, true);

        const centralArr = new Uint8Array(central);
        centralArr.set(nameBytes, 46);
        centralHeaders.push(centralArr);

        offset += localArr.length;
    }

    const centralOffset = offset;
    let centralSize = 0;
    centralHeaders.forEach(h => centralSize += h.length);

    // End of central directory
    const eocd = new ArrayBuffer(22);
    const ev = new DataView(eocd);
    ev.setUint32(0, 0x06054b50, true);
    ev.setUint16(4, 0, true);
    ev.setUint16(6, 0, true);
    ev.setUint16(8, files.length, true);
    ev.setUint16(10, files.length, true);
    ev.setUint32(12, centralSize, true);
    ev.setUint32(16, centralOffset, true);
    ev.setUint16(20, 0, true);

    const totalSize = offset + centralSize + 22;
    const result = new Uint8Array(totalSize);
    let pos = 0;
    for (const h of localHeaders) { result.set(h, pos); pos += h.length; }
    for (const h of centralHeaders) { result.set(h, pos); pos += h.length; }
    result.set(new Uint8Array(eocd), pos);

    return result;
};

const crc32 = (data) => {
    let crc = 0xFFFFFFFF;
    for (let i = 0; i < data.length; i++) {
        crc ^= data[i];
        for (let j = 0; j < 8; j++) {
            crc = (crc >>> 1) ^ (crc & 1 ? 0xEDB88320 : 0);
        }
    }
    return (crc ^ 0xFFFFFFFF) >>> 0;
};

const downloadZip = async () => {
    isProcessing.value = true;

    const files = [];
    const ext = outputFormat.value === 'ico' ? 'ico' : 'png';

    for (const size of sortedSizes.value) {
        const canvas = generateCanvas(size);
        const pngBlob = await new Promise(r => canvas.toBlob(r, 'image/png'));
        const pngBuffer = await pngBlob.arrayBuffer();

        if (outputFormat.value === 'ico') {
            const icoBuffer = createIco(new Uint8Array(pngBuffer), size);
            files.push({ name: `favicon-${size}x${size}.${ext}`, data: new Uint8Array(icoBuffer) });
        } else {
            files.push({ name: `favicon-${size}x${size}.${ext}`, data: new Uint8Array(pngBuffer) });
        }
    }

    const zipData = buildZip(files);
    const blob = new Blob([zipData], { type: 'application/zip' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'favicons.zip';
    a.click();
    URL.revokeObjectURL(url);

    isProcessing.value = false;
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
    loadedImage.value = null;
    previewRefs.value = {};
    selectedSizes.value = [16, 32, 48];
    customSizeInput.value = null;
    customSizeError.value = '';
};
</script>
