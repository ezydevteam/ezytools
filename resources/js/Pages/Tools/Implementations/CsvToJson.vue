<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">CSV Input</label>
            <div class="flex gap-2 mb-1">
                <button @click="pasteClipboard" class="text-xs text-primary-600 hover:text-primary-700 font-medium">📋 Paste</button>
                <label class="text-xs text-primary-600 hover:text-primary-700 font-medium cursor-pointer">📁 Upload File<input type="file" @change="onFile" accept=".csv,text/csv" class="hidden" /></label>
            </div>
            <textarea v-model="csvInput" rows="16" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none" placeholder="name,age,city&#10;John,30,Dhaka&#10;Jane,25,Chittagong"></textarea>
        </div>
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">JSON Output</label>
            <div class="flex gap-2 mb-1">
                <button v-if="jsonOutput" @click="copyOutput" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ copied ? '✅ Copied' : '📋 Copy' }}</button>
            </div>
            <textarea v-model="jsonOutput" rows="16" readonly class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none bg-surface-50 dark:bg-surface-950" placeholder="JSON output will appear here..."></textarea>
        </div>
    </div>
    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ error }}</div>
    <div class="flex gap-3">
        <button @click="convert" :disabled="!csvInput.trim()" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md">🔄 Convert to JSON</button>
        <button v-if="jsonOutput" @click="downloadJSON" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const csvInput = ref(''); const jsonOutput = ref(''); const error = ref(''); const copied = ref(false);
const onFile = (e) => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=(ev)=>{ csvInput.value=ev.target.result; }; r.readAsText(f); }};
const pasteClipboard = async () => { try { csvInput.value = await navigator.clipboard.readText(); } catch(e){} };
const copyOutput = () => { navigator.clipboard.writeText(jsonOutput.value); copied.value=true; setTimeout(()=>copied.value=false,2000); };
const convert = () => {
    error.value = ''; jsonOutput.value = '';
    try {
        const rows = parseCSV(csvInput.value);
        if (rows.length < 2) throw new Error('Need at least a header row and one data row');
        const headers = rows[0];
        const result = rows.slice(1).filter(r => r.some(c => c.trim())).map(row => {
            const obj = {};
            headers.forEach((h, i) => { obj[h.trim()] = row[i]?.trim() ?? ''; });
            return obj;
        });
        jsonOutput.value = JSON.stringify(result, null, 2);
    } catch (e) { error.value = e.message; }
};
const parseCSV = (text) => {
    const lines = []; let current = []; let field = ''; let inQuotes = false;
    for (let i = 0; i < text.length; i++) {
        const c = text[i];
        if (inQuotes) { if (c === '"' && text[i+1] === '"') { field += '"'; i++; } else if (c === '"') { inQuotes = false; } else { field += c; } }
        else { if (c === '"') { inQuotes = true; } else if (c === ',') { current.push(field); field = ''; } else if (c === '\n' || (c === '\r' && text[i+1] === '\n')) { current.push(field); field = ''; lines.push(current); current = []; if (c === '\r') i++; } else { field += c; } }
    }
    if (field || current.length) { current.push(field); lines.push(current); }
    return lines;
};
const downloadJSON = () => { const blob = new Blob([jsonOutput.value], {type:'application/json'}); const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='converted.json'; a.click(); };
</script>
