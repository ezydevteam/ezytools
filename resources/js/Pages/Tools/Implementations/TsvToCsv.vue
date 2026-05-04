<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">TSV Input (Tab-Separated)</label>
            <div class="flex gap-2 mb-1">
                <button @click="pasteClipboard" class="text-xs text-primary-600 hover:text-primary-700 font-medium">📋 Paste</button>
                <label class="text-xs text-primary-600 hover:text-primary-700 font-medium cursor-pointer">📁 Upload File<input type="file" @change="onFile" accept=".tsv,.txt" class="hidden" /></label>
            </div>
            <textarea v-model="tsvInput" rows="16" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none" placeholder="name&#9;age&#9;city&#10;John&#9;30&#9;Dhaka"></textarea>
        </div>
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">CSV Output</label>
            <div class="flex gap-2 mb-1"><button v-if="csvOutput" @click="copyOutput" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ copied ? '✅ Copied' : '📋 Copy' }}</button></div>
            <textarea v-model="csvOutput" rows="16" readonly class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none bg-surface-50 dark:bg-surface-950" placeholder="CSV output..."></textarea>
        </div>
    </div>
    <div class="flex gap-3">
        <button @click="convert" :disabled="!tsvInput.trim()" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md">🔄 Convert to CSV</button>
        <button v-if="csvOutput" @click="downloadCSV" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const tsvInput = ref(''); const csvOutput = ref(''); const copied = ref(false);
const onFile = (e) => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=(ev)=>{ tsvInput.value=ev.target.result; }; r.readAsText(f); }};
const pasteClipboard = async () => { try { tsvInput.value = await navigator.clipboard.readText(); } catch(e){} };
const copyOutput = () => { navigator.clipboard.writeText(csvOutput.value); copied.value=true; setTimeout(()=>copied.value=false,2000); };
const escCSV = (v) => { const s = String(v); return s.includes(',') || s.includes('"') || s.includes('\n') ? `"${s.replace(/"/g,'""')}"` : s; };
const convert = () => {
    const lines = tsvInput.value.split('\n').filter(l => l.trim());
    csvOutput.value = lines.map(line => line.split('\t').map(escCSV).join(',')).join('\n');
};
const downloadCSV = () => { const blob = new Blob([csvOutput.value], {type:'text/csv'}); const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='converted.csv'; a.click(); };
</script>
