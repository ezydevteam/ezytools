<template>
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6 h-full min-h-[500px]">
        
        <!-- Input -->
        <div class="flex flex-col bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex justify-between items-center">
                <h3 class="font-semibold text-surface-900 dark:text-white">Unicode Text (Avro/Bangla)</h3>
                <div class="flex gap-2">
                    <button @click="unicodeText = ''" class="text-xs text-surface-500 hover:text-red-500 font-medium">Clear</button>
                    <button @click="pasteFromClipboard" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Paste</button>
                </div>
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <textarea 
                    v-model="unicodeText" 
                    placeholder="Type or paste standard Unicode Bangla here..." 
                    class="flex-1 w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700 dark:text-white resize-none text-xl"
                ></textarea>
            </div>
        </div>

        <!-- Output -->
        <div class="flex flex-col bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex justify-between items-center">
                <h3 class="font-semibold text-surface-900 dark:text-white">Bijoy Classic Output</h3>
                <div class="flex gap-2">
                    <button @click="copyOutput" class="text-xs bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white px-3 py-1 rounded font-medium transition-colors shadow-sm">Copy Result</button>
                </div>
            </div>
            <div class="flex-1 relative bg-surface-50 dark:bg-surface-900/50">
                <textarea 
                    readonly
                    :value="bijoyResult" 
                    placeholder="Converted Bijoy text will appear here..." 
                    class="absolute inset-0 w-full h-full border-none bg-transparent focus:ring-0 dark:text-white resize-none p-4 font-sutonny text-xl"
                ></textarea>
            </div>
        </div>
        
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const unicodeText = ref('');

const convertUnicodeToBijoy = (srcString) => {
    if (!srcString) return '';

    let text = srcString;
    
    // Unicode to Bijoy Post-Conversion Re-arrangements
    // e.g., move e-kar before the consonant
    text = text.replace(/([ক-হড়ঢ়য়])([েৈ])/g, "$2$1"); 
    
    // Core Reverse Map
    const charMap = {
        '০':'0', '১':'1', '২':'2', '৩':'3', '৪':'4', '৫':'5', '৬':'6', '৭':'7', '৮':'8', '৯':'9',
        'ৃ':'a', 'র্':'A', 'ন':'b', 'ণ':'B', 'ে':'c', 'ৈ':'C', 'ি':'d', 'ী':'D',
        'ড':'e', 'ঢ':'E', 'া':'f', 'অ':'F', 'হ':'g', 'ঞ':'G', 'ব':'h', 'ভ':'H',
        'ক':'j', 'খ':'J', 'ত':'k', 'থ':'K', 'দ':'l', 'ধ':'L',
        'ম':'m', 'শ':'M', 'স':'n', 'ষ':'N', 'গ':'o', 'ঘ':'O', 'ড়':'p', 'ঢ়':'P',
        'ঙ':'q', 'ং':'Q', 'প':'r', 'ফ':'R', 'ু':'s', 'ূ':'S', 'ট':'t', 'ঠ':'T',
        'জ':'u', 'ঝ':'U', 'র':'v', 'ল':'V', 'য':'w', 'য়':'W', 'ও':'x', 'ঔ':'X',
        'চ':'y', 'ছ':'Y', '্র':'z', '্য':'Z'
        // Add more standard mapping pairs
    };

    let converted = '';
    for (let i = 0; i < text.length; i++) {
        const char = text[i];
        if (charMap[char]) {
            converted += charMap[char];
        } else {
            converted += char;
        }
    }
    
    return converted;
};

const bijoyResult = computed(() => {
    return convertUnicodeToBijoy(unicodeText.value);
});

const copyOutput = () => {
    if (!bijoyResult.value) return;
    navigator.clipboard.writeText(bijoyResult.value).then(() => {
        toast.success("Bijoy text copied!");
    });
};

const pasteFromClipboard = async () => {
    try {
        const text = await navigator.clipboard.readText();
        unicodeText.value = text;
    } catch (err) {
        toast.error("Failed to read from clipboard.");
    }
};
</script>

<style scoped>
@font-face {
    font-family: 'SutonnyMJ';
    src: local('SutonnyMJ');
}
.font-sutonny {
    font-family: 'SutonnyMJ', sans-serif;
}
</style>
