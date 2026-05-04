<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-emerald-900/50 dark:text-emerald-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to compress</h3>
        <p class="text-sm text-surface-500">Reduce file size while maintaining quality</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Compression Settings</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-surface-500">Original size:</span><span class="font-medium text-surface-900 dark:text-white">{{ formatBytes(origSize) }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-surface-500">Resolution:</span><span class="font-medium text-surface-900 dark:text-white">{{ origW }}×{{ origH }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-surface-500">Duration:</span><span class="font-medium text-surface-900 dark:text-white">{{ formatTime(duration) }}</span></div>
                </div>
                <div>
                    <div class="flex justify-between mb-1"><label class="text-sm font-medium text-surface-700 dark:text-surface-300">Quality</label><span class="text-sm font-bold text-primary-600">{{ quality }}%</span></div>
                    <input type="range" v-model.number="quality" min="10" max="100" class="w-full h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer dark:bg-surface-700 accent-primary-600" />
                    <div class="flex justify-between text-xs text-surface-500 mt-1"><span>Smallest</span><span>Best Quality</span></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Scale Down</label>
                    <select v-model="scale" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm">
                        <option value="1">Original resolution</option>
                        <option value="0.75">75% resolution</option>
                        <option value="0.5">50% resolution</option>
                        <option value="0.25">25% resolution</option>
                    </select>
                </div>
                <button @click="compressVideo" :disabled="processing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
                    <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    {{ processing ? `Compressing ${progress}%` : '📦 Compress Video' }}
                </button>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[300px]" @loadedmetadata="onMeta"></video>
            </div>
        </div>
        <div v-if="resultUrl" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-between">
            <div><span class="text-green-700 dark:text-green-300 font-medium">✅ Compressed: {{ formatBytes(compressedSize) }}</span><span class="text-sm text-green-600 ml-2">({{ savedPercent }}% saved)</span></div>
            <a :href="resultUrl" :download="resultName" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm">Download</a>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, computed } from 'vue';
const drag = ref(false); const videoSrc = ref(null); const origSize = ref(0); const origW = ref(0); const origH = ref(0); const duration = ref(0);
const quality = ref(60); const scale = ref('0.75'); const processing = ref(false); const progress = ref(0);
const resultUrl = ref(null); const resultName = ref('compressed.webm'); const compressedSize = ref(0); const vidEl = ref(null);
let file = null;
const savedPercent = computed(() => origSize.value ? Math.round((1 - compressedSize.value / origSize.value) * 100) : 0);
const formatBytes = (b) => { if(!b) return '0 B'; const k=1024,s=['B','KB','MB','GB']; const i=Math.floor(Math.log(b)/Math.log(k)); return (b/Math.pow(k,i)).toFixed(1)+' '+s[i]; };
const formatTime = (s) => { const m=Math.floor(s/60); return `${m}:${String(Math.floor(s%60)).padStart(2,'0')}`; };
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { file=f; origSize.value=f.size; videoSrc.value=URL.createObjectURL(f); resultUrl.value=null; resultName.value=f.name.replace(/\.[^.]+$/,'-compressed.webm'); };
const onMeta = () => { origW.value=vidEl.value.videoWidth; origH.value=vidEl.value.videoHeight; duration.value=vidEl.value.duration; };
const resetTool = () => { if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); if(resultUrl.value) URL.revokeObjectURL(resultUrl.value); videoSrc.value=null; resultUrl.value=null; file=null; };

const compressVideo = () => {
    if(!file||processing.value) return;
    processing.value=true; progress.value=0; resultUrl.value=null;
    const video = document.createElement('video'); video.src=videoSrc.value; video.muted=true;
    const canvas = document.createElement('canvas');
    const s = parseFloat(scale.value);
    video.onloadedmetadata = () => {
        canvas.width=Math.round(video.videoWidth*s); canvas.height=Math.round(video.videoHeight*s);
        const ctx = canvas.getContext('2d');
        const bitrate = Math.round(5000000 * (quality.value / 100) * s * s);
        const stream = canvas.captureStream(30);
        try { const ac=new AudioContext(); const src=ac.createMediaElementSource(video); const dest=ac.createMediaStreamDestination(); src.connect(dest); src.connect(ac.destination); dest.stream.getAudioTracks().forEach(t=>stream.addTrack(t)); } catch(e){}
        const recorder = new MediaRecorder(stream, { mimeType:'video/webm;codecs=vp9', videoBitsPerSecond: bitrate });
        const chunks = [];
        recorder.ondataavailable = (e) => { if(e.data.size>0) chunks.push(e.data); };
        recorder.onstop = () => { const blob=new Blob(chunks,{type:'video/webm'}); compressedSize.value=blob.size; resultUrl.value=URL.createObjectURL(blob); processing.value=false; };
        recorder.start(); video.play();
        const tick = () => { if(video.ended||video.paused) { recorder.stop(); return; } ctx.drawImage(video,0,0,canvas.width,canvas.height); progress.value=Math.min(99,Math.round((video.currentTime/video.duration)*100)); requestAnimationFrame(tick); };
        requestAnimationFrame(tick);
        video.onended = () => { progress.value=100; setTimeout(()=>recorder.stop(),200); };
    };
    video.load();
};
</script>
