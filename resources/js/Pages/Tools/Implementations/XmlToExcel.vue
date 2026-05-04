<template>
<div class="space-y-6">
    <div v-if="!xmlData" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept=".xml,text/xml,application/xml" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-blue-900/50 dark:text-blue-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload XML to convert to Excel</h3>
        <p class="text-sm text-surface-500">Or paste XML content below</p>
        <textarea v-model="pasteXml" rows="4" class="mt-4 w-full max-w-lg mx-auto rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono" placeholder="Paste XML here..." @input="pasteXml.trim() && loadPaste()"></textarea>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">XML → Excel</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-surface-500">Rows:</span><span class="font-medium text-surface-900 dark:text-white">{{ rows.length - 1 }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-surface-500">Columns:</span><span class="font-medium text-surface-900 dark:text-white">{{ rows[0]?.length || 0 }}</span></div>
        </div>
        <div class="overflow-x-auto rounded-xl border border-surface-200 dark:border-surface-700">
            <table class="w-full text-sm">
                <thead class="bg-surface-100 dark:bg-surface-800"><tr><th v-for="(h,i) in rows[0]" :key="i" class="px-3 py-2 text-left font-medium text-surface-700 dark:text-surface-300 border-b border-surface-200 dark:border-surface-700 truncate max-w-[200px]">{{ h }}</th></tr></thead>
                <tbody><tr v-for="(row,ri) in rows.slice(1,11)" :key="ri" class="border-b border-surface-100 dark:border-surface-800"><td v-for="(c,ci) in row" :key="ci" class="px-3 py-1.5 text-surface-600 dark:text-surface-400 truncate max-w-[200px]">{{ c }}</td></tr></tbody>
            </table>
            <p v-if="rows.length>11" class="text-xs text-surface-500 text-center py-2">Showing first 10 rows...</p>
        </div>
        <button @click="downloadExcel" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download as Excel (.xlsx)</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false); const xmlData = ref(null); const rows = ref([]); const pasteXml = ref('');
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { const r=new FileReader(); r.onload=(ev)=>{ xmlData.value=ev.target.result; parseXML(ev.target.result); }; r.readAsText(f); };
const loadPaste = () => { xmlData.value=pasteXml.value; parseXML(pasteXml.value); };
const resetTool = () => { xmlData.value=null; rows.value=[]; pasteXml.value=''; };
const parseXML = (text) => {
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(text, 'text/xml');
        const items = doc.querySelectorAll(doc.documentElement.tagName + ' > *');
        if (!items.length) return;
        const allKeys = new Set();
        const dataRows = [];
        items.forEach(item => {
            const obj = {};
            // Attributes
            Array.from(item.attributes).forEach(a => { obj['@'+a.name] = a.value; allKeys.add('@'+a.name); });
            // Child elements
            Array.from(item.children).forEach(child => {
                const key = child.tagName;
                obj[key] = child.textContent;
                allKeys.add(key);
            });
            // If no children, use text content
            if (!item.children.length && item.textContent.trim()) { obj['_text'] = item.textContent; allKeys.add('_text'); }
            dataRows.push(obj);
        });
        const headers = Array.from(allKeys);
        rows.value = [headers, ...dataRows.map(obj => headers.map(h => obj[h] || ''))];
    } catch (e) { rows.value = []; }
};
const downloadExcel = async () => {
    const XLSX = await loadXLSX();
    const ws = XLSX.utils.aoa_to_sheet(rows.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, 'xml-converted.xlsx');
};
const loadXLSX = () => new Promise((resolve) => {
    if (window.XLSX) return resolve(window.XLSX);
    const s = document.createElement('script'); s.src = 'https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js'; s.onload = () => resolve(window.XLSX); document.head.appendChild(s);
});
</script>
