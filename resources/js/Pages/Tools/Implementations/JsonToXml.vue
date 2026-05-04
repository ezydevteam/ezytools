<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">JSON Input</label>
            <div class="flex gap-2 mb-1">
                <label class="text-xs text-primary-600 hover:text-primary-700 font-medium cursor-pointer">📁 Upload File<input type="file" @change="onFile" accept=".json" class="hidden" /></label>
            </div>
            <textarea v-model="jsonInput" rows="16" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none" placeholder='{"users":[{"name":"John","age":30}]}'></textarea>
        </div>
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">XML Output</label>
            <div class="flex gap-2 mb-1"><button v-if="xmlOutput" @click="copyOutput" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ copied ? '✅ Copied' : '📋 Copy' }}</button></div>
            <textarea v-model="xmlOutput" rows="16" readonly class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none bg-surface-50 dark:bg-surface-950" placeholder="XML output..."></textarea>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Root Element</label>
            <input type="text" v-model="rootName" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" />
        </div>
        <div>
            <label class="block text-xs font-medium text-surface-700 dark:text-surface-300 mb-1">Item Element</label>
            <input type="text" v-model="itemName" class="w-full rounded-lg border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm" />
        </div>
    </div>
    <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm">{{ error }}</div>
    <div class="flex gap-3">
        <button @click="convert" :disabled="!jsonInput.trim()" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md">🔄 Convert to XML</button>
        <button v-if="xmlOutput" @click="downloadXML" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download</button>
    </div>
</div>
</template>

<script setup>
import { ref } from 'vue';
const jsonInput = ref(''); const xmlOutput = ref(''); const error = ref(''); const copied = ref(false);
const rootName = ref('root'); const itemName = ref('item');
const onFile = (e) => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=(ev)=>{ jsonInput.value=ev.target.result; }; r.readAsText(f); }};
const copyOutput = () => { navigator.clipboard.writeText(xmlOutput.value); copied.value=true; setTimeout(()=>copied.value=false,2000); };
const convert = () => {
    error.value = ''; xmlOutput.value = '';
    try {
        const data = JSON.parse(jsonInput.value);
        let xml = '<?xml version="1.0" encoding="UTF-8"?>\n';
        xml += `<${rootName.value}>\n`;
        xml += jsonToXml(data, 1);
        xml += `</${rootName.value}>`;
        xmlOutput.value = xml;
    } catch (e) { error.value = 'Invalid JSON: ' + e.message; }
};
const indent = (level) => '  '.repeat(level);
const escXml = (s) => String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
const jsonToXml = (data, level) => {
    let xml = '';
    if (Array.isArray(data)) {
        data.forEach(item => { xml += `${indent(level)}<${itemName.value}>\n${jsonToXml(item, level+1)}${indent(level)}</${itemName.value}>\n`; });
    } else if (typeof data === 'object' && data !== null) {
        for (const [key, val] of Object.entries(data)) {
            const tag = key.replace(/[^a-zA-Z0-9_-]/g, '_');
            if (Array.isArray(val)) { val.forEach(v => { if (typeof v === 'object') { xml += `${indent(level)}<${tag}>\n${jsonToXml(v, level+1)}${indent(level)}</${tag}>\n`; } else { xml += `${indent(level)}<${tag}>${escXml(v)}</${tag}>\n`; }}); }
            else if (typeof val === 'object' && val !== null) { xml += `${indent(level)}<${tag}>\n${jsonToXml(val, level+1)}${indent(level)}</${tag}>\n`; }
            else { xml += `${indent(level)}<${tag}>${escXml(val)}</${tag}>\n`; }
        }
    } else { xml += `${indent(level)}${escXml(data)}\n`; }
    return xml;
};
const downloadXML = () => { const blob = new Blob([xmlOutput.value], {type:'text/xml'}); const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='converted.xml'; a.click(); };
</script>
