<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Picker & Preview -->
            <div class="space-y-6">
                
                <div class="flex justify-center">
                    <div 
                        class="w-full h-48 rounded-2xl shadow-inner border border-surface-200 dark:border-surface-700 transition-colors duration-200"
                        :style="{ backgroundColor: hexColor }"
                    ></div>
                </div>

                <div class="flex items-center justify-center gap-4">
                    <input type="color" v-model="hexColor" class="w-16 h-16 rounded cursor-pointer border-0 p-0 bg-transparent">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Pick a Color</label>
                        <p class="text-xs text-surface-500">Click the color box or edit the values manually.</p>
                    </div>
                </div>
            </div>

            <!-- Values -->
            <div class="space-y-4">
                
                <!-- HEX -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">HEX</label>
                        <button @click="copy(hexColor)" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Copy</button>
                    </div>
                    <input type="text" v-model="hexColor" class="w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500 uppercase font-mono">
                </div>

                <!-- RGB -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">RGB</label>
                        <button @click="copy(rgbString)" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Copy</button>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 flex items-center border border-surface-300 dark:border-surface-600 rounded-xl overflow-hidden focus-within:ring-1 focus-within:ring-primary-500 focus-within:border-primary-500">
                            <span class="px-3 text-surface-500 font-bold bg-surface-100 dark:bg-surface-800 border-r border-surface-300 dark:border-surface-600">R</span>
                            <input type="number" min="0" max="255" v-model.number="r" class="w-full border-none focus:ring-0 bg-surface-50 dark:bg-surface-900 font-mono">
                        </div>
                        <div class="flex-1 flex items-center border border-surface-300 dark:border-surface-600 rounded-xl overflow-hidden focus-within:ring-1 focus-within:ring-primary-500 focus-within:border-primary-500">
                            <span class="px-3 text-surface-500 font-bold bg-surface-100 dark:bg-surface-800 border-r border-surface-300 dark:border-surface-600">G</span>
                            <input type="number" min="0" max="255" v-model.number="g" class="w-full border-none focus:ring-0 bg-surface-50 dark:bg-surface-900 font-mono">
                        </div>
                        <div class="flex-1 flex items-center border border-surface-300 dark:border-surface-600 rounded-xl overflow-hidden focus-within:ring-1 focus-within:ring-primary-500 focus-within:border-primary-500">
                            <span class="px-3 text-surface-500 font-bold bg-surface-100 dark:bg-surface-800 border-r border-surface-300 dark:border-surface-600">B</span>
                            <input type="number" min="0" max="255" v-model.number="b" class="w-full border-none focus:ring-0 bg-surface-50 dark:bg-surface-900 font-mono">
                        </div>
                    </div>
                </div>

                <!-- HSL -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">HSL</label>
                        <button @click="copy(hslString)" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Copy</button>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" :value="hslString" readonly class="w-full rounded-xl border-transparent bg-surface-100 dark:bg-surface-900 text-surface-700 dark:text-surface-300 focus:ring-0 font-mono cursor-default">
                    </div>
                </div>
                
                <!-- CMYK -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-bold text-surface-900 dark:text-white">CMYK</label>
                        <button @click="copy(cmykString)" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Copy</button>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" :value="cmykString" readonly class="w-full rounded-xl border-transparent bg-surface-100 dark:bg-surface-900 text-surface-700 dark:text-surface-300 focus:ring-0 font-mono cursor-default">
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const hexColor = ref('#4F46E5'); // Default primary indigo
const r = ref(79);
const g = ref(70);
const b = ref(229);

// Sync Hex to RGB
watch(hexColor, (newHex) => {
    if (/^#[0-9A-F]{6}$/i.test(newHex)) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(newHex);
        if (result) {
            r.value = parseInt(result[1], 16);
            g.value = parseInt(result[2], 16);
            b.value = parseInt(result[3], 16);
        }
    }
});

// Sync RGB to Hex
const updateFromRgb = () => {
    const clamp = (val) => Math.max(0, Math.min(255, val || 0));
    r.value = clamp(r.value);
    g.value = clamp(g.value);
    b.value = clamp(b.value);
    
    hexColor.value = '#' + [r.value, g.value, b.value].map(x => {
        const hex = x.toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    }).join('').toUpperCase();
};

watch([r, g, b], () => {
    updateFromRgb();
});

const rgbString = computed(() => `rgb(${r.value}, ${g.value}, ${b.value})`);

const hslString = computed(() => {
    let rVal = r.value / 255;
    let gVal = g.value / 255;
    let bVal = b.value / 255;
    
    let max = Math.max(rVal, gVal, bVal), min = Math.min(rVal, gVal, bVal);
    let h, s, l = (max + min) / 2;

    if(max == min){
        h = s = 0; // achromatic
    } else {
        let d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch(max) {
            case rVal: h = (gVal - bVal) / d + (gVal < bVal ? 6 : 0); break;
            case gVal: h = (bVal - rVal) / d + 2; break;
            case bVal: h = (rVal - gVal) / d + 4; break;
        }
        h /= 6;
    }

    return `hsl(${Math.round(h * 360)}, ${Math.round(s * 100)}%, ${Math.round(l * 100)}%)`;
});

const cmykString = computed(() => {
    let c = 1 - (r.value / 255);
    let m = 1 - (g.value / 255);
    let y = 1 - (b.value / 255);
    let k = Math.min(c, Math.min(m, y));
    
    if (k === 1) {
        c = m = y = 0;
    } else {
        c = (c - k) / (1 - k);
        m = (m - k) / (1 - k);
        y = (y - k) / (1 - k);
    }

    return `cmyk(${Math.round(c * 100)}%, ${Math.round(m * 100)}%, ${Math.round(y * 100)}%, ${Math.round(k * 100)}%)`;
});

const copy = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        alert('Copied: ' + text);
    } catch (err) {
        console.error('Failed to copy', err);
    }
};
</script>
