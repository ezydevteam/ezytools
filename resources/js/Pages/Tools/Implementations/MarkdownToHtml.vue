<template>
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Markdown Input</label>
            <div class="flex gap-2 mb-1">
                <label class="text-xs text-primary-600 hover:text-primary-700 font-medium cursor-pointer">📁 Upload File<input type="file" @change="onFile" accept=".md,.markdown,.txt" class="hidden" /></label>
            </div>
            <textarea v-model="mdInput" rows="16" class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none" placeholder="# Hello World&#10;&#10;This is **bold** and *italic* text.&#10;&#10;- List item 1&#10;- List item 2&#10;&#10;```javascript&#10;console.log('hi');&#10;```"></textarea>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Output</label>
                <div class="flex gap-2">
                    <button @click="mode='preview'" class="text-xs px-2 py-1 rounded-lg" :class="mode==='preview'?'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300':'text-surface-500'">Preview</button>
                    <button @click="mode='code'" class="text-xs px-2 py-1 rounded-lg" :class="mode==='code'?'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300':'text-surface-500'">HTML Code</button>
                </div>
            </div>
            <div v-if="mode==='preview'" class="h-[400px] overflow-y-auto rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 p-4 prose dark:prose-invert max-w-none" v-html="htmlOutput"></div>
            <div v-else class="relative">
                <textarea v-model="htmlOutput" rows="16" readonly class="w-full rounded-xl border-surface-300 dark:border-surface-600 dark:bg-surface-900 text-sm font-mono resize-none bg-surface-50 dark:bg-surface-950"></textarea>
                <button @click="copyOutput" class="absolute top-2 right-2 text-xs bg-surface-200 dark:bg-surface-700 px-2 py-1 rounded-lg text-surface-600 dark:text-surface-300 hover:bg-surface-300">{{ copied ? '✅' : '📋' }}</button>
            </div>
        </div>
    </div>
    <div class="flex gap-3">
        <button @click="convert" :disabled="!mdInput.trim()" class="flex-1 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-medium rounded-xl shadow-md">🔄 Convert to HTML</button>
        <button v-if="htmlOutput" @click="downloadHTML" class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-md">⬇ Download</button>
    </div>
</div>
</template>

<script setup>
import { ref, watch } from 'vue';
const mdInput = ref(''); const htmlOutput = ref(''); const mode = ref('preview'); const copied = ref(false);
const onFile = (e) => { const f=e.target.files[0]; if(f) { const r=new FileReader(); r.onload=(ev)=>{ mdInput.value=ev.target.result; convert(); }; r.readAsText(f); }};
const copyOutput = () => { navigator.clipboard.writeText(htmlOutput.value); copied.value=true; setTimeout(()=>copied.value=false,2000); };

watch(mdInput, () => { if (mdInput.value.trim()) convert(); });

const convert = () => {
    let md = mdInput.value;
    // Code blocks
    md = md.replace(/```(\w*)\n([\s\S]*?)```/g, (_, lang, code) => `<pre><code class="language-${lang}">${esc(code.trim())}</code></pre>`);
    // Inline code
    md = md.replace(/`([^`]+)`/g, '<code>$1</code>');
    // Headings
    md = md.replace(/^######\s+(.+)$/gm, '<h6>$1</h6>');
    md = md.replace(/^#####\s+(.+)$/gm, '<h5>$1</h5>');
    md = md.replace(/^####\s+(.+)$/gm, '<h4>$1</h4>');
    md = md.replace(/^###\s+(.+)$/gm, '<h3>$1</h3>');
    md = md.replace(/^##\s+(.+)$/gm, '<h2>$1</h2>');
    md = md.replace(/^#\s+(.+)$/gm, '<h1>$1</h1>');
    // Bold + italic
    md = md.replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>');
    md = md.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    md = md.replace(/\*(.+?)\*/g, '<em>$1</em>');
    md = md.replace(/~~(.+?)~~/g, '<del>$1</del>');
    // Links & images
    md = md.replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1" />');
    md = md.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');
    // Blockquotes
    md = md.replace(/^>\s+(.+)$/gm, '<blockquote>$1</blockquote>');
    // Horizontal rules
    md = md.replace(/^[-*_]{3,}$/gm, '<hr />');
    // Unordered lists
    md = md.replace(/^[-*+]\s+(.+)$/gm, '<li>$1</li>');
    md = md.replace(/((?:<li>.*<\/li>\n?)+)/g, '<ul>$1</ul>');
    // Ordered lists
    md = md.replace(/^\d+\.\s+(.+)$/gm, '<li>$1</li>');
    // Paragraphs
    md = md.replace(/\n\n+/g, '\n</p>\n<p>\n');
    md = '<p>' + md + '</p>';
    md = md.replace(/<p>\s*<(h[1-6]|ul|ol|pre|blockquote|hr)/g, '<$1');
    md = md.replace(/<\/(h[1-6]|ul|ol|pre|blockquote)>\s*<\/p>/g, '</$1>');
    md = md.replace(/<p>\s*<\/p>/g, '');
    md = md.replace(/<p>\s*<hr \/>\s*<\/p>/g, '<hr />');
    htmlOutput.value = md.trim();
};

const esc = (s) => s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
const downloadHTML = () => {
    const full = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Converted</title></head><body>\n${htmlOutput.value}\n</body></html>`;
    const blob = new Blob([full], {type:'text/html'}); const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='converted.html'; a.click();
};
</script>
