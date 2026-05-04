<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Settings Panel -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Template</label>
                <select v-model="template" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @change="applyTemplate">
                    <option value="banner">Channel Banner (2560×1440)</option>
                    <option value="thumbnail">Video Thumbnail (1280×720)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Background</label>
                <div class="grid grid-cols-4 gap-1.5 mb-2">
                    <button v-for="g in gradients" :key="g.name" @click="gradient=g.value" class="h-10 rounded-lg border-2 transition-all" :class="gradient===g.value?'border-primary-500 ring-2 ring-primary-200':'border-surface-300 dark:border-surface-600'" :style="{background:g.value}" :title="g.name"></button>
                </div>
                <label class="flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400 cursor-pointer">
                    <span>Or upload image:</span>
                    <input type="file" @change="onBgImage" accept="image/*" class="text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-primary-100 file:text-primary-700 file:text-xs file:font-medium" />
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Title Text</label>
                <input type="text" v-model="titleText" placeholder="Your Channel Name" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="render" />
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Subtitle</label>
                <input type="text" v-model="subtitleText" placeholder="Subscribe for more..." class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="render" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Text Color</label>
                    <input type="color" v-model="textColor" class="w-full h-9 rounded-lg border-surface-300 cursor-pointer" @input="render" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Font Size</label>
                    <input type="number" v-model.number="fontSize" min="20" max="200" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="render" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Overlay Image (Logo/Avatar)</label>
                <input type="file" @change="onOverlayImage" accept="image/*" class="w-full text-sm file:mr-2 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-primary-100 file:text-primary-700 file:text-xs file:font-medium" />
            </div>
            <button @click="downloadResult" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download Cover</button>
        </div>

        <!-- Canvas Preview -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-900 overflow-hidden">
                <canvas ref="canvasEl" class="w-full h-auto"></canvas>
            </div>
            <p class="text-xs text-surface-500 mt-2">Canvas: {{ canvasW }}×{{ canvasH }}px — YouTube {{ template === 'banner' ? 'Channel Art' : 'Thumbnail' }}</p>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';

const template = ref('thumbnail');
const canvasEl = ref(null);
const canvasW = ref(1280);
const canvasH = ref(720);
const gradient = ref('linear-gradient(135deg, #667eea 0%, #764ba2 100%)');
const titleText = ref('');
const subtitleText = ref('');
const textColor = ref('#ffffff');
const fontSize = ref(72);
let bgImg = null;
let overlayImg = null;

const gradients = [
    { name: 'Purple', value: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' },
    { name: 'Sunset', value: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)' },
    { name: 'Ocean', value: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)' },
    { name: 'Fire', value: 'linear-gradient(135deg, #f83600 0%, #f9d423 100%)' },
    { name: 'Dark', value: 'linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%)' },
    { name: 'Forest', value: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)' },
    { name: 'Candy', value: 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)' },
    { name: 'Midnight', value: 'linear-gradient(135deg, #232526 0%, #414345 100%)' },
];

const applyTemplate = () => {
    if (template.value === 'banner') { canvasW.value = 2560; canvasH.value = 1440; fontSize.value = 120; }
    else { canvasW.value = 1280; canvasH.value = 720; fontSize.value = 72; }
    render();
};

const onBgImage = (e) => {
    const f = e.target.files[0]; if (!f) return;
    const img = new Image();
    img.onload = () => { bgImg = img; render(); };
    img.src = URL.createObjectURL(f);
};

const onOverlayImage = (e) => {
    const f = e.target.files[0]; if (!f) return;
    const img = new Image();
    img.onload = () => { overlayImg = img; render(); };
    img.src = URL.createObjectURL(f);
};

watch(gradient, render);

const parseGradient = (grad) => {
    const match = grad.match(/linear-gradient\((\d+)deg,\s*(.+)\)/);
    if (!match) return null;
    const angle = parseInt(match[1]) * Math.PI / 180;
    const stops = match[2].split(',').map(s => {
        const parts = s.trim().split(/\s+/);
        return { color: parts[0], pos: parseInt(parts[1]) / 100 };
    });
    return { angle, stops };
};

function render() {
    const cv = canvasEl.value; if (!cv) return;
    cv.width = canvasW.value; cv.height = canvasH.value;
    const ctx = cv.getContext('2d');
    const w = cv.width, h = cv.height;

    // Background
    if (bgImg) {
        const ir = bgImg.naturalWidth / bgImg.naturalHeight;
        const cr = w / h;
        let sx = 0, sy = 0, sw = bgImg.naturalWidth, sh = bgImg.naturalHeight;
        if (ir > cr) { sw = bgImg.naturalHeight * cr; sx = (bgImg.naturalWidth - sw) / 2; }
        else { sh = bgImg.naturalWidth / cr; sy = (bgImg.naturalHeight - sh) / 2; }
        ctx.drawImage(bgImg, sx, sy, sw, sh, 0, 0, w, h);
        // Dim overlay
        ctx.fillStyle = 'rgba(0,0,0,0.35)';
        ctx.fillRect(0, 0, w, h);
    } else {
        const g = parseGradient(gradient.value);
        if (g) {
            const grd = ctx.createLinearGradient(0, 0, w * Math.cos(g.angle), h * Math.sin(g.angle));
            g.stops.forEach(s => grd.addColorStop(s.pos, s.color));
            ctx.fillStyle = grd;
        } else {
            ctx.fillStyle = '#1a1a2e';
        }
        ctx.fillRect(0, 0, w, h);
    }

    // Overlay image (logo)
    if (overlayImg) {
        const size = Math.min(w, h) * 0.2;
        const ox = w * 0.08;
        const oy = (h - size) / 2;
        ctx.save();
        ctx.beginPath();
        ctx.arc(ox + size / 2, oy + size / 2, size / 2, 0, Math.PI * 2);
        ctx.clip();
        ctx.drawImage(overlayImg, ox, oy, size, size);
        ctx.restore();
        // Border
        ctx.strokeStyle = 'rgba(255,255,255,0.5)';
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.arc(ox + size / 2, oy + size / 2, size / 2, 0, Math.PI * 2);
        ctx.stroke();
    }

    // Title
    if (titleText.value) {
        ctx.fillStyle = textColor.value;
        ctx.font = `bold ${fontSize.value}px 'Inter', 'Segoe UI', Arial, sans-serif`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.shadowColor = 'rgba(0,0,0,0.5)';
        ctx.shadowBlur = 10;
        ctx.fillText(titleText.value, w / 2, h / 2 - (subtitleText.value ? fontSize.value * 0.4 : 0));
        ctx.shadowBlur = 0;
    }

    // Subtitle
    if (subtitleText.value) {
        const subSize = Math.round(fontSize.value * 0.45);
        ctx.fillStyle = textColor.value;
        ctx.globalAlpha = 0.8;
        ctx.font = `${subSize}px 'Inter', 'Segoe UI', Arial, sans-serif`;
        ctx.textAlign = 'center';
        ctx.fillText(subtitleText.value, w / 2, h / 2 + fontSize.value * 0.5);
        ctx.globalAlpha = 1;
    }

    // YouTube play button watermark
    ctx.globalAlpha = 0.15;
    ctx.fillStyle = '#ff0000';
    const btnW = 80, btnH = 56;
    const btnX = w - btnW - 30, btnY = h - btnH - 30;
    ctx.beginPath();
    ctx.roundRect(btnX, btnY, btnW, btnH, 12);
    ctx.fill();
    ctx.fillStyle = '#fff';
    ctx.beginPath();
    ctx.moveTo(btnX + 30, btnY + 14);
    ctx.lineTo(btnX + 56, btnY + 28);
    ctx.lineTo(btnX + 30, btnY + 42);
    ctx.closePath();
    ctx.fill();
    ctx.globalAlpha = 1;
}

const downloadResult = () => {
    const cv = canvasEl.value; if (!cv) return;
    const a = document.createElement('a');
    a.href = cv.toDataURL('image/png');
    a.download = `youtube-${template.value}-${Date.now()}.png`;
    a.click();
};

onMounted(() => { applyTemplate(); });
</script>
