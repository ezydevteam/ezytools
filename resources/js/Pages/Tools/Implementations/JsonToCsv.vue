<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">JSON Input</label>
            <div class="flex gap-2 mb-1">
                <button @click="pasteClipboard" class="text-xs text-primary-600 hover:text-primary-700 font-medium">📋 Paste</button>
                <label class="text-xs text-primary-600 hover:text-primary-700 font-medium cursor-pointer">📁 Upload File<input type="file" @change="onFile" accept=".json,application/json" class="hidden" /></label>
            </div>
            <textarea v-model="jsonInput" rows="16" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none" placeholder='[{"name":"John","age":30},{"name":"Jane","age":25}]'></textarea>
        </div>
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">CSV Output</label>
            <div class="flex gap-2 mb-1">
                <button v-if="csvOutput" @click="copyOutput" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ copied ? '✅ Copied' : '📋 Copy' }}</button>
            </div>
            <textarea v-model="csvOutput" rows="16" readonly class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none bg-surface-50 dark:bg-surface-950" placeholder="CSV output will appear here..."></textarea>
        </div>
    </div>
    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ error }}</div>
    <div class="flex gap-3">
        <button @click="convert" :disabled="!jsonInput.trim()" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md">🔄 Convert to CSV</button>
        <button v-if="csvOutput" @click="downloadCSV" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const jsonInput = ref(''); const csvOutput = ref(''); const error = ref(''); const copied = ref(false);
const onFile = (e) => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=(ev)=>{ jsonInput.value=ev.target.result; }; r.readAsText(f); }};
const pasteClipboard = async () => { try { jsonInput.value = await navigator.clipboard.readText(); } catch(e){} };
const copyOutput = () => { navigator.clipboard.writeText(csvOutput.value); copied.value=true; setTimeout(()=>copied.value=false,2000); };
const convert = () => {
    error.value = ''; csvOutput.value = '';
    try {
        let data = JSON.parse(jsonInput.value);
        if (!Array.isArray(data)) { if (typeof data === 'object') data = [data]; else throw new Error('Input must be a JSON array or object'); }
        if (!data.length) throw new Error('Empty array');
        const flat = data.map(item => flattenObj(item));
        const headers = [...new Set(flat.flatMap(Object.keys))];
        const lines = [headers.map(escCSV).join(',')];
        flat.forEach(row => { lines.push(headers.map(h => escCSV(row[h] ?? '')).join(',')); });
        csvOutput.value = lines.join('\n');
    } catch (e) { error.value = 'Invalid JSON: ' + e.message; }
};
const flattenObj = (obj, prefix = '') => {
    const res = {};
    for (const [k, v] of Object.entries(obj)) {
        const key = prefix ? `${prefix}.${k}` : k;
        if (v && typeof v === 'object' && !Array.isArray(v)) { Object.assign(res, flattenObj(v, key)); }
        else { res[key] = Array.isArray(v) ? JSON.stringify(v) : String(v ?? ''); }
    }
    return res;
};
const escCSV = (v) => { const s = String(v); return s.includes(',') || s.includes('"') || s.includes('\n') ? `"${s.replace(/"/g,'""')}"` : s; };
const downloadCSV = () => { const blob = new Blob([csvOutput.value], {type:'text/csv'}); const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='converted.csv'; a.click(); };
</script>
