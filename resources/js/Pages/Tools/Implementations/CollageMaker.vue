<template>
<div class="space-y-6">
    <!-- Upload zone -->
    <div class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-8 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="image/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-3 dark:bg-primary-900/50 dark:text-primary-400">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-base font-bold text-surface-900 dark:text-white mb-1">{{ images.length ? 'Add more photos' : 'Upload photos for collage' }}</h3>
        <p class="text-sm text-surface-500">Select 2-9 images (JPG, PNG, WebP)</p>
    </div>

    <div v-if="images.length >= 2" class="space-y-6">
        <!-- Image list -->
        <div class="flex gap-2 overflow-x-auto pb-2">
            <div v-for="(img,i) in images" :key="i" class="relative shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-surface-200 dark:border-surface-700 group">
                <img :src="img.src" class="w-full h-full object-cover" />
                <button @click="images.splice(i,1)" class="absolute top-0.5 right-0.5 w-5 h-5 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">✕</button>
            </div>
        </div>

        <!-- Layout options -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Layout</label>
                    <select v-model="layout" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm">
                        <option value="grid">Grid</option>
                        <option value="horizontal">Horizontal Strip</option>
                        <option value="vertical">Vertical Strip</option>
                        <option value="featured">Featured (1 big + small)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Gap (px)</label>
                    <input type="range" v-model.number="gap" min="0" max="20" class="w-full accent-primary-600" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Border Radius</label>
                    <input type="range" v-model.number="radius" min="0" max="30" class="w-full accent-primary-600" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Background</label>
                    <div class="flex gap-2">
                        <button v-for="c in bgColors" :key="c" @click="bgColor=c" class="w-8 h-8 rounded-lg border-2 transition-all" :class="bgColor===c?'border-primary-500 ring-2 ring-primary-200':'border-surface-300'" :style="{background:c}"></button>
                        <input type="color" v-model="bgColor" class="w-8 h-8 rounded-lg border-0 cursor-pointer" />
                    </div>
                </div>
                <button @click="generateCollage" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">🎨 Generate Collage</button>
            </div>

            <!-- Preview -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden flex items-center justify-center p-2 min-h-[300px]">
                    <canvas ref="canvasEl" class="max-w-full max-h-[500px] rounded-lg shadow-sm"></canvas>
                </div>
            </div>
        </div>

        <div v-if="resultUrl" class="flex gap-3">
            <a :href="resultUrl" download="collage.png" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md text-center">⬇ Download Collage</a>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';

const drag = ref(false);
const images = reactive([]);
const layout = ref('grid');
const gap = ref(6);
const radius = ref(8);
const bgColor = ref('#ffffff');
const resultUrl = ref(null);
const canvasEl = ref(null);

const bgColors = ['#ffffff', '#000000', '#f3f4f6', '#1e293b', '#fef3c7', '#dbeafe'];

const onDrop = (e) => { drag.value = false; [...e.dataTransfer.files].filter(f => f.type.startsWith('image/')).forEach(addFile); };
const onFile = (e) => { [...e.target.files].forEach(addFile); e.target.value = ''; };

const addFile = (f) => {
    if (images.length >= 9) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
        const img = new Image();
        img.onload = () => { images.push({ src: ev.target.result, el: img, w: img.naturalWidth, h: img.naturalHeight }); };
        img.src = ev.target.result;
    };
    reader.readAsDataURL(f);
};

watch([layout, gap, radius, bgColor, () => images.length], () => { if (images.length >= 2) generateCollage(); });

const generateCollage = () => {
    if (images.length < 2) return;
    const cv = canvasEl.value;
    const g = gap.value;
    const n = images.length;
    let cw, ch;

    if (layout.value === 'horizontal') { cw = 1200; ch = 400; }
    else if (layout.value === 'vertical') { cw = 500; ch = 1200; }
    else if (layout.value === 'featured') { cw = 1200; ch = 800; }
    else { // grid
        const cols = Math.ceil(Math.sqrt(n));
        cw = 1200; ch = Math.ceil(n / cols) * (1200 / cols);
    }

    cv.width = cw; cv.height = ch;
    const ctx = cv.getContext('2d');
    ctx.fillStyle = bgColor.value;
    ctx.fillRect(0, 0, cw, ch);

    if (layout.value === 'horizontal') {
        const cellW = (cw - g * (n + 1)) / n;
        images.forEach((img, i) => {
            const x = g + i * (cellW + g);
            drawRoundedImage(ctx, img.el, x, g, cellW, ch - g * 2, radius.value);
        });
    } else if (layout.value === 'vertical') {
        const cellH = (ch - g * (n + 1)) / n;
        images.forEach((img, i) => {
            const y = g + i * (cellH + g);
            drawRoundedImage(ctx, img.el, g, y, cw - g * 2, cellH, radius.value);
        });
    } else if (layout.value === 'featured') {
        // First image takes left 60%
        const mainW = cw * 0.6 - g * 1.5;
        drawRoundedImage(ctx, images[0].el, g, g, mainW, ch - g * 2, radius.value);
        // Rest stack on right
        const rightX = cw * 0.6 + g * 0.5;
        const rightW = cw - rightX - g;
        const sideN = n - 1;
        const cellH = (ch - g * (sideN + 1)) / sideN;
        for (let i = 1; i < n; i++) {
            const y = g + (i - 1) * (cellH + g);
            drawRoundedImage(ctx, images[i].el, rightX, y, rightW, cellH, radius.value);
        }
    } else {
        // Grid
        const cols = Math.ceil(Math.sqrt(n));
        const rows = Math.ceil(n / cols);
        const cellW = (cw - g * (cols + 1)) / cols;
        const cellH = (ch - g * (rows + 1)) / rows;
        images.forEach((img, i) => {
            const col = i % cols;
            const row = Math.floor(i / cols);
            drawRoundedImage(ctx, img.el, g + col * (cellW + g), g + row * (cellH + g), cellW, cellH, radius.value);
        });
    }

    resultUrl.value = cv.toDataURL('image/png');
};

const drawRoundedImage = (ctx, img, x, y, w, h, r) => {
    ctx.save();
    ctx.beginPath();
    ctx.roundRect(x, y, w, h, r);
    ctx.clip();
    // Cover-fit the image
    const imgRatio = img.naturalWidth / img.naturalHeight;
    const cellRatio = w / h;
    let sx = 0, sy = 0, sw = img.naturalWidth, sh = img.naturalHeight;
    if (imgRatio > cellRatio) { sw = img.naturalHeight * cellRatio; sx = (img.naturalWidth - sw) / 2; }
    else { sh = img.naturalWidth / cellRatio; sy = (img.naturalHeight - sh) / 2; }
    ctx.drawImage(img, sx, sy, sw, sh, x, y, w, h);
    ctx.restore();
};
</script>
