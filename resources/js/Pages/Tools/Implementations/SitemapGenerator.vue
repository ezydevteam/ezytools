<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Input Area -->
            <div class="space-y-6">
                
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">URLs (One per line)</label>
                        <span class="text-xs text-surface-500">{{ urlList.length }} URLs</span>
                    </div>
                    <textarea 
                        v-model="rawUrls" 
                        rows="8" 
                        class="block w-full p-4 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 font-mono text-sm resize-y" 
                        placeholder="https://example.com/
https://example.com/about
https://example.com/contact"
                    ></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Change Frequency</label>
                        <select v-model="changeFreq" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="none">Don't include</option>
                            <option value="always">Always</option>
                            <option value="hourly">Hourly</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="never">Never</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Default Priority</label>
                        <select v-model="priority" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                            <option value="none">Don't include</option>
                            <option value="1.0">1.0 (Highest)</option>
                            <option value="0.9">0.9</option>
                            <option value="0.8">0.8</option>
                            <option value="0.7">0.7</option>
                            <option value="0.6">0.6</option>
                            <option value="0.5" selected>0.5 (Default)</option>
                            <option value="0.4">0.4</option>
                            <option value="0.3">0.3</option>
                            <option value="0.2">0.2</option>
                            <option value="0.1">0.1</option>
                            <option value="0.0">0.0 (Lowest)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer mt-2">
                        <input type="checkbox" v-model="includeLastMod" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                        <span class="text-sm text-surface-700 dark:text-surface-300">Include Current Date as Last Modified (<code class="text-xs">lastmod</code>)</span>
                    </label>
                </div>
                
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30 text-xs text-blue-800 dark:text-blue-300">
                    <p>XML Sitemaps help search engines like Google and Bing discover and index your pages faster.</p>
                </div>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Generated sitemap.xml</label>
                    <div class="flex gap-2">
                        <button @click="downloadFile" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300" :disabled="!rawUrls">
                            Download
                        </button>
                        <button @click="copyToClipboard" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600" :class="copied ? 'text-green-500' : 'text-surface-700 dark:text-surface-300'" :disabled="!rawUrls">
                            {{ copied ? 'Copied!' : 'Copy' }}
                        </button>
                    </div>
                </div>
                <div class="flex-1 w-full p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 overflow-auto">
                    <pre v-if="rawUrls" class="font-mono text-sm whitespace-pre overflow-x-auto text-surface-900 dark:text-surface-200"><code>{{ generatedSitemap }}</code></pre>
                    <div v-else class="h-full flex items-center justify-center text-surface-400 text-sm">
                        Enter URLs to generate a sitemap
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const rawUrls = ref('');
const changeFreq = ref('monthly');
const priority = ref('0.5');
const includeLastMod = ref(true);
const copied = ref(false);

const urlList = computed(() => {
    return rawUrls.value.split('\n').map(u => u.trim()).filter(u => u.length > 0 && u.startsWith('http'));
});

const generatedSitemap = computed(() => {
    if (urlList.value.length === 0) return '';
    
    let xml = [];
    xml.push('<?xml version="1.0" encoding="UTF-8"?>');
    xml.push('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    
    const today = new Date().toISOString().split('T')[0];
    
    urlList.value.forEach(url => {
        // Basic XML entity escaping for URLs
        const escapedUrl = url.replace(/&/g, '&amp;')
                             .replace(/</g, '&lt;')
                             .replace(/>/g, '&gt;')
                             .replace(/"/g, '&quot;')
                             .replace(/'/g, '&apos;');
                             
        xml.push('  <url>');
        xml.push(`    <loc>${escapedUrl}</loc>`);
        if (includeLastMod.value) xml.push(`    <lastmod>${today}</lastmod>`);
        if (changeFreq.value !== 'none') xml.push(`    <changefreq>${changeFreq.value}</changefreq>`);
        if (priority.value !== 'none') xml.push(`    <priority>${priority.value}</priority>`);
        xml.push('  </url>');
    });
    
    xml.push('</urlset>');
    return xml.join('\n');
});

const copyToClipboard = async () => {
    if (!generatedSitemap.value) return;
    try {
        await navigator.clipboard.writeText(generatedSitemap.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};

const downloadFile = () => {
    if (!generatedSitemap.value) return;
    const blob = new Blob([generatedSitemap.value], { type: 'text/xml' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'sitemap.xml';
    a.click();
    URL.revokeObjectURL(url);
};
</script>
