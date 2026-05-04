<template>
<div class="space-y-6">
    <div v-if="!imageSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-primary-900/50 dark:text-primary-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload image to remove background</h3>
        <p class="text-sm text-surface-500">Supports JPG, PNG, WebP (Max 10MB)</p>
    </div>

    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Background Remover</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Settings -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Detection Sensitivity</label>
                    <div class="flex justify-between mb-1"><span class="text-xs text-surface-500">Less</span><span class="text-xs font-bold text-primary-600">{{ threshold }}</span><span class="text-xs text-surface-500">More</span></div>
                    <input type="range" v-model.number="threshold" min="5" max="80" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-primary-600" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Edge Softness</label>
                    <input type="range" v-model.number="feather" min="0" max="5" step="0.5" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-primary-600" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Background Color</label>
                    <div class="flex gap-2">
                        <button v-for="bg in bgOptions" :key="bg.value" @click="bgColor=bg.value" class="w-10 h-10 rounded-lg border-2 transition-all" :class="bgColor===bg.value ? 'border-primary-500 ring-2 ring-primary-200' : 'border-surface-300 dark:border-surface-600'" :style="bg.style" :title="bg.label"></button>
                    </div>
                </div>
                <button @click="removeBackground" :disabled="processing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
                    <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    {{ processing ? 'Processing...' : '✨ Remove Background' }}
                </button>
            </div>
            <!-- Preview -->
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Original</label>
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden flex items-center justify-center p-2">
                    <img :src="imageSrc" class="max-w-full max-h-[280px] object-contain rounded" alt="Original" />
                </div>
            </div>
        </div>

        <div v-if="resultUrl" class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Result</label>
            <div class="rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden flex items-center justify-center p-4" :style="previewBgStyle">
                <img :src="resultUrl" class="max-w-full max-h-[400px] object-contain" alt="Result" />
            </div>
            <div class="flex gap-3">
                <a :href="resultUrl" download="bg-removed.png" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md text-center">⬇ Download PNG</a>
            </div>
        </div>
    </div>
    <canvas ref="canvas" class="hidden"></canvas>
</div>
</template>

<script setup>
import { ref, computed } from 'vue';

const drag = ref(false);
const imageSrc = ref(null);
const resultUrl = ref(null);
const processing = ref(false);
const threshold = ref(30);
const feather = ref(1);
const bgColor = ref('transparent');
const canvas = ref(null);
let imgEl = null;

const bgOptions = [
    { value: 'transparent', label: 'Transparent', style: 'background: repeating-conic-gradient(#d4d4d4 0% 25%, white 0% 50%) 50% / 16px 16px' },
    { value: '#ffffff', label: 'White', style: 'background: #ffffff' },
    { value: '#000000', label: 'Black', style: 'background: #000000' },
    { value: '#ff0000', label: 'Red', style: 'background: #ff0000' },
    { value: '#00cc44', label: 'Green', style: 'background: #00cc44' },
    { value: '#3366ff', label: 'Blue', style: 'background: #3366ff' },
];

const previewBgStyle = computed(() => {
    if (bgColor.value === 'transparent') return { background: 'repeating-conic-gradient(#e5e5e5 0% 25%, white 0% 50%) 50% / 20px 20px' };
    return { background: bgColor.value };
});

const onDrop = (e) => { drag.value = false; const f = e.dataTransfer.files[0]; if (f?.type.startsWith('image/')) loadFile(f); };
const onFile = (e) => { const f = e.target.files[0]; if (f) loadFile(f); };

const loadFile = (f) => {
    const reader = new FileReader();
    reader.onload = (ev) => {
        imageSrc.value = ev.target.result;
        imgEl = new Image();
        imgEl.src = ev.target.result;
        resultUrl.value = null;
    };
    reader.readAsDataURL(f);
};

const resetTool = () => { imageSrc.value = null; resultUrl.value = null; imgEl = null; };

const removeBackground = () => {
    if (!imgEl || processing.value) return;
    processing.value = true;
    resultUrl.value = null;

    // Wait for image to be loaded
    const process = () => {
        const cv = canvas.value;
        cv.width = imgEl.naturalWidth;
        cv.height = imgEl.naturalHeight;
        const ctx = cv.getContext('2d', { willReadFrequently: true });

        // Draw original image
        ctx.drawImage(imgEl, 0, 0);
        const imageData = ctx.getImageData(0, 0, cv.width, cv.height);
        const data = imageData.data;

        // Sample corners to detect background color
        const samples = [];
        const samplePositions = [
            [0, 0], [cv.width - 1, 0], [0, cv.height - 1], [cv.width - 1, cv.height - 1],
            [Math.floor(cv.width / 2), 0], [0, Math.floor(cv.height / 2)],
            [cv.width - 1, Math.floor(cv.height / 2)], [Math.floor(cv.width / 2), cv.height - 1],
        ];

        for (const [sx, sy] of samplePositions) {
            const idx = (sy * cv.width + sx) * 4;
            samples.push([data[idx], data[idx + 1], data[idx + 2]]);
        }

        // Find most common background color from samples
        const bgR = Math.round(samples.reduce((s, c) => s + c[0], 0) / samples.length);
        const bgG = Math.round(samples.reduce((s, c) => s + c[1], 0) / samples.length);
        const bgB = Math.round(samples.reduce((s, c) => s + c[2], 0) / samples.length);

        const th = threshold.value;

        // Flood-fill from edges to mark background pixels
        const w = cv.width, h = cv.height;
        const visited = new Uint8Array(w * h);
        const isBackground = new Uint8Array(w * h);
        const queue = [];

        // Seed from all edge pixels
        for (let x = 0; x < w; x++) { queue.push(x); queue.push(0); queue.push(x); queue.push(h - 1); }
        for (let y = 0; y < h; y++) { queue.push(0); queue.push(y); queue.push(w - 1); queue.push(y); }

        let qi = 0;
        while (qi < queue.length) {
            const x = queue[qi++];
            const y = queue[qi++];
            if (x < 0 || x >= w || y < 0 || y >= h) continue;
            const pos = y * w + x;
            if (visited[pos]) continue;
            visited[pos] = 1;

            const idx = pos * 4;
            const dr = Math.abs(data[idx] - bgR);
            const dg = Math.abs(data[idx + 1] - bgG);
            const db = Math.abs(data[idx + 2] - bgB);
            const dist = (dr + dg + db) / 3;

            if (dist <= th) {
                isBackground[pos] = 1;
                // Add neighbors
                queue.push(x - 1, y);
                queue.push(x + 1, y);
                queue.push(x, y - 1);
                queue.push(x, y + 1);
            }
        }

        // Apply feathering at edges
        const fe = feather.value;

        // Apply transparency
        for (let i = 0; i < w * h; i++) {
            if (isBackground[i]) {
                const idx = i * 4;
                if (bgColor.value === 'transparent') {
                    data[idx + 3] = 0;
                } else {
                    // Replace with chosen color
                    const c = hexToRgb(bgColor.value);
                    data[idx] = c.r; data[idx + 1] = c.g; data[idx + 2] = c.b;
                }
            } else if (fe > 0) {
                // Check if near a background pixel for feathering
                const x = i % w, y = Math.floor(i / w);
                let nearBg = false;
                const feInt = Math.ceil(fe);
                for (let dy = -feInt; dy <= feInt && !nearBg; dy++) {
                    for (let dx = -feInt; dx <= feInt && !nearBg; dx++) {
                        const nx = x + dx, ny = y + dy;
                        if (nx >= 0 && nx < w && ny >= 0 && ny < h && isBackground[ny * w + nx]) nearBg = true;
                    }
                }
                if (nearBg) {
                    const idx = i * 4;
                    data[idx + 3] = Math.round(data[idx + 3] * 0.7);
                }
            }
        }

        ctx.putImageData(imageData, 0, 0);
        resultUrl.value = cv.toDataURL('image/png');
        processing.value = false;
    };

    if (imgEl.complete) { setTimeout(process, 50); }
    else { imgEl.onload = () => setTimeout(process, 50); }
};

const hexToRgb = (hex) => {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return { r, g, b };
};
</script>
