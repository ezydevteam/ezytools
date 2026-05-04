<template>
    <AiToolWrapper :remaining="remainingRequests"><div class="bg-white dark:bg-surface-800 p-6 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700"><div class="grid grid-cols-1 lg:grid-cols-2 gap-6"><div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Occasion</label><select v-model="options.occasion" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="o in occasions" :key="o" :value="o">{{ o }}</option></select></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Relationship</label><select v-model="options.relationship" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="r in relationships" :key="r" :value="r">{{ r }}</option></select></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Recipient Name</label><input v-model="inputText" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="Name" /></div>
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Your Name (optional)</label><input v-model="options.sender" type="text" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-2.5" placeholder="Your name" /></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-medium text-surface-500 mb-1">Tone</label><select v-model="options.tone" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option v-for="t in tones" :key="t" :value="t">{{ t }}</option></select></div>
            <LanguageSelector v-model="options.language" />
        </div>
        <div><label class="block text-xs font-medium text-surface-500 mb-1">Special Memory / Personal Touch (optional)</label><textarea v-model="options.memory" rows="2" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-sm p-3 resize-none" placeholder="Add a personal touch..."></textarea></div>
        <div><label class="block text-xs font-medium text-surface-500 mb-1">Variations</label><select v-model="options.count" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-sm focus:ring-primary-500 focus:border-primary-500"><option value="1">1</option><option value="3">3</option><option value="5">5</option></select></div>
        <button @click="handleGenerate" :disabled="!inputText || isLoading" class="w-full py-3 px-4 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm"><svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>{{ isLoading ? 'Creating Wishes...' : 'Generate Wishes' }}</button>
        <div v-if="error" class="p-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-sm border border-red-100 dark:border-red-800">{{ error }}</div>
    </div><AiOutputCard :content="result" :loading="isLoading" :language="options.language" title="Wishes" loadingText="Creating wishes..." :showCopy="true" /></div></div></AiToolWrapper>
</template>
<script setup>
import { ref, computed } from 'vue';import { usePage } from '@inertiajs/vue3';import AiToolWrapper from '@/Components/Ai/AiToolWrapper.vue';import AiOutputCard from '@/Components/Ai/AiOutputCard.vue';import LanguageSelector from '@/Components/Ai/LanguageSelector.vue';import { useAiRequest } from '@/Composables/useAiRequest';
const props = defineProps({ tool: Object });const page = usePage();const isPro = computed(() => page.props.auth.user?.is_pro || false);const maxLength = computed(() => { const c = props.tool.ai_config; return c ? (isPro.value ? c.max_input_length_pro : c.max_input_length_free) : 1000; });
const occasions = ['🎂 Birthday','🌙 Eid ul-Fitr','🐄 Eid ul-Adha','🎆 Pohela Boishakh','🌸 Pohela Falgun','💑 Wedding','🎓 Graduation','🏆 Exam Result','👶 New Baby','💼 New Job','🏠 New Home','🎊 Anniversary','❤️ Valentine\'s Day','🙏 Durga Puja','🎄 Christmas','🎉 Custom'];
const relationships = ['Friend','Family','Lover / Partner','Boss / Senior','Colleague','Child'];
const tones = ['Heartfelt & Emotional','Funny & Playful','Formal & Respectful','Short & Sweet','Islamic / Spiritual'];
const inputText = ref('');const options = ref({ occasion: '🎂 Birthday', relationship: 'Friend', tone: 'Heartfelt & Emotional', language: 'english_us', sender: '', memory: '', count: '3' });
const { isLoading, error, result, remainingRequests, generate } = useAiRequest();const handleGenerate = () => { generate(props.tool.slug, inputText.value, options.value); };
</script>
