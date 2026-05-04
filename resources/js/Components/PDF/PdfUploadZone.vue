<template>
    <div 
        class="border-2 border-dashed rounded-xl p-8 text-center transition-colors relative"
        :class="isDragging ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10' : 'border-surface-300 dark:border-surface-600 hover:border-primary-400 dark:hover:border-primary-500'"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
    >
        <div class="flex flex-col items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                <DocumentArrowUpIcon class="w-8 h-8 text-primary-600 dark:text-primary-400" />
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-surface-900 dark:text-white mb-1">
                    Upload PDF File
                </h3>
                <p class="text-sm text-surface-500 dark:text-surface-400">
                    Drag and drop your file here, or click to browse
                </p>
            </div>

            <input 
                type="file" 
                ref="fileInput" 
                class="hidden" 
                :accept="accept" 
                :multiple="multiple"
                @change="handleFileSelect"
            >
            
            <button 
                @click="$refs.fileInput.click()"
                class="btn-primary px-6 py-2.5 rounded-lg text-sm font-semibold mt-2"
            >
                Select PDF File
            </button>
            
            <p v-if="maxSize" class="text-xs text-surface-400 mt-2">
                Max file size: {{ formatSize(maxSize) }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { DocumentArrowUpIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    accept: { type: String, default: '.pdf' },
    multiple: { type: Boolean, default: false },
    maxSize: { type: Number, default: 10 * 1024 * 1024 }, // default 10MB
    maxFiles: { type: Number, default: 1 }
});

const emit = defineEmits(['files-selected', 'error']);
const isDragging = ref(false);
const fileInput = ref(null);

const formatSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const validateFiles = (files) => {
    const validFiles = [];
    
    if (files.length > props.maxFiles) {
        emit('error', `You can only upload up to ${props.maxFiles} files.`);
        return [];
    }

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (file.size > props.maxSize) {
            emit('error', `File ${file.name} exceeds maximum size of ${formatSize(props.maxSize)}`);
            continue;
        }
        if (props.accept.includes('.pdf') && file.type !== 'application/pdf') {
            emit('error', `File ${file.name} is not a valid PDF.`);
            continue;
        }
        validFiles.push(file);
    }
    
    return validFiles;
};

const processFiles = (fileList) => {
    const valid = validateFiles(fileList);
    if (valid.length > 0) {
        emit('files-selected', valid);
    }
};

const handleDrop = (e) => {
    isDragging.value = false;
    processFiles(e.dataTransfer.files);
};

const handleFileSelect = (e) => {
    processFiles(e.target.files);
    e.target.value = ''; // reset
};
</script>
