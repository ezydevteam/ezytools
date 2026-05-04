<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="md:col-span-1 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Paragraphs</label>
                    <input type="number" v-model="paragraphs" min="1" max="100" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Length</label>
                    <select v-model="lengthType" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="short">Short</option>
                        <option value="medium">Medium</option>
                        <option value="long">Long</option>
                    </select>
                </div>
                <div>
                    <label class="flex items-center gap-2 cursor-pointer mt-6">
                        <input type="checkbox" v-model="startWithLorem" class="rounded text-primary-600 focus:ring-primary-500 border-surface-300">
                        <span class="text-sm text-surface-700 dark:text-surface-300">Start with "Lorem ipsum..."</span>
                    </label>
                </div>
                <div>
                    <button @click="generateText" class="w-full py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl transition-colors shadow-md text-sm mt-2">
                        Regenerate
                    </button>
                </div>
            </div>

            <div class="md:col-span-3 flex flex-col h-full">
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-bold text-surface-900 dark:text-white">Generated Text</label>
                    <div class="flex gap-2">
                        <button @click="copyToClipboard" class="text-xs flex items-center gap-1 transition-colors px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600" :class="copied ? 'text-green-500' : 'text-surface-700 dark:text-surface-300'">
                            <svg v-if="copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            {{ copied ? 'Copied!' : 'Copy' }}
                        </button>
                    </div>
                </div>
                <div class="flex-1 w-full p-4 rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 overflow-y-auto min-h-[300px] max-h-[500px]">
                    <div class="space-y-4 text-surface-800 dark:text-surface-200 text-sm leading-relaxed">
                        <p v-for="(p, i) in generatedParagraphs" :key="i">{{ p }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const paragraphs = ref(3);
const lengthType = ref('medium');
const startWithLorem = ref(true);
const generatedParagraphs = ref([]);
const copied = ref(false);

const loremWords = ['lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'curabitur', 'vel', 'hendrerit', 'libero', 'eleifend', 'blandit', 'nunc', 'ornare', 'odio', 'ut', 'orci', 'gravida', 'imperdiet', 'nullam', 'purus', 'lacinia', 'a', 'pretium', 'quis', 'congue', 'praesent', 'sagittis', 'laoreet', 'auctor', 'mauris', 'non', 'velit', 'eros', 'dictum', 'proin', 'accumsan', 'sapien', 'nec', 'massa', 'volutpat', 'venenatis', 'sed', 'eu', 'molestie', 'lacus', 'quisque', 'porttitor', 'ligula', 'dui', 'mollis', 'tempus', 'at', 'magna', 'vestibulum', 'turpis', 'ac', 'diam', 'tincidunt', 'id', 'condimentum', 'enim', 'sodales', 'in', 'hac', 'habitasse', 'platea', 'dictumst', 'aenean', 'neque', 'fusce', 'augue', 'leo', 'eget', 'semper', 'mattis', 'tortor', 'scelerisque', 'nulla', 'interdum', 'tellus', 'malesuada', 'rhoncus', 'porta', 'sem', 'aliquet', 'et', 'nam', 'suspendisse', 'potenti', 'vivamus', 'luctus', 'fringilla', 'erat', 'donec', 'justo', 'vehicula', 'ultricies', 'varius', 'ante', 'primis', 'faucibus', 'ultrices', 'posuere', 'cubilia', 'curae', 'etiam', 'cursus', 'aliquam', 'quam', 'dapibus', 'nisl', 'feugiat', 'egestas', 'class', 'aptent', 'taciti', 'sociosqu', 'ad', 'litora', 'torquent', 'per', 'conubia', 'nostra', 'inceptos', 'himenaeos', 'phasellus', 'nibh', 'pulvinar', 'vitae', 'urna', 'iaculis', 'lobortis', 'nisi', 'viverra', 'arcu', 'morbi', 'pellentesque', 'metus', 'commodo', 'ut', 'facilisis', 'felis', 'tristique', 'ullamcorper', 'placerat', 'aenean', 'convallis', 'sollicitudin', 'integer', 'rutrum', 'duis', 'est', 'etiam', 'bibendum', 'donec', 'pharetra', 'vulputate', 'maecenas', 'mi', 'fermentum', 'consequat', 'suscipit', 'aliquam', 'habitant', 'senectus', 'netus', 'fames', 'quisque', 'euismod', 'curabitur', 'lectus', 'elementum', 'tempor', 'risus', 'cras'];

const generateSentence = (wordsCount) => {
    let sentence = [];
    for (let i = 0; i < wordsCount; i++) {
        const randIndex = Math.floor(Math.random() * loremWords.length);
        sentence.push(loremWords[randIndex]);
    }
    sentence[0] = sentence[0].charAt(0).toUpperCase() + sentence[0].slice(1);
    return sentence.join(' ') + '.';
};

const generateParagraph = (isFirst) => {
    let sentencesCount = 5;
    if (lengthType.value === 'short') sentencesCount = 3;
    if (lengthType.value === 'long') sentencesCount = 8;
    
    // Add random variation
    sentencesCount += Math.floor(Math.random() * 3) - 1; 

    let sentences = [];
    
    if (isFirst && startWithLorem.value) {
        let firstSentenceWords = [];
        for (let i = 0; i < 8; i++) {
            firstSentenceWords.push(loremWords[Math.floor(Math.random() * loremWords.length)]);
        }
        sentences.push('Lorem ipsum dolor sit amet, consectetur adipiscing elit, ' + firstSentenceWords.join(' ') + '.');
        sentencesCount--;
    }

    for (let i = 0; i < sentencesCount; i++) {
        const wordsPerSentence = Math.floor(Math.random() * 10) + 8; // 8 to 17 words
        sentences.push(generateSentence(wordsPerSentence));
    }

    return sentences.join(' ');
};

const generateText = () => {
    let numP = parseInt(paragraphs.value);
    if (isNaN(numP) || numP < 1) numP = 1;
    if (numP > 100) numP = 100;
    
    generatedParagraphs.value = [];
    for (let i = 0; i < numP; i++) {
        generatedParagraphs.value.push(generateParagraph(i === 0));
    }
};

onMounted(() => {
    generateText();
});

const copyToClipboard = async () => {
    try {
        const textToCopy = generatedParagraphs.value.join('\n\n');
        await navigator.clipboard.writeText(textToCopy);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
};
</script>
