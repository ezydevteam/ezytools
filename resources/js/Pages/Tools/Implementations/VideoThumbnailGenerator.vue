<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-rose-900/50 dark:text-rose-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to generate thumbnails</h3>
        <p class="text-sm text-surface-500">Extract high-quality frames at any timestamp</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Thumbnail Generator</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[400px]" @loadedmetadata="onMeta" @timeupdate="onTimeUpdate"></video>
        <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-3">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-500 mb-1">Seek to capture position</label>
                    <input type="range" v-model.number="seekTime" min="0" :max="duration" step="0.1" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer accent-rose-600" @input="seekTo" />
                </div>
                <span class="text-sm font-mono font-bold text-surface-900 dark:text-white w-16 text-right">{{ formatTime(seekTime) }}</span>
            </div>
            <div class="flex items-center gap-3">
                <button @click="captureFrame" class="px-5 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-sm text-sm">📸 Capture Frame</button>
                <button @click="autoCapture" :disabled="autoCapturing" class="px-5 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-sm text-sm disabled:opacity-50">
                    {{ autoCapturing ? 'Capturing...' : '🎯 Auto-Capture (8 frames)' }}
                </button>
            </div>
        </div>
        <div v-if="thumbnails.length" class="space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-surface-900 dark:text-white">{{ thumbnails.length }} Thumbnails</h4>
                <button @click="downloadAll" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Download All</button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div v-for="(t,i) in thumbnails" :key="i" class="group relative rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700 bg-black">
                    <img :src="t.dataUrl" class="w-full h-auto" :alt="`Frame at ${formatTime(t.time)}`" />
                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <a :href="t.dataUrl" :download="`thumbnail-${i+1}.png`" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 text-white text-sm">⬇</a>
                        <button @click="thumbnails.splice(i,1)" class="p-2 bg-white/20 rounded-lg hover:bg-red-500/50 text-white text-sm">✕</button>
                    </div>
                    <span class="absolute bottom-1 left-1 text-[10px] bg-black/70 text-white px-1.5 py-0.5 rounded font-mono">{{ formatTime(t.time) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, reactive } from 'vue';
const drag = ref(false); const videoSrc = ref(null); const duration = ref(0); const seekTime = ref(0);
const vidEl = ref(null); const thumbnails = reactive([]); const autoCapturing = ref(false);
const formatTime = (s) => { const m=Math.floor(s/60); return `${m}:${String(Math.floor(s%60)).padStart(2,'0')}`; };
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { videoSrc.value=URL.createObjectURL(f); thumbnails.splice(0); };
const onMeta = () => { duration.value = vidEl.value.duration; };
const onTimeUpdate = () => { seekTime.value = vidEl.value.currentTime; };
const seekTo = () => { if(vidEl.value) vidEl.value.currentTime = seekTime.value; };
const resetTool = () => { if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); videoSrc.value=null; thumbnails.splice(0); };

const captureFrame = () => {
    const v = vidEl.value; if(!v) return;
    const canvas = document.createElement('canvas'); canvas.width=v.videoWidth; canvas.height=v.videoHeight;
    canvas.getContext('2d').drawImage(v, 0, 0);
    thumbnails.push({ dataUrl: canvas.toDataURL('image/png'), time: v.currentTime });
};

const autoCapture = async () => {
    autoCapturing.value = true;
    const v = vidEl.value; if(!v) { autoCapturing.value=false; return; }
    v.pause();
    const canvas = document.createElement('canvas'); canvas.width=v.videoWidth; canvas.height=v.videoHeight;
    const ctx = canvas.getContext('2d');
    const count = 8;
    for (let i = 0; i < count; i++) {
        const t = (duration.value / (count + 1)) * (i + 1);
        v.currentTime = t;
        await new Promise(r => { v.onseeked = r; });
        ctx.drawImage(v, 0, 0);
        thumbnails.push({ dataUrl: canvas.toDataURL('image/png'), time: t });
    }
    autoCapturing.value = false;
};

const downloadAll = () => {
    thumbnails.forEach((t, i) => {
        const a = document.createElement('a'); a.href = t.dataUrl; a.download = `thumbnail-${i+1}.png`;
        a.click();
    });
};
</script>
