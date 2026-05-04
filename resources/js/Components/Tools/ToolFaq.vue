<template>
    <div v-if="faqs && faqs.length > 0" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center">
                        <QuestionMarkCircleIcon class="w-6 h-6" />
                    </div>
                    <h2 class="text-xl font-bold text-surface-900 dark:text-white">Frequently Asked Questions</h2>
                </div>
                <!-- Language toggle -->
                <div class="flex gap-1 bg-surface-100 dark:bg-surface-700 rounded-lg p-1">
                    <button @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-white dark:bg-surface-600 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500'" class="px-3 py-1 text-sm rounded-md transition-all font-medium">
                        English
                    </button>
                    <button @click="activeLang = 'bn'" :class="activeLang === 'bn' ? 'bg-white dark:bg-surface-600 shadow text-primary-600 dark:text-primary-400' : 'text-surface-500'" class="px-3 py-1 text-sm rounded-md transition-all font-medium">
                        বাংলা
                    </button>
                </div>
            </div>

            <div class="space-y-3">
                <div 
                    v-for="(faq, index) in faqs" 
                    :key="faq.id"
                    class="rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden transition-all duration-200"
                    :class="{'ring-2 ring-primary-500/20 border-primary-500/50': openIndex === index}"
                >
                    <button 
                        @click="toggle(index)"
                        class="w-full px-6 py-4 text-left flex items-center justify-between gap-4 hover:bg-surface-50 dark:hover:bg-surface-700/30 transition-colors"
                    >
                        <span class="font-semibold text-surface-900 dark:text-white">{{ getQuestion(faq) }}</span>
                        <ChevronDownIcon 
                            class="w-5 h-5 text-surface-400 transition-transform duration-200 flex-shrink-0"
                            :class="{'rotate-180': openIndex === index}"
                        />
                    </button>
                    
                    <div 
                        v-show="openIndex === index"
                        class="px-6 pb-5 text-surface-600 dark:text-surface-400 leading-relaxed text-sm"
                    >
                        {{ getAnswer(faq) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { QuestionMarkCircleIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    faqs: Array,
    lang: { type: String, default: 'en' },
});

const activeLang = ref(props.lang === 'bn' ? 'bn' : 'en');
const openIndex = ref(0);

const toggle = (index) => {
    openIndex.value = openIndex.value === index ? -1 : index;
};

const getQuestion = (faq) => {
    if (activeLang.value === 'bn' && faq.question_bn) return faq.question_bn;
    return faq.question;
};

const getAnswer = (faq) => {
    if (activeLang.value === 'bn' && faq.answer_bn) return faq.answer_bn;
    return faq.answer;
};
</script>
