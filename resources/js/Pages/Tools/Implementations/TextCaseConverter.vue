<template>
    <div>
        <div class="mb-4">
            <label for="text-input" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                Enter your text below
            </label>
            <textarea
                id="text-input"
                v-model="text"
                rows="6"
                class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-lg dark:bg-surface-800 dark:border-surface-600 dark:text-white transition-colors font-mono"
                placeholder="Type or paste your text here..."
            ></textarea>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <button @click="convert('upper')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">UPPERCASE</span>
                <span>ABC</span>
            </button>
            <button @click="convert('lower')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">lowercase</span>
                <span>abc</span>
            </button>
            <button @click="convert('title')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">Title Case</span>
                <span>Abc</span>
            </button>
            <button @click="convert('sentence')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">Sentence case</span>
                <span>Abc.</span>
            </button>
            <button @click="convert('camel')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">camelCase</span>
                <span>helloWorld</span>
            </button>
            <button @click="convert('snake')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">snake_case</span>
                <span>hello_world</span>
            </button>
            <button @click="convert('kebab')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">kebab-case</span>
                <span>hello-world</span>
            </button>
            <button @click="convert('alternating')" class="px-4 py-3 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 text-surface-800 dark:text-surface-200 rounded-lg text-sm font-semibold transition-all shadow-sm flex flex-col items-center gap-1">
                <span class="text-xs text-surface-400 font-normal">aLtErNaTiNg</span>
                <span>aBc</span>
            </button>
        </div>

        <div class="flex gap-2 justify-between items-center border-t border-surface-200 dark:border-surface-700 pt-4">
            <button @click="text = ''" class="text-surface-500 hover:text-red-500 text-sm font-medium transition-colors">
                Clear Text
            </button>
            <button @click="copyText" class="px-6 py-2 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                Copy Result
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const text = ref('');

const convert = (type) => {
    if (!text.value) return;

    let result = text.value;

    switch (type) {
        case 'upper':
            result = result.toUpperCase();
            break;
        case 'lower':
            result = result.toLowerCase();
            break;
        case 'title':
            result = result.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            break;
        case 'sentence':
            result = result.toLowerCase().replace(/(^\s*\w|[\.\!\?]\s*\w)/g, c => c.toUpperCase());
            break;
        case 'camel':
            result = result.toLowerCase().replace(/[^a-zA-Z0-9]+(.)/g, (m, chr) => chr.toUpperCase());
            break;
        case 'snake':
            result = result.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)?.map(x => x.toLowerCase()).join('_') || result;
            break;
        case 'kebab':
            result = result.match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)?.map(x => x.toLowerCase()).join('-') || result;
            break;
        case 'alternating':
            result = result.split('').map((c, i) => i % 2 === 0 ? c.toLowerCase() : c.toUpperCase()).join('');
            break;
    }

    text.value = result;
};

const copyText = () => {
    if (!text.value) return;
    navigator.clipboard.writeText(text.value).then(() => {
        toast.success("Copied to clipboard!");
    });
};
</script>
