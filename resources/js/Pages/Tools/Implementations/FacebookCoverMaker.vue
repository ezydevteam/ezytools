<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Settings Panel -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Template</label>
                <select v-model="template" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @change="applyTemplate">
                    <option value="cover">Profile Cover (820×312)</option>
                    <option value="page">Page Cover (820×312)</option>
                    <option value="event">Event Cover (1200×628)</option>
                    <option value="group">Group Cover (1640×856)</option>
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
                <input type="text" v-model="titleText" placeholder="Your Name / Brand" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="render" />
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Subtitle</label>
                <input type="text" v-model="subtitleText" placeholder="Tagline or description..." class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="render" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Text Color</label>
                    <input type="color" v-model="textColor" class="w-full h-9 rounded-lg border-surface-300 cursor-pointer" @input="render" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Font Size</label>
                    <input type="number" v-model.number="fontSize" min="16" max="120" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="render" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Text Position</label>
                <div class="grid grid-cols-3 gap-1">
                    <button v-for="p in positions" :key="p.value" @click="textPos=p.value;render()" class="py-1.5 text-xs rounded-lg border transition-colors" :class="textPos===p.value?'bg-primary-100 dark:bg-primary-900/30 border-primary-400 text-primary-700':'border-surface-300 dark:border-surface-600 text-surface-500'">{{ p.label }}</button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Profile Photo Overlay</label>
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
            <p class="text-xs text-surface-500 mt-2">Canvas: {{ canvasW }}×{{ canvasH }}px — Facebook {{ templateLabel }}</p>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';

const template = ref('cover');
const canvasEl = ref(null);
const canvasW = ref(820);
const canvasH = ref(312);
const gradient = ref('linear-gradient(135deg, #4361ee 0%, #7209b7 100%)');
const titleText = ref('');
const subtitleText = ref('');
const textColor = ref('#ffffff');
const fontSize = ref(48);
const textPos = ref('center');
let bgImg = null;
let overlayImg = null;

const templateLabel = computed(() => {
    const map = { cover: 'Profile Cover', page: 'Page Cover', event: 'Event Cover', group: 'Group Cover' };
    return map[template.value];
});

const positions = [
    { label: 'Left', value: 'left' }, { label: 'Center', value: 'center' }, { label: 'Right', value: 'right' },
];

const gradients = [
    { name: 'Royal', value: 'linear-gradient(135deg, #4361ee 0%, #7209b7 100%)' },
    { name: 'Warm', value: 'linear-gradient(135deg, #f72585 0%, #b5179e 100%)' },
    { name: 'Sky', value: 'linear-gradient(135deg, #48cae4 0%, #0096c7 100%)' },
    { name: 'Gold', value: 'linear-gradient(135deg, #f9c74f 0%, #f3722c 100%)' },
    { name: 'Night', value: 'linear-gradient(135deg, #14213d 0%, #023047 100%)' },
    { name: 'Fresh', value: 'linear-gradient(135deg, #2ec4b6 0%, #06d6a0 100%)' },
    { name: 'Rose', value: 'linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%)' },
    { name: 'Mono', value: 'linear-gradient(135deg, #2d3436 0%, #636e72 100%)' },
];

const applyTemplate = () => {
    const sizes = { cover: [820, 312], page: [820, 312], event: [1200, 628], group: [1640, 856] };
    const s = sizes[template.value] || [820, 312];
    canvasW.value = s[0]; canvasH.value = s[1];
    fontSize.value = s[1] > 400 ? 64 : 48;
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
        ctx.fillStyle = 'rgba(0,0,0,0.3)';
        ctx.fillRect(0, 0, w, h);
    } else {
        const g = parseGradient(gradient.value);
        if (g) {
            const grd = ctx.createLinearGradient(0, 0, w * Math.cos(g.angle), h * Math.sin(g.angle));
            g.stops.forEach(s => grd.addColorStop(s.pos, s.color));
            ctx.fillStyle = grd;
        } else { ctx.fillStyle = '#14213d'; }
        ctx.fillRect(0, 0, w, h);
    }

    // Profile overlay
    if (overlayImg) {
        const size = h * 0.45;
        const ox = textPos.value === 'right' ? w - size - 40 : textPos.value === 'center' ? (w - size) / 2 : 40;
        const oy = (h - size) / 2;
        ctx.save();
        ctx.beginPath();
        ctx.arc(ox + size / 2, oy + size / 2, size / 2, 0, Math.PI * 2);
        ctx.clip();
        ctx.drawImage(overlayImg, ox, oy, size, size);
        ctx.restore();
        ctx.strokeStyle = 'rgba(255,255,255,0.6)';
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.arc(ox + size / 2, oy + size / 2, size / 2, 0, Math.PI * 2);
        ctx.stroke();
    }

    // Text
    const textAlign = textPos.value;
    let textX = textPos.value === 'left' ? 40 : textPos.value === 'right' ? w - 40 : w / 2;
    // Offset text if overlay is at the same position
    if (overlayImg && textPos.value === 'center') {
        // Move text below overlay
    }

    if (titleText.value) {
        ctx.fillStyle = textColor.value;
        ctx.font = `bold ${fontSize.value}px 'Inter', 'Segoe UI', Arial, sans-serif`;
        ctx.textAlign = textAlign;
        ctx.textBaseline = 'middle';
        ctx.shadowColor = 'rgba(0,0,0,0.5)';
        ctx.shadowBlur = 8;
        const ty = subtitleText.value ? h / 2 - fontSize.value * 0.35 : h / 2;
        ctx.fillText(titleText.value, textX, ty);
        ctx.shadowBlur = 0;
    }

    if (subtitleText.value) {
        const subSize = Math.round(fontSize.value * 0.45);
        ctx.fillStyle = textColor.value;
        ctx.globalAlpha = 0.75;
        ctx.font = `${subSize}px 'Inter', 'Segoe UI', Arial, sans-serif`;
        ctx.textAlign = textAlign;
        ctx.fillText(subtitleText.value, textX, h / 2 + fontSize.value * 0.45);
        ctx.globalAlpha = 1;
    }

    // Facebook logo watermark
    ctx.globalAlpha = 0.1;
    ctx.fillStyle = '#1877f2';
    ctx.font = 'bold 24px Arial';
    ctx.textAlign = 'right';
    ctx.fillText('f', w - 15, h - 15);
    ctx.globalAlpha = 1;
}

const downloadResult = () => {
    const cv = canvasEl.value; if (!cv) return;
    const a = document.createElement('a');
    a.href = cv.toDataURL('image/png');
    a.download = `facebook-${template.value}-${Date.now()}.png`;
    a.click();
};

onMounted(() => { applyTemplate(); });
</script>
