<template>
<div class="space-y-6">
    <div v-if="files.length === 0" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-indigo-900/50 dark:text-indigo-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload videos to merge</h3>
        <p class="text-sm text-surface-500">Select 2 or more video files to combine</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">{{ files.length }} Videos Selected</h3>
            <div class="flex gap-2">
                <label class="text-sm text-primary-600 hover:text-primary-700 font-medium cursor-pointer">+ Add More<input type="file" @change="onFile" accept="video/*" multiple class="hidden" /></label>
                <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Clear All</button>
            </div>
        </div>
        <div class="space-y-2">
            <div v-for="(f,i) in files" :key="i" class="p-3 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 flex items-center gap-3">
                <span class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-sm font-bold">{{ i+1 }}</span>
                <span class="flex-1 text-sm text-surface-900 dark:text-white truncate">{{ f.name }}</span>
                <span class="text-xs text-surface-500">{{ formatBytes(f.size) }}</span>
                <button @click="moveUp(i)" :disabled="i===0" class="p-1 text-surface-400 hover:text-surface-600 disabled:opacity-30">↑</button>
                <button @click="moveDown(i)" :disabled="i===files.length-1" class="p-1 text-surface-400 hover:text-surface-600 disabled:opacity-30">↓</button>
                <button @click="removeFile(i)" class="p-1 text-red-400 hover:text-red-600">✕</button>
            </div>
        </div>
        <button @click="mergeVideos" :disabled="processing || files.length < 2" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
            <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
            {{ processing ? `Merging ${progress}%` : '🔗 Merge Videos' }}
        </button>
        <div v-if="resultUrl" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-green-700 dark:text-green-300 font-medium">✅ Videos merged successfully</span>
                <a :href="resultUrl" download="merged.webm" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm">Download</a>
            </div>
            <video :src="resultUrl" controls class="w-full rounded-lg max-h-[300px]"></video>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, reactive } from 'vue';
const drag = ref(false); const files = reactive([]); const processing = ref(false); const progress = ref(0); const resultUrl = ref(null);
const formatBytes = (b) => { if(!b) return '0 B'; const k=1024,s=['B','KB','MB','GB']; const i=Math.floor(Math.log(b)/Math.log(k)); return (b/Math.pow(k,i)).toFixed(1)+' '+s[i]; };
const onDrop = (e) => { drag.value=false; [...e.dataTransfer.files].filter(f=>f.type.startsWith('video/')).forEach(f=>files.push(f)); };
const onFile = (e) => { [...e.target.files].forEach(f=>files.push(f)); e.target.value=''; };
const moveUp = (i) => { if(i>0) { const t=files[i]; files[i]=files[i-1]; files[i-1]=t; }};
const moveDown = (i) => { if(i<files.length-1) { const t=files[i]; files[i]=files[i+1]; files[i+1]=t; }};
const removeFile = (i) => { files.splice(i,1); };
const resetTool = () => { files.splice(0); if(resultUrl.value) URL.revokeObjectURL(resultUrl.value); resultUrl.value=null; };

const mergeVideos = async () => {
    if(files.length<2||processing.value) return;
    processing.value=true; progress.value=0; resultUrl.value=null;
    // Load all videos to get dimensions
    const vids = [];
    for (const f of files) { const v=document.createElement('video'); v.src=URL.createObjectURL(f); v.muted=true; await new Promise(r=>{v.onloadedmetadata=r;v.load();}); vids.push(v); }
    const maxW = Math.max(...vids.map(v=>v.videoWidth));
    const maxH = Math.max(...vids.map(v=>v.videoHeight));
    const canvas = document.createElement('canvas'); canvas.width=maxW; canvas.height=maxH;
    const ctx=canvas.getContext('2d');
    const stream=canvas.captureStream(30);
    const recorder=new MediaRecorder(stream, {mimeType:'video/webm;codecs=vp9', videoBitsPerSecond:5000000});
    const chunks=[];
    recorder.ondataavailable=(e)=>{if(e.data.size>0)chunks.push(e.data);};
    recorder.onstop=()=>{const blob=new Blob(chunks,{type:'video/webm'}); resultUrl.value=URL.createObjectURL(blob); processing.value=false; vids.forEach(v=>URL.revokeObjectURL(v.src));};
    recorder.start();
    const totalDur=vids.reduce((s,v)=>s+v.duration,0);
    let elapsed=0;
    for(let vi=0;vi<vids.length;vi++){
        const v=vids[vi];
        v.currentTime=0;
        await new Promise(resolve=>{
            v.onseeked=()=>{
                v.play();
                const tick=()=>{
                    if(v.ended||v.paused){resolve();return;}
                    ctx.fillStyle='#000'; ctx.fillRect(0,0,maxW,maxH);
                    const sw=maxW/v.videoWidth, sh=maxH/v.videoHeight, sc=Math.min(sw,sh);
                    const dw=v.videoWidth*sc, dh=v.videoHeight*sc;
                    ctx.drawImage(v,(maxW-dw)/2,(maxH-dh)/2,dw,dh);
                    progress.value=Math.min(99,Math.round(((elapsed+v.currentTime)/totalDur)*100));
                    requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
                v.onended=()=>{elapsed+=v.duration;resolve();};
            };
        });
    }
    progress.value=100; setTimeout(()=>recorder.stop(),200);
};
</script>
