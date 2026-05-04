<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-amber-900/50 dark:text-amber-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to add watermark</h3>
        <p class="text-sm text-surface-500">Add text or image watermark to protect your content</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Watermark Settings</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Watermark Text</label>
                    <input type="text" v-model="watermarkText" placeholder="Your watermark text..." class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" />
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Font Size</label>
                        <input type="number" v-model.number="fontSize" min="12" max="120" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Color</label>
                        <input type="color" v-model="fontColor" class="w-full h-9 rounded-lg border-surface-300 cursor-pointer" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Opacity</label>
                        <input type="range" v-model.number="opacity" min="10" max="100" class="w-full mt-2 accent-amber-600" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Position</label>
                    <div class="grid grid-cols-3 gap-1">
                        <button v-for="p in positions" :key="p.value" @click="position=p.value" class="py-2 text-xs rounded-lg border transition-colors" :class="position===p.value?'bg-amber-100 dark:bg-amber-900/30 border-amber-400 text-amber-700':'border-surface-300 dark:border-surface-600 text-surface-500 hover:bg-surface-50'">{{ p.label }}</button>
                    </div>
                </div>
                <button @click="addWatermark" :disabled="processing || !watermarkText.trim()" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
                    <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    {{ processing ? `Processing ${progress}%` : '🛡️ Add Watermark' }}
                </button>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[300px]" @loadedmetadata="onMeta"></video>
            </div>
        </div>
        <div v-if="resultUrl" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl flex items-center justify-between">
            <span class="text-green-700 dark:text-green-300 font-medium">✅ Watermark added</span>
            <a :href="resultUrl" :download="resultName" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm">Download</a>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false); const videoSrc = ref(null); const processing = ref(false); const progress = ref(0);
const resultUrl = ref(null); const resultName = ref('watermarked.webm'); const vidEl = ref(null);
const watermarkText = ref('© EzyTools'); const fontSize = ref(32); const fontColor = ref('#ffffff'); const opacity = ref(50); const position = ref('br');
let file = null; let vw = 0; let vh = 0;
const positions = [
    { label: 'Top Left', value: 'tl' }, { label: 'Top Center', value: 'tc' }, { label: 'Top Right', value: 'tr' },
    { label: 'Mid Left', value: 'ml' }, { label: 'Center', value: 'mc' }, { label: 'Mid Right', value: 'mr' },
    { label: 'Bot Left', value: 'bl' }, { label: 'Bot Center', value: 'bc' }, { label: 'Bot Right', value: 'br' },
];
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { file=f; videoSrc.value=URL.createObjectURL(f); resultUrl.value=null; resultName.value=f.name.replace(/\.[^.]+$/,'-watermarked.webm'); };
const onMeta = () => { vw=vidEl.value.videoWidth; vh=vidEl.value.videoHeight; };
const resetTool = () => { if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); if(resultUrl.value) URL.revokeObjectURL(resultUrl.value); videoSrc.value=null; resultUrl.value=null; };

const getPos = (ctx, text, fs) => {
    const m = ctx.measureText(text); const tw = m.width; const pad = 20;
    const map = { tl:[pad,fs+pad], tc:[(vw-tw)/2,fs+pad], tr:[vw-tw-pad,fs+pad], ml:[pad,vh/2], mc:[(vw-tw)/2,vh/2], mr:[vw-tw-pad,vh/2], bl:[pad,vh-pad], bc:[(vw-tw)/2,vh-pad], br:[vw-tw-pad,vh-pad] };
    return map[position.value] || map.br;
};

const addWatermark = () => {
    if(!file||processing.value) return;
    processing.value=true; progress.value=0; resultUrl.value=null;
    const video = document.createElement('video'); video.src=videoSrc.value; video.muted=true;
    const canvas = document.createElement('canvas');
    video.onloadedmetadata = () => {
        canvas.width=video.videoWidth; canvas.height=video.videoHeight;
        const ctx=canvas.getContext('2d');
        const stream=canvas.captureStream(30);
        try { const ac=new AudioContext(); const src=ac.createMediaElementSource(video); const dest=ac.createMediaStreamDestination(); src.connect(dest); src.connect(ac.destination); dest.stream.getAudioTracks().forEach(t=>stream.addTrack(t)); } catch(e){}
        const recorder=new MediaRecorder(stream,{mimeType:'video/webm;codecs=vp9',videoBitsPerSecond:5000000});
        const chunks=[];
        recorder.ondataavailable=(e)=>{if(e.data.size>0)chunks.push(e.data);};
        recorder.onstop=()=>{const blob=new Blob(chunks,{type:'video/webm'}); resultUrl.value=URL.createObjectURL(blob); processing.value=false;};
        recorder.start(); video.play();
        const fs = fontSize.value;
        const tick=()=>{
            if(video.ended||video.paused){recorder.stop();return;}
            ctx.drawImage(video,0,0);
            ctx.globalAlpha=opacity.value/100;
            ctx.font=`bold ${fs}px Arial`; ctx.fillStyle=fontColor.value;
            ctx.shadowColor='rgba(0,0,0,0.5)'; ctx.shadowBlur=4;
            const [x,y] = getPos(ctx, watermarkText.value, fs);
            ctx.fillText(watermarkText.value, x, y);
            ctx.globalAlpha=1; ctx.shadowBlur=0;
            progress.value=Math.min(99,Math.round((video.currentTime/video.duration)*100));
            requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        video.onended=()=>{progress.value=100;setTimeout(()=>recorder.stop(),200);};
    };
    video.load();
};
</script>
