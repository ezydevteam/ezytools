<template>
    <div class="max-w-4xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">Merge PDF</h1>
        <p class="text-surface-600 dark:text-surface-400 mb-8">Combine multiple PDFs into one unified document.</p>

        <div v-if="files.length === 0" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8">
            <PdfUploadZone 
                @files-selected="onFilesSelected" 
                @error="error = $event"
                :max-files="tool.settings?.free_max_files || 10"
                :max-size="tool.settings?.free_max_mb * 1024 * 1024 || 10485760" 
                multiple
            />
            <p v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</p>
        </div>

        <div v-else class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="p-6 md:p-8 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900/50 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 rounded-xl flex items-center justify-center">
                        <DocumentDuplicateIcon class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-surface-900 dark:text-white">{{ files.length }} Files Selected</h3>
                        <p class="text-sm text-surface-500">Drag to reorder</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="$refs.addMoreInput.click()" class="text-primary-600 hover:text-primary-700 text-sm font-medium px-4 py-2 hover:bg-primary-50 rounded-lg transition-colors">
                        Add More
                    </button>
                    <input type="file" ref="addMoreInput" class="hidden" accept="application/pdf" multiple @change="addMoreFiles" />
                    <button @click="reset" class="text-red-500 hover:text-red-600 text-sm font-medium px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">
                        Clear All
                    </button>
                </div>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <draggable 
                    v-model="files" 
                    item-key="name"
                    class="space-y-3"
                    ghost-class="opacity-50"
                    animation="200"
                >
                    <template #item="{ element, index }">
                        <div class="flex items-center justify-between p-4 border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 rounded-xl cursor-move hover:border-primary-300 transition-colors">
                            <div class="flex items-center gap-4 truncate">
                                <Bars2Icon class="w-5 h-5 text-surface-400" />
                                <span class="font-medium text-surface-700 dark:text-surface-200 truncate">{{ element.name }}</span>
                                <span class="text-xs text-surface-400">{{ (element.size / 1024 / 1024).toFixed(2) }} MB</span>
                            </div>
                            <button @click="removeFile(index)" class="p-2 text-surface-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <XMarkIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </template>
                </draggable>

                <div v-if="processError" class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl text-sm border border-red-100 dark:border-red-800">
                    {{ processError }}
                </div>

                <div class="flex justify-end pt-4 border-t border-surface-100 dark:border-surface-700">
                    <button 
                        @click="mergePdfs" 
                        :disabled="isProcessing || files.length < 2"
                        class="bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white shadow-lg hover:shadow-xl shadow-primary-500/30 transition-all duration-300 px-8 py-3 rounded-xl text-lg font-medium flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': isProcessing || files.length < 2}"
                    >
                        <ArrowPathIcon v-if="isProcessing" class="w-5 h-5 animate-spin" />
                        <DocumentDuplicateIcon v-else class="w-5 h-5" />
                        {{ isProcessing ? 'Merging...' : 'Merge PDFs' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Download Modal Overlay -->
        <div v-if="downloadUrl" class="fixed inset-0 z-50 flex items-center justify-center bg-surface-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-surface-800 rounded-3xl shadow-2xl p-8 md:p-12 max-w-md w-full text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-emerald-500"></div>
                
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <CheckCircleIcon class="w-10 h-10" />
                </div>
                
                <h3 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Success!</h3>
                <p class="text-surface-600 dark:text-surface-400 mb-8">Your PDFs have been merged successfully.</p>
                
                <div class="space-y-4">
                    <a :href="downloadUrl" download class="btn-primary w-full py-4 rounded-xl text-lg font-medium flex items-center justify-center gap-2">
                        <ArrowDownTrayIcon class="w-6 h-6" />
                        Download Merged PDF
                    </a>
                    
                    <button @click="resetFull" class="w-full py-3 text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 font-medium transition-colors">
                        Merge More Files
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import draggable from 'vuedraggable';
import PdfUploadZone from '@/Components/PDF/PdfUploadZone.vue';
import { DocumentDuplicateIcon, ArrowPathIcon, CheckCircleIcon, ArrowDownTrayIcon, Bars2Icon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object
});

const files = ref([]);
const error = ref('');
const processError = ref('');
const isProcessing = ref(false);
const downloadUrl = ref('');
const addMoreInput = ref(null);

const onFilesSelected = (newFiles) => {
    error.value = '';
    processError.value = '';
    files.value = [...files.value, ...newFiles];
};

const addMoreFiles = (e) => {
    if (e.target.files.length) {
        onFilesSelected(Array.from(e.target.files));
    }
};

const removeFile = (index) => {
    files.value.splice(index, 1);
};

const reset = () => {
    files.value = [];
    processError.value = '';
};

const resetFull = () => {
    reset();
    downloadUrl.value = '';
};

const mergePdfs = async () => {
    if (files.value.length < 2) {
        processError.value = "You need at least 2 files to merge.";
        return;
    }
    
    isProcessing.value = true;
    processError.value = '';
    
    const formData = new FormData();
    files.value.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });
    
    try {
        const response = await axios.post('/api/pdf/merge', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            downloadUrl.value = response.data.download_url;
        } else {
            processError.value = response.data.message || 'An error occurred while merging the PDFs.';
        }
    } catch (err) {
        processError.value = err.response?.data?.message || 'Failed to connect to the server. Please try again.';
    } finally {
        isProcessing.value = false;
    }
};
</script>
