<template>
<div class="space-y-6">
    <div v-if="!loaded" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept=".xlsx,.xls,.ods" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-emerald-900/50 dark:text-emerald-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload Excel to convert to CSV</h3>
        <p class="text-sm text-surface-500">Supports .xlsx, .xls, .ods files</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">Excel → CSV</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div v-if="sheets.length > 1">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Select Sheet</label>
            <select v-model="activeSheet" @change="parseSheet" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm">
                <option v-for="s in sheets" :key="s" :value="s">{{ s }}</option>
            </select>
        </div>
        <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-surface-500">File:</span><span class="font-medium text-surface-900 dark:text-white truncate ml-2">{{ fileName }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-surface-500">Sheets:</span><span class="font-medium text-surface-900 dark:text-white">{{ sheets.length }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-surface-500">Rows:</span><span class="font-medium text-surface-900 dark:text-white">{{ rows.length }}</span></div>
        </div>
        <div class="overflow-x-auto rounded-xl border border-surface-200 dark:border-surface-700">
            <table class="w-full text-sm">
                <thead class="bg-surface-100 dark:bg-surface-800"><tr><th v-for="(h,i) in rows[0]" :key="i" class="px-3 py-2 text-left font-medium text-surface-700 dark:text-surface-300 border-b border-surface-200 dark:border-surface-700 truncate max-w-[200px]">{{ h }}</th></tr></thead>
                <tbody><tr v-for="(row,ri) in rows.slice(1,11)" :key="ri" class="border-b border-surface-100 dark:border-surface-800"><td v-for="(c,ci) in row" :key="ci" class="px-3 py-1.5 text-surface-600 dark:text-surface-400 truncate max-w-[200px]">{{ c }}</td></tr></tbody>
            </table>
            <p v-if="rows.length>11" class="text-xs text-surface-500 text-center py-2">Showing first 10 rows...</p>
        </div>
        <button @click="downloadCSV" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download as CSV</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false); const loaded = ref(false); const fileName = ref(''); const sheets = ref([]); const activeSheet = ref(''); const rows = ref([]); let workbook = null;
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const resetTool = () => { loaded.value=false; rows.value=[]; sheets.value=[]; workbook=null; };
const loadFile = async (f) => {
    fileName.value = f.name;
    const XLSX = await loadXLSX();
    const buf = await f.arrayBuffer();
    workbook = XLSX.read(buf);
    sheets.value = workbook.SheetNames;
    activeSheet.value = sheets.value[0];
    parseSheet();
    loaded.value = true;
};
const parseSheet = () => {
    if (!workbook) return;
    const XLSX = window.XLSX;
    const ws = workbook.Sheets[activeSheet.value];
    rows.value = XLSX.utils.sheet_to_json(ws, { header: 1 });
};
const downloadCSV = () => {
    const XLSX = window.XLSX;
    const ws = workbook.Sheets[activeSheet.value];
    const csv = XLSX.utils.sheet_to_csv(ws);
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = fileName.value.replace(/\.[^.]+$/, '') + '.csv'; a.click();
};
const loadXLSX = () => new Promise((resolve) => {
    if (window.XLSX) return resolve(window.XLSX);
    const s = document.createElement('script'); s.src = 'https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js'; s.onload = () => resolve(window.XLSX); document.head.appendChild(s);
});
</script>
