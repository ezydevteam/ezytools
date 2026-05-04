<template>
    <div>
        <label v-if="label" class="block text-xs font-medium text-surface-500 mb-1">{{ label }}</label>
        <Listbox :modelValue="modelValue" @update:modelValue="$emit('update:modelValue', $event)">
            <div class="relative">
                <ListboxButton
                    class="relative w-full cursor-default rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 py-2 pl-3 pr-10 text-left focus:outline-none focus-visible:border-primary-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-primary-300 sm:text-sm flex items-center gap-2 h-[38px]"
                >
                    <img v-if="currentLang" :src="currentLang.flagUrl" :alt="currentLang.label" class="w-5 h-3.5 object-cover rounded-[2px]" />
                    <span class="block truncate text-surface-900 dark:text-white text-sm">{{ currentLang ? currentLang.label : 'Select Language' }}</span>
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                        <ChevronUpDownIcon class="h-5 w-5 text-surface-400" aria-hidden="true" />
                    </span>
                </ListboxButton>

                <transition
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <ListboxOptions
                        class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-surface-800 py-1 text-base shadow-lg ring-1 ring-black/5 dark:ring-white/10 focus:outline-none sm:text-sm"
                    >
                        <ListboxOption
                            v-slot="{ active, selected }"
                            v-for="lang in languages"
                            :key="lang.value"
                            :value="lang.value"
                            as="template"
                        >
                            <li
                                :class="[
                                    active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-900 dark:text-primary-100' : 'text-surface-900 dark:text-white',
                                    'relative cursor-default select-none py-2 pl-3 pr-9 flex items-center gap-2'
                                ]"
                            >
                                <img :src="lang.flagUrl" :alt="lang.label" class="w-5 h-3.5 object-cover rounded-[2px] shadow-sm" />
                                <span :class="[selected ? 'font-medium' : 'font-normal', 'block truncate']">{{ lang.label }}</span>
                                
                                <span
                                    v-if="selected"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-primary-600 dark:text-primary-400"
                                >
                                    <CheckIcon class="h-4 w-4" aria-hidden="true" />
                                </span>
                            </li>
                        </ListboxOption>
                    </ListboxOptions>
                </transition>
            </div>
        </Listbox>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    modelValue: { type: String, default: 'english_us' },
    label: { type: String, default: 'Language' },
});

defineEmits(['update:modelValue']);

const languages = [
    { value: 'bangla',          label: 'বাংলা',          flagUrl: 'https://flagcdn.com/bd.svg', dir: 'ltr' },
    { value: 'english_us',      label: 'English (US)',   flagUrl: 'https://flagcdn.com/us.svg', dir: 'ltr' },
    { value: 'english_british', label: 'English (UK)',   flagUrl: 'https://flagcdn.com/gb.svg', dir: 'ltr' },
    { value: 'hindi',           label: 'हिंदी',          flagUrl: 'https://flagcdn.com/in.svg', dir: 'ltr' },
    { value: 'urdu',            label: 'اردو',           flagUrl: 'https://flagcdn.com/pk.svg', dir: 'rtl' },
    { value: 'arabic',          label: 'العربية',        flagUrl: 'https://flagcdn.com/sa.svg', dir: 'rtl' },
];

const currentLang = computed(() => languages.find(l => l.value === props.modelValue) || languages[1]);

const isRtl = (langValue) => {
    const lang = languages.find(l => l.value === langValue);
    return lang?.dir === 'rtl';
};

defineExpose({ languages, isRtl });
</script>
