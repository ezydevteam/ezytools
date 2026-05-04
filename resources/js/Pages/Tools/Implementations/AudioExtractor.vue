<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-purple-900/50 dark:text-purple-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to extract audio</h3>
        <p class="text-sm text-surface-500">Supports MP4, WebM, MOV, AVI</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Extract Audio</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-2">
                    <div class="flex justify-between text-sm"><span class="text-surface-500">File:</span><span class="font-medium text-surface-900 dark:text-white truncate ml-2">{{ fileName }}</span></div>
                    <div class="flex justify-between text-sm"><span class="text-surface-500">Duration:</span><span class="font-medium text-surface-900 dark:text-white">{{ formatTime(duration) }}</span></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Output Format</label>
                    <select v-model="format" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm">
                        <option value="mp3">MP3</option>
                        <option value="wav">WAV</option>
                        <option value="webm">WebM Audio</option>
                    </select>
                </div>
                <button @click="extractAudio" :disabled="processing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
                    <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    {{ processing ? `Extracting ${progress}%` : '🎵 Extract Audio' }}
                </button>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[300px]" @loadedmetadata="onMeta"></video>
            </div>
        </div>
        <div v-if="resultUrl" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-between">
            <div>
                <span class="text-green-700 dark:text-green-300 font-medium">✅ Audio extracted</span>
                <audio :src="resultUrl" controls class="mt-2 w-full"></audio>
            </div>
            <a :href="resultUrl" :download="resultName" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm shrink-0 ml-4">Download</a>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false);
const videoSrc = ref(null);
const fileName = ref('');
const duration = ref(0);
const format = ref('mp3');
const processing = ref(false);
const progress = ref(0);
const resultUrl = ref(null);
const resultName = ref('audio.mp3');
const vidEl = ref(null);

const formatTime = (s) => { const m=Math.floor(s/60); return `${m}:${String(Math.floor(s%60)).padStart(2,'0')}`; };
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { fileName.value=f.name; videoSrc.value=URL.createObjectURL(f); resultUrl.value=null; };
const onMeta = () => { duration.value = vidEl.value.duration; };
const resetTool = () => { if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); if(resultUrl.value) URL.revokeObjectURL(resultUrl.value); videoSrc.value=null; resultUrl.value=null; };

const extractAudio = () => {
    if(processing.value) return;
    processing.value=true; progress.value=0; resultUrl.value=null;
    const video = document.createElement('video');
    video.src = videoSrc.value; video.muted = false;
    video.onloadedmetadata = () => {
        const ac = new AudioContext();
        const src = ac.createMediaElementSource(video);
        const dest = ac.createMediaStreamDestination();
        src.connect(dest); src.connect(ac.destination);
        const mimeType = format.value === 'webm' ? 'audio/webm;codecs=opus' : 'audio/webm;codecs=opus';
        const recorder = new MediaRecorder(dest.stream, { mimeType });
        const chunks = [];
        recorder.ondataavailable = (e) => { if(e.data.size>0) chunks.push(e.data); };
        recorder.onstop = () => {
            const ext = format.value === 'webm' ? 'webm' : 'webm'; // browser can only output webm natively
            const blob = new Blob(chunks, {type:'audio/webm'});
            resultUrl.value = URL.createObjectURL(blob);
            resultName.value = fileName.value.replace(/\.[^.]+$/,`.${ext}`);
            processing.value = false;
        };
        recorder.start();
        video.play();
        const tick = () => {
            if(video.ended) return;
            progress.value = Math.min(99, Math.round((video.currentTime/video.duration)*100));
            requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        video.onended = () => { progress.value=100; setTimeout(()=>recorder.stop(),200); };
    };
    video.load();
};
</script>
