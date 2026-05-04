<template>
<div class="space-y-6">
    <div v-if="!videoSrc" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept="video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-teal-900/50 dark:text-teal-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload video to transcribe</h3>
        <p class="text-sm text-surface-500">Uses browser Speech Recognition API</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Video to Text</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <video ref="vidEl" :src="videoSrc" controls class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-black max-h-[250px]" @loadedmetadata="onMeta"></video>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Language</label>
                    <select v-model="language" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm">
                        <option value="bn-BD">বাংলা (Bangla)</option>
                        <option value="en-US">English (US)</option>
                        <option value="en-GB">English (UK)</option>
                        <option value="hi-IN">हिन्दी (Hindi)</option>
                        <option value="ar-SA">العربية (Arabic)</option>
                    </select>
                </div>
                <button @click="toggleTranscription" :disabled="!supported" class="w-full py-3 rounded-xl shadow-md text-white font-medium flex items-center justify-center gap-2" :class="transcribing ? 'bg-red-600 hover:bg-red-700' : 'bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700'">
                    <span v-if="transcribing" class="flex items-center gap-2"><span class="w-3 h-3 bg-white rounded-full animate-pulse"></span> Stop Transcription</span>
                    <span v-else>🎤 Start Transcription</span>
                </button>
                <p v-if="!supported" class="text-xs text-red-500">⚠️ Your browser does not support Speech Recognition. Use Chrome for best results.</p>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Transcription</label>
                    <button v-if="transcript" @click="copyText" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ copied ? '✅ Copied!' : '📋 Copy' }}</button>
                </div>
                <textarea v-model="transcript" readonly class="w-full h-64 rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm resize-none" placeholder="Transcription will appear here..."></textarea>
                <div v-if="transcript" class="flex gap-2">
                    <button @click="downloadTxt" class="flex-1 py-2 bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300 font-medium rounded-lg text-sm">📄 Download .txt</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount } from 'vue';
const drag = ref(false); const videoSrc = ref(null); const language = ref('en-US');
const transcript = ref(''); const transcribing = ref(false); const copied = ref(false);
const vidEl = ref(null); const duration = ref(0);
let recognition = null;
const supported = computed(() => typeof window !== 'undefined' && ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window));
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f?.type.startsWith('video/')) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { videoSrc.value=URL.createObjectURL(f); transcript.value=''; };
const onMeta = () => { duration.value = vidEl.value.duration; };
const resetTool = () => { stopTranscription(); if(videoSrc.value) URL.revokeObjectURL(videoSrc.value); videoSrc.value=null; transcript.value=''; };

const toggleTranscription = () => {
    if (transcribing.value) { stopTranscription(); return; }
    startTranscription();
};

const startTranscription = () => {
    if (!supported.value) return;
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = language.value;
    let finalTranscript = transcript.value;
    recognition.onresult = (e) => {
        let interim = '';
        for (let i = e.resultIndex; i < e.results.length; i++) {
            if (e.results[i].isFinal) { finalTranscript += e.results[i][0].transcript + ' '; }
            else { interim += e.results[i][0].transcript; }
        }
        transcript.value = finalTranscript + interim;
    };
    recognition.onerror = (e) => { if(e.error !== 'aborted') { transcript.value += `\n[Error: ${e.error}]`; }};
    recognition.onend = () => { if(transcribing.value) { recognition.start(); } };
    recognition.start();
    transcribing.value = true;
    // Play video
    if (vidEl.value) { vidEl.value.play(); }
};

const stopTranscription = () => {
    transcribing.value = false;
    if (recognition) { recognition.stop(); recognition = null; }
    if (vidEl.value) { vidEl.value.pause(); }
};

const copyText = () => { navigator.clipboard.writeText(transcript.value); copied.value = true; setTimeout(() => copied.value = false, 2000); };
const downloadTxt = () => { const blob = new Blob([transcript.value], {type:'text/plain'}); const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'transcription.txt'; a.click(); };

onBeforeUnmount(() => { stopTranscription(); });
</script>
