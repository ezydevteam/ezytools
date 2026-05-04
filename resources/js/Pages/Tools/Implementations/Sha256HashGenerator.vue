<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="grid grid-cols-1 gap-6">
            <!-- Input Area -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Text to Hash</label>
                    <button @click="inputText = ''" class="text-xs text-surface-500 hover:text-red-500 transition-colors">Clear</button>
                </div>
                <textarea 
                    v-model="inputText" 
                    class="block w-full p-4 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 font-mono text-sm resize-y min-h-[120px]" 
                    placeholder="Enter string here..."
                ></textarea>
            </div>

            <!-- Output Area -->
            <div>
                <label class="block text-sm font-bold text-surface-900 dark:text-white mb-2">SHA-256 Hash Result</label>
                <div class="flex items-center gap-2">
                    <div class="flex-1 p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-100/50 dark:bg-surface-800/50 overflow-x-auto">
                        <p class="font-mono text-sm break-all text-surface-900 dark:text-white select-all">
                            {{ hashResult || 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855' }}
                        </p>
                    </div>
                    <button @click="copyToClipboard" class="p-4 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 rounded-xl hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors" title="Copy to clipboard">
                        <svg v-if="copied" class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30 text-sm text-blue-800 dark:text-blue-300">
            <p><strong>What is SHA-256?</strong> SHA-256 is a cryptographic hash function that produces a 256-bit (32-byte) signature. It is significantly more secure than MD5 and is widely used for data integrity verification, digital signatures, and blockchain technologies.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const inputText = ref('');
const hashResult = ref('');
const copied = ref(false);

const generateSHA256 = async (str) => {
    // encode as UTF-8
    const msgBuffer = new TextEncoder().encode(str);
    // hash the message
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    // convert ArrayBuffer to Array
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    // convert bytes to hex string
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex;
};

// Use watcher for async operation
watch(inputText, async (newVal) => {
    if (!newVal) {
        hashResult.value = '';
        return;
    }
    hashResult.value = await generateSHA256(newVal);
}, { immediate: true });

const copyToClipboard = async () => {
    const textToCopy = hashResult.value || 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';
    try {
        await navigator.clipboard.writeText(textToCopy);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};
</script>
