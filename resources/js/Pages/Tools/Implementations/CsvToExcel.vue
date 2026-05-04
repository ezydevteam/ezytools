<template>
<div class="space-y-6">
    <div v-if="!csvData" class="border-2 border-dashed border-surface-300 dark:border-surface-600 rounded-2xl p-12 text-center hover:bg-surface-50 dark:hover:bg-surface-900/50 transition-colors cursor-pointer relative" @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="onDrop" :class="{'bg-primary-50 dark:bg-primary-900/20 border-primary-400':drag}">
        <input type="file" @change="onFile" accept=".csv,text/csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
        <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 dark:bg-emerald-900/50 dark:text-emerald-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-1">Upload CSV to convert to Excel</h3>
        <p class="text-sm text-surface-500">Drag & drop or click to select .csv file</p>
    </div>
    <div v-else class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white">CSV → Excel</h3>
            <button @click="resetTool" class="text-sm text-red-500 hover:text-red-600 font-medium">Start Over</button>
        </div>
        <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-2">
            <div class="flex justify-between text-sm"><span class="text-surface-500">File:</span><span class="font-medium text-surface-900 dark:text-white truncate ml-2">{{ fileName }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-surface-500">Rows:</span><span class="font-medium text-surface-900 dark:text-white">{{ rows.length - 1 }} (+ header)</span></div>
            <div class="flex justify-between text-sm"><span class="text-surface-500">Columns:</span><span class="font-medium text-surface-900 dark:text-white">{{ rows[0]?.length || 0 }}</span></div>
        </div>
        <!-- Preview table -->
        <div class="overflow-x-auto rounded-xl border border-surface-200 dark:border-surface-700">
            <table class="w-full text-sm">
                <thead class="bg-surface-100 dark:bg-surface-800"><tr><th v-for="(h,i) in rows[0]" :key="i" class="px-3 py-2 text-left font-medium text-surface-700 dark:text-surface-300 border-b border-surface-200 dark:border-surface-700 truncate max-w-[200px]">{{ h }}</th></tr></thead>
                <tbody><tr v-for="(row,ri) in rows.slice(1,11)" :key="ri" class="border-b border-surface-100 dark:border-surface-800"><td v-for="(c,ci) in row" :key="ci" class="px-3 py-1.5 text-surface-600 dark:text-surface-400 truncate max-w-[200px]">{{ c }}</td></tr></tbody>
            </table>
            <p v-if="rows.length>11" class="text-xs text-surface-500 text-center py-2">Showing first 10 of {{ rows.length-1 }} rows...</p>
        </div>
        <button @click="convertToExcel" class="w-full py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download as Excel (.xlsx)</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const drag = ref(false); const csvData = ref(null); const fileName = ref(''); const rows = ref([]);
const onDrop = (e) => { drag.value=false; const f=e.dataTransfer.files[0]; if(f) loadFile(f); };
const onFile = (e) => { const f=e.target.files[0]; if(f) loadFile(f); };
const loadFile = (f) => { fileName.value=f.name; const r=new FileReader(); r.onload=(e)=>{ csvData.value=e.target.result; parseCSV(e.target.result); }; r.readAsText(f); };
const resetTool = () => { csvData.value=null; rows.value=[]; };
const parseCSV = (text) => {
    const lines = []; let current = []; let field = ''; let inQuotes = false;
    for (let i = 0; i < text.length; i++) {
        const c = text[i];
        if (inQuotes) { if (c === '"' && text[i+1] === '"') { field += '"'; i++; } else if (c === '"') { inQuotes = false; } else { field += c; } }
        else { if (c === '"') { inQuotes = true; } else if (c === ',') { current.push(field); field = ''; } else if (c === '\n' || (c === '\r' && text[i+1] === '\n')) { current.push(field); field = ''; lines.push(current); current = []; if (c === '\r') i++; } else { field += c; } }
    }
    if (field || current.length) { current.push(field); lines.push(current); }
    rows.value = lines.filter(r => r.some(c => c.trim()));
};
const convertToExcel = async () => {
    const XLSX = await loadXLSX();
    const ws = XLSX.utils.aoa_to_sheet(rows.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, fileName.value.replace(/\.csv$/i, '') + '.xlsx');
};
const loadXLSX = () => new Promise((resolve) => {
    if (window.XLSX) return resolve(window.XLSX);
    const s = document.createElement('script');
    s.src = 'https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js';
    s.onload = () => resolve(window.XLSX);
    document.head.appendChild(s);
});
</script>
