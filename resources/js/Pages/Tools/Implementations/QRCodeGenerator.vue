<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Input Area -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-surface-900 dark:text-white mb-2">Text or URL to encode</label>
                    <textarea 
                        v-model="inputText" 
                        class="block w-full p-4 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-sm resize-none h-[120px]" 
                        placeholder="https://example.com or any text..."
                    ></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Size</label>
                        <select v-model="size" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <option value="150x150">Small (150x150)</option>
                            <option value="250x250">Medium (250x250)</option>
                            <option value="350x350">Large (350x350)</option>
                            <option value="500x500">Extra Large (500x500)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Margin (px)</label>
                        <input type="number" v-model="margin" min="0" max="50" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Format</label>
                    <div class="flex p-1 bg-surface-100 dark:bg-surface-900 rounded-xl">
                        <button @click="format = 'png'" :class="[format === 'png' ? 'bg-white dark:bg-surface-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-surface-600 dark:text-surface-400']" class="flex-1 py-1.5 text-sm font-medium rounded-lg transition-all">PNG</button>
                        <button @click="format = 'svg'" :class="[format === 'svg' ? 'bg-white dark:bg-surface-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-surface-600 dark:text-surface-400']" class="flex-1 py-1.5 text-sm font-medium rounded-lg transition-all">SVG</button>
                    </div>
                </div>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full bg-surface-50 dark:bg-surface-900 rounded-2xl border border-surface-200 dark:border-surface-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Generated QR Code</label>
                </div>
                
                <div class="flex-1 flex flex-col items-center justify-center min-h-[250px]">
                    <div v-if="inputText" class="flex flex-col items-center gap-6 w-full">
                        <div class="p-4 bg-white rounded-xl shadow-sm inline-block">
                            <img :src="qrUrl" alt="QR Code" class="max-w-full h-auto" :style="{ width: displaySize + 'px', height: displaySize + 'px' }">
                        </div>
                        
                        <div class="flex gap-3 w-full max-w-xs">
                            <a :href="qrUrl" download="qrcode" target="_blank" class="flex-1 py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl transition-colors shadow-md text-sm text-center flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Download
                            </a>
                        </div>
                    </div>
                    <div v-else class="text-surface-500 dark:text-surface-400 flex flex-col items-center text-center">
                        <svg class="w-16 h-16 mb-4 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        <p>Type something to generate your QR Code instantly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const inputText = ref('');
const size = ref('250x250');
const margin = ref(10);
const format = ref('png');

const displaySize = computed(() => {
    return parseInt(size.value.split('x')[0]);
});

const qrUrl = computed(() => {
    if (!inputText.value) return '';
    return `https://api.qrserver.com/v1/create-qr-code/?size=${size.value}&data=${encodeURIComponent(inputText.value)}&margin=${margin.value}&format=${format.value}`;
});
</script>
