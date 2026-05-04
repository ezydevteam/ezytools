<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full min-h-[500px]">
            <!-- Input Area -->
            <div class="flex flex-col h-full space-y-3">
                <div class="flex justify-between items-center">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Input CSS</label>
                    <div class="flex gap-2">
                        <button @click="minifyCss" class="text-xs px-3 py-1.5 bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 rounded-lg font-medium hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-colors">Minify</button>
                        <button @click="clearInput" class="text-xs px-3 py-1.5 text-surface-500 hover:text-red-500 transition-colors">Clear</button>
                    </div>
                </div>
                
                <textarea 
                    v-model="inputCss" 
                    class="flex-1 w-full p-4 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 font-mono text-sm resize-none" 
                    placeholder="/* Paste your CSS here */&#10;body {&#10;  background-color: #fff;&#10;  color: #333;&#10;}"
                    spellcheck="false"
                ></textarea>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full space-y-3">
                <div class="flex justify-between items-center">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Minified CSS</label>
                    <div class="flex items-center gap-3">
                        <span v-if="savingsText" class="text-xs font-bold text-green-600 dark:text-green-400">{{ savingsText }}</span>
                        <button @click="copyToClipboard" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600" :class="copied ? 'text-green-500' : 'text-surface-700 dark:text-surface-300'" :disabled="!outputCss">
                            <svg v-if="copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            {{ copied ? 'Copied!' : 'Copy' }}
                        </button>
                    </div>
                </div>
                
                <div class="flex-1 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-[#1e1e1e] text-[#d4d4d4] overflow-hidden relative group">
                    <textarea 
                        v-model="outputCss" 
                        readonly
                        class="absolute inset-0 w-full h-full p-4 bg-transparent border-none focus:ring-0 font-mono text-sm resize-none" 
                        placeholder="Minified CSS will appear here..."
                        spellcheck="false"
                    ></textarea>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const inputCss = ref('');
const outputCss = ref('');
const copied = ref(false);
const savingsText = ref('');

const minifyCss = () => {
    if (!inputCss.value.trim()) {
        outputCss.value = '';
        savingsText.value = '';
        return;
    }
    
    const originalLength = inputCss.value.length;
    
    // Basic CSS Minification
    let minified = inputCss.value;
    
    // Remove comments
    minified = minified.replace(/\/\*[\s\S]*?\*\//g, '');
    
    // Remove extra whitespace
    minified = minified.replace(/\s+/g, ' ');
    
    // Remove whitespace around specific characters
    minified = minified.replace(/\s*([{}:;,>])\s*/g, '$1');
    
    // Remove trailing semicolons
    minified = minified.replace(/;}/g, '}');

    outputCss.value = minified.trim();
    
    const newLength = outputCss.value.length;
    if (originalLength > 0 && newLength < originalLength) {
        const saved = ((originalLength - newLength) / originalLength * 100).toFixed(1);
        savingsText.value = `Saved ${saved}%`;
    } else {
        savingsText.value = '';
    }
};

const clearInput = () => {
    inputCss.value = '';
    outputCss.value = '';
    savingsText.value = '';
};

const copyToClipboard = async () => {
    if (!outputCss.value) return;
    try {
        await navigator.clipboard.writeText(outputCss.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};
</script>
