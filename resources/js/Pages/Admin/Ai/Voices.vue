<template>
    <AdminLayout>
        <Head title="AI Voices" />

        <template #header>
            <div class="flex justify-between items-center w-full">
                <h2 class="font-semibold text-xl text-surface-800 dark:text-surface-200 leading-tight">AI Voices</h2>
                <button @click="openCreateModal" class="ms-4 px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                    Add Voice
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filter -->
            <div class="flex gap-2 flex-wrap">
                <button v-for="lang in ['all', 'bangla', 'english', 'hindi', 'arabic', 'urdu']" :key="lang"
                        @click="filterLang = lang"
                        :class="filterLang === lang ? 'bg-primary-600 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 border border-surface-200 dark:border-surface-700'"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors capitalize">
                    {{ lang }}
                </button>
            </div>

            <!-- Voices Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="voice in filteredVoices" :key="voice.id" class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold"
                                     :class="voice.provider === 'elevenlabs' ? 'bg-purple-500' : voice.provider === 'openai' ? 'bg-emerald-500' : 'bg-blue-500'">
                                    {{ voice.provider.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-surface-900 dark:text-white text-sm">{{ voice.name }}</h3>
                                    <p class="text-xs text-surface-400">{{ voice.provider }} &bull; {{ voice.gender }}</p>
                                </div>
                            </div>
                            <div class="flex gap-1">
                                <span v-if="voice.is_active" class="px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Active</span>
                                <span v-else class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Inactive</span>
                                <span v-if="voice.is_pro_only" class="px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">Pro</span>
                            </div>
                        </div>
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between"><span class="text-surface-400">Language:</span><span class="font-medium text-surface-700 dark:text-surface-200 capitalize">{{ voice.language }}</span></div>
                            <div class="flex justify-between"><span class="text-surface-400">Voice ID:</span><span class="font-mono text-xs text-surface-500 truncate max-w-[140px]">{{ voice.provider_voice_id }}</span></div>
                            <div v-if="voice.accent" class="flex justify-between"><span class="text-surface-400">Accent:</span><span class="text-surface-700 dark:text-surface-200">{{ voice.accent }}</span></div>
                        </div>
                    </div>
                    <div class="px-5 py-3 border-t border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-800/50 flex justify-between items-center">
                        <button @click="deleteVoice(voice)" class="text-xs font-medium text-red-500 hover:text-red-700 transition-colors">Delete</button>
                        <button @click="openEditModal(voice)" class="px-3 py-1 bg-white dark:bg-surface-700 border border-surface-200 dark:border-surface-600 rounded-lg text-xs font-medium hover:bg-surface-50 dark:hover:bg-surface-600 transition-colors">Edit</button>
                    </div>
                </div>
            </div>

            <div v-if="filteredVoices.length === 0" class="text-center py-12 text-surface-400">
                No voices found for this language.
            </div>
        </div>

        <!-- Voice Modal -->
        <Modal :show="showingModal" @close="closeModal" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-surface-900 dark:text-white mb-4">
                    {{ editingVoice ? 'Edit Voice' : 'Add New Voice' }}
                </h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="name" value="Voice Name" />
                            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.name" class="mt-1" />
                        </div>
                        <div>
                            <InputLabel for="provider" value="Provider" />
                            <select id="provider" v-model="form.provider" class="mt-1 block w-full rounded-md border-surface-300 dark:border-surface-700 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                <option value="elevenlabs">ElevenLabs</option>
                                <option value="openai">OpenAI</option>
                                <option value="google">Google</option>
                            </select>
                            <InputError :message="form.errors.provider" class="mt-1" />
                        </div>
                    </div>
                    <div>
                        <InputLabel for="provider_voice_id" value="Provider Voice ID" />
                        <TextInput id="provider_voice_id" v-model="form.provider_voice_id" type="text" class="mt-1 block w-full" required placeholder="e.g., nova, bn-BD-Standard-A" />
                        <InputError :message="form.errors.provider_voice_id" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="language" value="Language" />
                            <select id="language" v-model="form.language" class="mt-1 block w-full rounded-md border-surface-300 dark:border-surface-700 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                <option value="bangla">Bangla</option>
                                <option value="english">English</option>
                                <option value="hindi">Hindi</option>
                                <option value="arabic">Arabic</option>
                                <option value="urdu">Urdu</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="gender" value="Gender" />
                            <select id="gender" v-model="form.gender" class="mt-1 block w-full rounded-md border-surface-300 dark:border-surface-700 dark:bg-surface-900 focus:ring-primary-500 focus:border-primary-500 text-sm">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="accent" value="Accent (Optional)" />
                            <TextInput id="accent" v-model="form.accent" type="text" class="mt-1 block w-full" placeholder="e.g., American, Bangladesh" />
                        </div>
                        <div>
                            <InputLabel for="preview_url" value="Preview URL (Optional)" />
                            <TextInput id="preview_url" v-model="form.preview_url" type="url" class="mt-1 block w-full" placeholder="https://..." />
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mt-2">
                        <label class="flex items-center">
                            <Checkbox name="is_active" v-model:checked="form.is_active" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-400">Active</span>
                        </label>
                        <label class="flex items-center">
                            <Checkbox name="is_pro_only" v-model:checked="form.is_pro_only" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-400">Pro Only</span>
                        </label>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Save Voice</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({ voices: Array });

const showingModal = ref(false);
const editingVoice = ref(null);
const filterLang = ref('all');

const filteredVoices = computed(() => {
    if (filterLang.value === 'all') return props.voices;
    return props.voices.filter(v => v.language === filterLang.value);
});

const form = useForm({
    provider: 'elevenlabs',
    provider_voice_id: '',
    name: '',
    language: 'bangla',
    gender: 'female',
    accent: '',
    is_active: true,
    is_pro_only: false,
    preview_url: '',
});

const openCreateModal = () => { editingVoice.value = null; form.reset(); showingModal.value = true; };

const openEditModal = (voice) => {
    editingVoice.value = voice;
    form.provider = voice.provider;
    form.provider_voice_id = voice.provider_voice_id;
    form.name = voice.name;
    form.language = voice.language;
    form.gender = voice.gender;
    form.accent = voice.accent || '';
    form.is_active = voice.is_active;
    form.is_pro_only = voice.is_pro_only;
    form.preview_url = voice.preview_url || '';
    showingModal.value = true;
};

const closeModal = () => { showingModal.value = false; form.reset(); form.clearErrors(); };

const submit = () => {
    if (editingVoice.value) {
        form.put(route('admin.ai.voices.update', editingVoice.value.id), { preserveScroll: true, onSuccess: () => closeModal() });
    } else {
        form.post(route('admin.ai.voices.store'), { preserveScroll: true, onSuccess: () => closeModal() });
    }
};

const deleteVoice = (voice) => {
    if (confirm(`Delete voice "${voice.name}"?`)) {
        router.delete(route('admin.ai.voices.destroy', voice.id), { preserveScroll: true });
    }
};
</script>
