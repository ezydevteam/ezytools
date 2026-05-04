<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">XML Input</label>
            <div class="flex gap-2 mb-1">
                <label class="text-xs text-primary-600 hover:text-primary-700 font-medium cursor-pointer">📁 Upload File<input type="file" @change="onFile" accept=".xml" class="hidden" /></label>
            </div>
            <textarea v-model="xmlInput" rows="16" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none" placeholder="<root><item><name>John</name><age>30</age></item></root>"></textarea>
        </div>
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">JSON Output</label>
            <div class="flex gap-2 mb-1"><button v-if="jsonOutput" @click="copyOutput" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ copied ? '✅ Copied' : '📋 Copy' }}</button></div>
            <textarea v-model="jsonOutput" rows="16" readonly class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none bg-surface-50 dark:bg-surface-950" placeholder="JSON output..."></textarea>
        </div>
    </div>
    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ error }}</div>
    <div class="flex gap-3">
        <button @click="convert" :disabled="!xmlInput.trim()" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md">🔄 Convert to JSON</button>
        <button v-if="jsonOutput" @click="downloadJSON" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const xmlInput = ref(''); const jsonOutput = ref(''); const error = ref(''); const copied = ref(false);
const onFile = (e) => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=(ev)=>{ xmlInput.value=ev.target.result; }; r.readAsText(f); }};
const copyOutput = () => { navigator.clipboard.writeText(jsonOutput.value); copied.value=true; setTimeout(()=>copied.value=false,2000); };
const convert = () => {
    error.value = ''; jsonOutput.value = '';
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(xmlInput.value, 'text/xml');
        const parseErr = doc.querySelector('parsererror');
        if (parseErr) throw new Error('Invalid XML: ' + parseErr.textContent.slice(0, 100));
        const result = xmlToJson(doc.documentElement);
        jsonOutput.value = JSON.stringify(result, null, 2);
    } catch (e) { error.value = e.message; }
};
const xmlToJson = (node) => {
    const obj = {};
    // Attributes
    if (node.attributes?.length) { obj['@attributes'] = {}; Array.from(node.attributes).forEach(a => { obj['@attributes'][a.name] = a.value; }); }
    // Children
    if (node.children.length) {
        const childMap = {};
        Array.from(node.children).forEach(child => {
            const val = xmlToJson(child);
            if (childMap[child.tagName]) {
                if (!Array.isArray(obj[child.tagName])) obj[child.tagName] = [obj[child.tagName]];
                obj[child.tagName].push(val);
            } else { obj[child.tagName] = val; childMap[child.tagName] = true; }
        });
    } else { const txt = node.textContent.trim(); if (txt) { if (Object.keys(obj).length) obj['#text'] = txt; else return txt; } }
    return obj;
};
const downloadJSON = () => { const blob = new Blob([jsonOutput.value], {type:'application/json'}); const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='converted.json'; a.click(); };
</script>
