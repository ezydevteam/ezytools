<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-primary-900/50 dark:text-primary-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to resize</h3>
        <p class="text-sm text-surface-500">Supports MP4, WebM, MOV (Max 500MB)</p>
    </div>

    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Resize Settings</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-3">
                    <div class="flex justify-between text-sm"><span class="text-surface-500">Original:</span><span class="font-medium text-surface-900 dark:text-white">{{ origW }} × {{ origH }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-surface-500">File size:</span><span class="font-medium text-surface-900 dark:text-white">{{ formatBytes(fileSize) }}</span></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Width (px)</label>
                        <input type="number" v-model.number="targetW" min="16" max="7680" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="lockAspect && syncH()" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Height (px)</label>
                        <input type="number" v-model.number="targetH" min="16" max="4320" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" @input="lockAspect && syncW()" />
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400">
                    <input type="checkbox" v-model="lockAspect" class="rounded border-surface-300 text-primary-600 focus:ring-primary-500" /> Lock aspect ratio
                </label>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Preset</label>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="p in presets" :key="p.label" @click="applyPreset(p)" class="px-3 py-1.5 text-xs rounded-lg border border-surface-300 dark:border-surface-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" :class="targetW===p.w&&targetH===p.h?'bg-primary-100 dark:bg-primary-900/30 border-primary-400 text-primary-700':'text-surface-600 dark:text-surface-400'">{{ p.label }}</button>
                    </div>
                </div>
                <button @click="processVideo" :disabled="processing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
                    <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    {{ processing ? `Processing ${progress}%` : 'Resize Video' }}
                </button>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[300px]" @loadedmetadata="onMeta"></video>
            </div>
        </div>
        <div v-if="resultUrl" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-between">
            <span class="text-green-700 dark:text-green-300 font-medium">✅ Resized to {{ targetW }}×{{ targetH }}</span>
            <a :href="resultUrl" :download="resultName" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm">Download</a>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false);
const videoSrc = ref(null);
const fileSize = ref(0);
const origW = ref(0);
const origH = ref(0);
const targetW = ref(1280);
const targetH = ref(720);
const lockAspect = ref(true);
const processing = ref(false);
const progress = ref(0);
const resultUrl = ref(null);
const resultName = ref('resized.mp4');
const vidEl = ref(null);
let file = null;
const presets = [
    { label: '1080p', w: 1920, h: 1080 },
    { label: '720p', w: 1280, h: 720 },
    { label: '480p', w: 854, h: 480 },
    { label: '360p', w: 640, h: 360 },
    { label: 'Square', w: 720, h: 720 },
    { label: 'Story 9:16', w: 1080, h: 1920 },
];

const formatBytes = (b) => { if (!b) return '0 B'; const k=1024,s=['B','KB','MB','GB']; const i=Math.floor(Math.log(b)/Math.log(k)); return (b/Math.pow(k,i)).toFixed(1)+' '+s[i]; };
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { file=f; fileSize.value=f.size; videoSrc.value=URL.createObjectURL(f); resultUrl.value=null; resultName.value=f.name.replace(/\.[^.]+$/,'-resized.mp4'); };
const onMeta = () => { const v=vidEl.value; origW.value=v.videoWidth; origH.value=v.videoHeight; targetW.value=v.videoWidth; targetH.value=v.videoHeight; };
const syncH = () => { if(origW.value) targetH.value = Math.round(targetW.value * origH.value / origW.value); };
const syncW = () => { if(origH.value) targetW.value = Math.round(targetH.value * origW.value / origH.value); };
const applyPreset = (p) => { targetW.value=p.w; targetH.value=p.h; lockAspect.value=false; };
const resetTool = () => { if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); if(resultUrl.value) URL.revokeObjectURL(resultUrl.value); videoSrc.value=null; resultUrl.value=null; file=null; };

const processVideo = () => {
    if(!file||processing.value) return;
    processing.value=true; progress.value=0; resultUrl.value=null;
    const video = document.createElement('video');
    video.src = videoSrc.value; video.muted = true;
    const canvas = document.createElement('canvas');
    canvas.width = targetW.value; canvas.height = targetH.value;
    const ctx = canvas.getContext('2d');
    video.onloadedmetadata = () => {
        const dur = video.duration;
        const stream = canvas.captureStream(30);
        // Add audio track from original
        try {
            const ac = new AudioContext();
            const src = ac.createMediaElementSource(video);
            const dest = ac.createMediaStreamDestination();
            src.connect(dest); src.connect(ac.destination);
            dest.stream.getAudioTracks().forEach(t => stream.addTrack(t));
        } catch(e) {}
        const recorder = new MediaRecorder(stream, { mimeType: 'video/webm;codecs=vp9', videoBitsPerSecond: 5000000 });
        const chunks = [];
        recorder.ondataavailable = (e) => { if(e.data.size>0) chunks.push(e.data); };
        recorder.onstop = () => {
            const blob = new Blob(chunks, {type:'video/webm'});
            resultUrl.value = URL.createObjectURL(blob);
            resultName.value = file.name.replace(/\.[^.]+$/,'-resized.webm');
            processing.value = false;
        };
        recorder.start();
        video.play();
        const tick = () => {
            if(video.ended || video.paused) { recorder.stop(); return; }
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            progress.value = Math.min(99, Math.round((video.currentTime/dur)*100));
            requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        video.onended = () => { progress.value=100; setTimeout(()=>recorder.stop(),200); };
    };
    video.load();
};
</script>
