<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Input Area -->
            <div class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Default Rule for All Robots</label>
                    <select v-model="defaultRule" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="allow">Allow All (Recommended)</option>
                        <option value="disallow">Disallow All</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Crawl-delay (seconds)</label>
                    <select v-model="crawlDelay" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Default - No Delay</option>
                        <option value="5">5 Seconds</option>
                        <option value="10">10 Seconds</option>
                        <option value="20">20 Seconds</option>
                        <option value="60">60 Seconds</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-1">Sitemap URL (Optional)</label>
                    <input type="text" v-model="sitemapUrl" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" placeholder="https://example.com/sitemap.xml">
                </div>

                <div class="border-t border-surface-200 dark:border-surface-700 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-md font-bold text-surface-900 dark:text-white">Specific Rules</h3>
                        <button @click="addRule" class="text-xs px-3 py-1 bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 rounded-lg font-medium hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-colors">+ Add Rule</button>
                    </div>

                    <div v-for="(rule, index) in rules" :key="index" class="p-4 bg-surface-50 dark:bg-surface-900 rounded-xl mb-3 border border-surface-200 dark:border-surface-700 flex gap-4 items-start">
                        <div class="flex-1 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">User-Agent</label>
                                <input type="text" v-model="rule.userAgent" class="block w-full text-sm rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. Googlebot, Bingbot...">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Directory / Path</label>
                                <div class="flex items-center">
                                    <select v-model="rule.access" class="text-sm rounded-l-lg border-r-0 border-surface-300 dark:border-surface-600 bg-surface-100 dark:bg-surface-700 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="Disallow">Disallow</option>
                                        <option value="Allow">Allow</option>
                                    </select>
                                    <input type="text" v-model="rule.path" class="flex-1 text-sm rounded-r-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. /admin/">
                                </div>
                            </div>
                        </div>
                        <button @click="removeRule(index)" class="p-2 text-surface-400 hover:text-red-500 transition-colors mt-6" title="Remove rule">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                    
                    <div v-if="rules.length === 0" class="text-sm text-surface-500 text-center py-4 border border-dashed border-surface-300 dark:border-surface-600 rounded-xl">
                        No specific rules added. Click "+ Add Rule" to restrict specific bots or paths.
                    </div>
                </div>
            </div>

            <!-- Output Area -->
            <div class="flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Generated robots.txt</label>
                    <div class="flex gap-2">
                        <button @click="downloadFile" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600 text-surface-700 dark:text-surface-300">
                            Download
                        </button>
                        <button @click="copyToClipboard" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600" :class="copied ? 'text-green-500' : 'text-surface-700 dark:text-surface-300'">
                            {{ copied ? 'Copied!' : 'Copy' }}
                        </button>
                    </div>
                </div>
                <div class="flex-1 w-full p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 overflow-auto">
                    <pre class="font-mono text-sm whitespace-pre overflow-x-auto text-surface-900 dark:text-surface-200"><code>{{ generatedRobotsTxt }}</code></pre>
                </div>
                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/30 text-xs text-blue-800 dark:text-blue-300">
                    <p><strong>Installation:</strong> Create a file named <code>robots.txt</code>, paste this content inside, and place it in the root directory of your website (e.g. <code>https://example.com/robots.txt</code>).</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const defaultRule = ref('allow');
const crawlDelay = ref('');
const sitemapUrl = ref('');
const copied = ref(false);

const rules = ref([
    { userAgent: 'Googlebot', access: 'Disallow', path: '/admin/' }
]);

const addRule = () => {
    rules.value.push({ userAgent: '', access: 'Disallow', path: '/' });
};

const removeRule = (index) => {
    rules.value.splice(index, 1);
};

const generatedRobotsTxt = computed(() => {
    let lines = [];
    
    lines.push('User-agent: *');
    if (defaultRule.value === 'disallow') {
        lines.push('Disallow: /');
    } else {
        lines.push('Disallow:'); // Disallow nothing = allow all
    }
    
    if (crawlDelay.value) {
        lines.push(`Crawl-delay: ${crawlDelay.value}`);
    }
    
    lines.push('');
    
    // Group rules by User Agent
    const groupedRules = {};
    rules.value.forEach(r => {
        if (!r.userAgent || !r.path) return;
        const ua = r.userAgent.trim();
        if (!groupedRules[ua]) groupedRules[ua] = [];
        groupedRules[ua].push(`${r.access}: ${r.path}`);
    });
    
    Object.keys(groupedRules).forEach(ua => {
        lines.push(`User-agent: ${ua}`);
        groupedRules[ua].forEach(ruleStr => {
            lines.push(ruleStr);
        });
        lines.push('');
    });
    
    if (sitemapUrl.value) {
        lines.push(`Sitemap: ${sitemapUrl.value}`);
    }
    
    return lines.join('\n').trim();
});

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(generatedRobotsTxt.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};

const downloadFile = () => {
    const blob = new Blob([generatedRobotsTxt.value], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'robots.txt';
    a.click();
    URL.revokeObjectURL(url);
};
</script>
