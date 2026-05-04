<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Input Area -->
            <div class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Barcode Value / Data</label>
                    <input type="text" v-model="value" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 text-lg" placeholder="e.g. 123456789012">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Format</label>
                        <select v-model="format" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="CODE128">CODE128 (Standard)</option>
                            <option value="CODE39">CODE39</option>
                            <option value="EAN13">EAN-13 (Product Barcode)</option>
                            <option value="EAN8">EAN-8</option>
                            <option value="UPC">UPC</option>
                            <option value="ITF14">ITF-14</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Color</label>
                        <input type="color" v-model="lineColor" class="block w-full h-10 rounded-xl cursor-pointer border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 p-1">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Width</label>
                        <input type="range" v-model.number="width" min="1" max="4" step="1" class="w-full accent-primary-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Height</label>
                        <input type="range" v-model.number="height" min="20" max="150" step="10" class="w-full accent-primary-600">
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer mt-2">
                        <input type="checkbox" v-model="displayValue" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                        <span class="text-sm text-surface-700 dark:text-surface-300">Show Value Text Below Barcode</span>
                    </label>
                </div>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Generated Barcode</label>
                    <button @click="downloadBarcode" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300" :disabled="!value">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download PNG
                    </button>
                </div>
                
                <div class="flex-1 w-full p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 flex items-center justify-center min-h-[250px] overflow-hidden">
                    <img v-if="barcodeUrl" :src="barcodeUrl" alt="Barcode" class="max-w-full mix-blend-multiply dark:mix-blend-normal dark:bg-white dark:p-4 dark:rounded-lg">
                    <div v-else class="text-surface-400 text-sm text-center">
                        <svg class="w-12 h-12 mx-auto mb-2 text-surface-300 dark:text-surface-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        Enter a value to generate a barcode
                    </div>
                </div>
                <p v-if="errorMsg" class="mt-2 text-xs text-red-500">{{ errorMsg }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const value = ref('123456789012');
const format = ref('CODE128');
const lineColor = ref('#000000');
const width = ref(2);
const height = ref(100);
const displayValue = ref(true);
const barcodeUrl = ref('');
const errorMsg = ref('');

// Dynamically load JsBarcode
const loadJsBarcode = () => {
    return new Promise((resolve, reject) => {
        if (window.JsBarcode) {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
};

const generateBarcode = () => {
    if (!value.value) {
        barcodeUrl.value = '';
        return;
    }
    
    errorMsg.value = '';
    
    try {
        const canvas = document.createElement('canvas');
        window.JsBarcode(canvas, value.value, {
            format: format.value,
            lineColor: lineColor.value,
            width: width.value,
            height: height.value,
            displayValue: displayValue.value,
            background: '#ffffff',
            margin: 10,
            valid: (valid) => {
                if (!valid) {
                    errorMsg.value = `Invalid value for format ${format.value}. Please check your input.`;
                }
            }
        });
        
        if (!errorMsg.value) {
            barcodeUrl.value = canvas.toDataURL('image/png');
        } else {
            barcodeUrl.value = '';
        }
    } catch (e) {
        errorMsg.value = 'Failed to generate barcode. ' + e.message;
        barcodeUrl.value = '';
    }
};

watch([value, format, lineColor, width, height, displayValue], () => {
    if (window.JsBarcode) generateBarcode();
});

onMounted(() => {
    loadJsBarcode().then(() => {
        generateBarcode();
    }).catch(err => {
        console.error("Failed to load JsBarcode", err);
        errorMsg.value = "Failed to load Barcode library. Please check your connection.";
    });
});

const downloadBarcode = () => {
    if (!barcodeUrl.value) return;
    const a = document.createElement('a');
    a.href = barcodeUrl.value;
    a.download = `barcode-${value.value}.png`;
    a.click();
};
</script>
