<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-sky-100 text-sky-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-sky-900/50 dark:text-sky-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to trim</h3>
        <p class="text-sm text-surface-500">Select start and end points to cut your video</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Trim Video</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[400px]" @loadedmetadata="onMeta" @timeupdate="onTimeUpdate"></video>
        <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-3">
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-500 mb-1">Start Time</label>
                    <div class="flex items-center gap-2">
                        <input type="range" v-model.number="trimStart" min="0" :max="duration" step="0.1" class="flex-1 h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer accent-sky-600" @input="seekTo(trimStart)" />
                        <span class="text-sm font-mono font-bold text-surface-900 dark:text-white w-16 text-right">{{ formatTime(trimStart) }}</span>
                    </div>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-surface-500 mb-1">End Time</label>
                    <div class="flex items-center gap-2">
                        <input type="range" v-model.number="trimEnd" min="0" :max="duration" step="0.1" class="flex-1 h-2 bg-surface-200 rounded-lg appearance-none cursor-pointer accent-sky-600" @input="seekTo(trimEnd)" />
                        <span class="text-sm font-mono font-bold text-surface-900 dark:text-white w-16 text-right">{{ formatTime(trimEnd) }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-surface-500">Clip duration: <strong class="text-surface-900 dark:text-white">{{ formatTime(Math.max(0, trimEnd - trimStart)) }}</strong></span>
                <button @click="previewClip" class="text-sky-600 hover:text-sky-700 font-medium text-sm">▶ Preview Clip</button>
            </div>
        </div>
        <button @click="trimVideo" :disabled="processing || trimEnd <= trimStart" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
            <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
            {{ processing ? `Trimming ${progress}%` : '✂️ Trim Video' }}
        </button>
        <div v-if="resultUrl" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-between">
            <span class="text-green-700 dark:text-green-300 font-medium">✅ Video trimmed successfully</span>
            <a :href="resultUrl" :download="resultName" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm">Download</a>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false); const videoSrc = ref(null); const duration = ref(0); const trimStart = ref(0); const trimEnd = ref(0);
const processing = ref(false); const progress = ref(0); const resultUrl = ref(null); const resultName = ref('trimmed.webm'); const vidEl = ref(null);
let file = null;
const formatTime = (s) => { const m=Math.floor(s/60); return `${m}:${String(Math.floor(s%60)).padStart(2,'0')}.${String(Math.floor((s%1)*10))}`; };
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { file=f; videoSrc.value=URL.createObjectURL(f); resultUrl.value=null; resultName.value=f.name.replace(/\.[^.]+$/,'-trimmed.webm'); };
const onMeta = () => { duration.value=vidEl.value.duration; trimEnd.value=duration.value; };
const onTimeUpdate = () => {};
const seekTo = (t) => { if(vidEl.value) vidEl.value.currentTime = t; };
const previewClip = () => { if(vidEl.value) { vidEl.value.currentTime=trimStart.value; vidEl.value.play(); const check=()=>{ if(vidEl.value.currentTime>=trimEnd.value){vidEl.value.pause();return;} requestAnimationFrame(check); }; check(); }};
const resetTool = () => { if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); if(resultUrl.value) URL.revokeObjectURL(resultUrl.value); videoSrc.value=null; resultUrl.value=null; };

const trimVideo = () => {
    if(!file||processing.value) return;
    processing.value=true; progress.value=0; resultUrl.value=null;
    const video = document.createElement('video'); video.src=videoSrc.value; video.muted=true;
    const canvas = document.createElement('canvas');
    video.onloadedmetadata = () => {
        canvas.width=video.videoWidth; canvas.height=video.videoHeight;
        const ctx=canvas.getContext('2d');
        const stream=canvas.captureStream(30);
        try { const ac=new AudioContext(); const src=ac.createMediaElementSource(video); const dest=ac.createMediaStreamDestination(); src.connect(dest); src.connect(ac.destination); dest.stream.getAudioTracks().forEach(t=>stream.addTrack(t)); } catch(e){}
        const recorder = new MediaRecorder(stream, { mimeType:'video/webm;codecs=vp9', videoBitsPerSecond:5000000 });
        const chunks=[];
        recorder.ondataavailable=(e)=>{ if(e.data.size>0) chunks.push(e.data); };
        recorder.onstop=()=>{ const blob=new Blob(chunks,{type:'video/webm'}); resultUrl.value=URL.createObjectURL(blob); processing.value=false; };
        video.currentTime=trimStart.value;
        video.onseeked=()=>{
            recorder.start(); video.play();
            const clipDur=trimEnd.value-trimStart.value;
            const tick=()=>{
                if(video.currentTime>=trimEnd.value||video.ended){recorder.stop();video.pause();return;}
                ctx.drawImage(video,0,0); progress.value=Math.min(99,Math.round(((video.currentTime-trimStart.value)/clipDur)*100));
                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };
    };
    video.load();
};
</script>
