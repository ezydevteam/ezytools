<template>
<div class="space-y-6">
    <div class="space-y-3">
        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">JSON Input</label>
        <div class="flex gap-2 mb-1">
            <button @click="pasteClipboard" class="text-xs text-primary-600 hover:text-primary-700 font-medium">📋 Paste</button>
            <label class="text-xs text-primary-600 hover:text-primary-700 font-medium cursor-pointer">📁 Upload File<input type="file" @change="onFile" accept=".json" class="hidden" /></label>
        </div>
        <textarea v-model="jsonInput" rows="12" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none" placeholder='[{"name":"John","age":30,"city":"Dhaka"}]'></textarea>
    </div>
    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ error }}</div>
    <div v-if="rows.length" class="space-y-3">
        <div class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl border border-surface-200 dark:border-surface-700 space-y-1">
            <div class="flex justify-between text-sm"><span class="text-surface-500">Rows:</span><span class="font-medium text-surface-900 dark:text-white">{{ rows.length - 1 }}</span></div>
            <div class="flex justify-between text-sm"><span class="text-surface-500">Columns:</span><span class="font-medium text-surface-900 dark:text-white">{{ rows[0]?.length || 0 }}</span></div>
        </div>
        <div class="overflow-x-auto rounded-xl border border-surface-200 dark:border-surface-700">
            <table class="w-full text-sm">
                <thead class="bg-surface-100 dark:bg-surface-800"><tr><th v-for="(h,i) in rows[0]" :key="i" class="px-3 py-2 text-left font-medium text-surface-700 dark:text-surface-300 border-b border-surface-200 dark:border-surface-700 truncate max-w-[200px]">{{ h }}</th></tr></thead>
                <tbody><tr v-for="(row,ri) in rows.slice(1,11)" :key="ri" class="border-b border-surface-100 dark:border-surface-800"><td v-for="(c,ci) in row" :key="ci" class="px-3 py-1.5 text-surface-600 dark:text-surface-400 truncate max-w-[200px]">{{ c }}</td></tr></tbody>
            </table>
        </div>
    </div>
    <div class="flex gap-3">
        <button @click="convert" :disabled="!jsonInput.trim()" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md">🔄 Convert</button>
        <button v-if="rows.length" @click="downloadExcel" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download .xlsx</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const jsonInput = ref(''); const rows = ref([]); const error = ref('');
const onFile = (e) => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=(ev)=>{ jsonInput.value=ev.target.result; }; r.readAsText(f); }};
const pasteClipboard = async () => { try { jsonInput.value = await navigator.clipboard.readText(); } catch(e){} };
const flattenObj = (obj, prefix = '') => {
    const res = {};
    for (const [k, v] of Object.entries(obj)) {
        const key = prefix ? `${prefix}.${k}` : k;
        if (v && typeof v === 'object' && !Array.isArray(v)) Object.assign(res, flattenObj(v, key));
        else res[key] = Array.isArray(v) ? JSON.stringify(v) : String(v ?? '');
    }
    return res;
};
const convert = () => {
    error.value = ''; rows.value = [];
    try {
        let data = JSON.parse(jsonInput.value);
        if (!Array.isArray(data)) { if (typeof data === 'object') data = [data]; else throw new Error('Must be array or object'); }
        const flat = data.map(item => flattenObj(item));
        const headers = [...new Set(flat.flatMap(Object.keys))];
        rows.value = [headers, ...flat.map(obj => headers.map(h => obj[h] || ''))];
    } catch (e) { error.value = 'Invalid JSON: ' + e.message; }
};
const downloadExcel = async () => {
    const XLSX = await loadXLSX();
    const ws = XLSX.utils.aoa_to_sheet(rows.value);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
    XLSX.writeFile(wb, 'json-converted.xlsx');
};
const loadXLSX = () => new Promise((resolve) => {
    if (window.XLSX) return resolve(window.XLSX);
    const s = document.createElement('script'); s.src = 'https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js'; s.onload = () => resolve(window.XLSX); document.head.appendChild(s);
});
</script>
