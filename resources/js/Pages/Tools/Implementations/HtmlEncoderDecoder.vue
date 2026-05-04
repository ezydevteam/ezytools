<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="mb-6">
            <div class="flex p-1 bg-surface-100 dark:bg-surface-900 rounded-xl max-w-md mx-auto">
                <button @click="mode = 'encode'" :class="[mode === 'encode' ? 'bg-white dark:bg-surface-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white']" class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">Encode HTML</button>
                <button @click="mode = 'decode'" :class="[mode === 'decode' ? 'bg-white dark:bg-surface-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-surface-600 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white']" class="flex-1 py-2 text-sm font-medium rounded-lg transition-all">Decode HTML</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Input Area -->
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">
                        {{ mode === 'encode' ? 'HTML to Encode' : 'HTML Entities to Decode' }}
                    </label>
                    <button @click="inputText = ''" class="text-xs text-surface-500 hover:text-red-500 transition-colors">Clear</button>
                </div>
                <textarea 
                    v-model="inputText" 
                    class="flex-1 w-full p-4 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 font-mono text-sm resize-none min-h-[200px]" 
                    :placeholder="mode === 'encode' ? '<div>Hello & Welcome</div>' : '&lt;div&gt;Hello &amp; Welcome&lt;/div&gt;'"
                ></textarea>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Result</label>
                    <button @click="copyToClipboard" class="text-xs flex items-center gap-1 transition-colors" :class="copied ? 'text-green-500' : 'text-primary-600 hover:text-primary-700 dark:text-primary-400'">
                        <svg v-if="copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                        {{ copied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>
                <div class="flex-1 w-full p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100/50 dark:bg-surface-800/50 overflow-auto min-h-[200px] relative group">
                    <p class="font-mono text-sm whitespace-pre-wrap break-all text-surface-800 dark:text-surface-200">
                        {{ outputText }}
                    </p>
                    <div v-if="!inputText" class="absolute inset-0 flex items-center justify-center text-surface-400 pointer-events-none">
                        Result will appear here...
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const mode = ref('encode');
const inputText = ref('');
const copied = ref(false);

const encodeHTML = (str) => {
    return str.replace(/[\u00A0-\u9999<>\&]/g, function(i) {
        return '&#'+i.charCodeAt(0)+';';
    });
};

const decodeHTML = (str) => {
    const textArea = document.createElement("textarea");
    textArea.innerHTML = str;
    return textArea.value;
};

const outputText = computed(() => {
    if (!inputText.value) return '';
    
    if (mode.value === 'encode') {
        return encodeHTML(inputText.value);
    } else {
        return decodeHTML(inputText.value);
    }
});

// Clear input when switching modes or swap
watch(mode, () => {
    inputText.value = outputText.value || '';
});

const copyToClipboard = async () => {
    if (!outputText.value) return;
    
    try {
        await navigator.clipboard.writeText(outputText.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};
</script>
