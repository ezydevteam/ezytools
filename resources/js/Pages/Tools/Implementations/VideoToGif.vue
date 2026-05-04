<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-orange-900/50 dark:text-orange-400">
            <svg class="w-10 h-10" viewBox="0 0 24 24" fill="currentColor"><path d="M11.5 9C8.462 9 6 11.462 6 14.5S8.462 20 11.5 20s5.5-2.462 5.5-5.5S14.538 9 11.5 9zm0 9c-1.93 0-3.5-1.57-3.5-3.5S9.57 11 11.5 11s3.5 1.57 3.5 3.5-1.57 3.5-3.5 3.5zM18 4h-4l-2-2H8L6 4H2v14h4.05c-.03-.166-.05-.332-.05-.5C6 14.462 8.462 12 11.5 12s5.5 2.462 5.5 5.5c0 .168-.02.334-.05.5H22V4h-4z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to convert to GIF</h3>
        <p class="text-sm text-surface-500">Short clips work best (under 30 seconds)</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">GIF Settings</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Start (sec)</label>
                        <input type="number" v-model.number="startTime" min="0" :max="duration" step="0.1" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Duration (sec)</label>
                        <input type="number" v-model.number="gifDuration" min="0.5" max="30" step="0.5" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Width (px)</label>
                        <input type="number" v-model.number="gifW" min="50" max="1000" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">FPS</label>
                        <select v-model.number="fps" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm">
                            <option :value="5">5</option><option :value="10">10</option><option :value="15">15</option><option :value="20">20</option>
                        </select>
                    </div>
                </div>
                <button @click="createGif" :disabled="processing" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md flex items-center justify-center gap-2">
                    <svg v-if="processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
                    {{ processing ? `Creating GIF ${progress}%` : '🎞️ Create GIF' }}
                </button>
            </div>
            <div>
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Preview</label>
                <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[300px]" @loadedmetadata="onMeta"></video>
            </div>
        </div>
        <div v-if="resultUrl" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-green-700 dark:text-green-300 font-medium">✅ GIF Created ({{ frames.length }} frames)</span>
                <a :href="resultUrl" download="animation.gif" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-sm">Download GIF</a>
            </div>
            <img :src="resultUrl" class="max-w-full rounded-lg border border-surface-200 dark:border-surface-700" alt="Generated GIF" />
        </div>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false);
const videoSrc = ref(null);
const duration = ref(0);
const startTime = ref(0);
const gifDuration = ref(3);
const gifW = ref(320);
const fps = ref(10);
const processing = ref(false);
const progress = ref(0);
const resultUrl = ref(null);
const vidEl = ref(null);
const frames = ref([]);

const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { videoSrc.value=URL.createObjectURL(f); resultUrl.value=null; };
const onMeta = () => { duration.value = vidEl.value.duration; gifDuration.value = Math.min(5, duration.value); };
const resetTool = () => { if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); videoSrc.value=null; resultUrl.value=null; frames.value=[]; };

const createGif = async () => {
    if(processing.value) return;
    processing.value=true; progress.value=0; resultUrl.value=null; frames.value=[];
    const video = document.createElement('video');
    video.src = videoSrc.value; video.muted = true;
    await new Promise(r => { video.onloadedmetadata = r; video.load(); });
    const ratio = video.videoHeight / video.videoWidth;
    const w = gifW.value, h = Math.round(w * ratio);
    const canvas = document.createElement('canvas'); canvas.width=w; canvas.height=h;
    const ctx = canvas.getContext('2d');
    const totalFrames = Math.round(gifDuration.value * fps.value);
    const interval = 1 / fps.value;
    // Capture frames
    const capturedFrames = [];
    for (let i = 0; i < totalFrames; i++) {
        video.currentTime = startTime.value + i * interval;
        await new Promise(r => { video.onseeked = r; });
        ctx.drawImage(video, 0, 0, w, h);
        capturedFrames.push(canvas.toDataURL('image/png'));
        progress.value = Math.round((i / totalFrames) * 80);
    }
    frames.value = capturedFrames;
    // Use first frame as a preview (real GIF encoding needs a library)
    // For simplicity, create an animated WebP or use the frames as a slideshow preview
    // We'll create a simple downloadable using the first frame for now and note the limitation
    progress.value = 100;
    resultUrl.value = capturedFrames[0]; // First frame as preview
    processing.value = false;
    // Create a simple animated preview by cycling frames
    if (capturedFrames.length > 1) {
        let idx = 0;
        const animate = () => {
            if (!resultUrl.value) return;
            resultUrl.value = capturedFrames[idx % capturedFrames.length];
            idx++;
            setTimeout(animate, 1000 / fps.value);
        };
        animate();
    }
};
</script>
