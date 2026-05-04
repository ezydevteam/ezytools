<template>
    <div class="max-w-6xl mx-auto py-8">
        <h1 class="text-3xl font-bold text-surface-900 dark:text-white mb-2">PDF Editor</h1>
        <p class="text-surface-600 dark:text-surface-400 mb-8">Edit PDF files directly in your browser. Add text, images, and drawings.</p>

        <div v-if="!file" class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8">
            <PdfUploadZone
                @files-selected="onFileSelected"
                @error="error = $event"
                :max-size="tool.settings?.free_max_mb * 1024 * 1024 || 10485760"
            />
            <p v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</p>
        </div>

        <div v-else class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 flex flex-col md:flex-row overflow-hidden h-[800px]">
            <!-- Sidebar: Page Thumbnails -->
            <div class="w-full md:w-64 border-r border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-900 flex flex-col h-[200px] md:h-full">
                <div class="p-4 border-b border-surface-200 dark:border-surface-700 flex justify-between items-center bg-white dark:bg-surface-800">
                    <h3 class="font-semibold text-sm">Pages ({{ pageOrder.length }})</h3>
                    <button @click="reset" class="text-xs text-red-500 hover:text-red-600 font-semibold">Close PDF</button>
                </div>
                <div class="flex-1 overflow-y-auto p-4 flex md:flex-col gap-4">
                    <div v-for="(page, index) in pageOrder" :key="page"
                         @click="changePage(page)"
                         class="flex-shrink-0 w-24 md:w-full aspect-[1/1.4] bg-white dark:bg-surface-800 shadow-sm border rounded-lg flex flex-col items-center justify-center cursor-pointer relative overflow-hidden transition-all group"
                         :class="currentPage === page ? 'border-primary-500 ring-2 ring-primary-500/50' : 'border-surface-200 dark:border-surface-700 hover:border-primary-300'">

                        <template v-if="pageStates[page] && (pageStates[page].thumbnail || pageStates[page].dataUrl)">
                            <!-- Base PDF Layer -->
                            <img v-if="pageStates[page].thumbnail" :src="pageStates[page].thumbnail" class="absolute inset-0 w-full h-full object-contain bg-white" />
                            <div v-else class="absolute inset-0 w-full h-full bg-white"></div>
                            <!-- Edits Layer -->
                            <img v-if="pageStates[page].dataUrl" :src="pageStates[page].dataUrl" class="absolute inset-0 w-full h-full object-contain z-10" />
                            <span class="absolute bottom-1 left-0 right-0 text-center text-[10px] font-bold text-surface-800 bg-white/70 backdrop-blur z-20">Page {{ index + 1 }}</span>
                        </template>
                        <template v-else>
                            <DocumentTextIcon class="w-10 h-10 text-surface-300 dark:text-surface-600 mb-2 group-hover:text-primary-400 transition-colors" />
                            <span class="text-xs font-bold text-surface-600 dark:text-surface-300">Page {{ index + 1 }}</span>
                        </template>

                        <button @click.stop="removePage(page, $event)" class="absolute top-2 left-2 p-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md shadow-sm opacity-0 group-hover:opacity-100 transition-opacity z-40" title="Remove Page">
                            <TrashIcon class="w-4 h-4" />
                        </button>

                        <button @click.stop="addNewPage(index)" class="absolute bottom-1 right-1 p-1.5 bg-primary-500 hover:bg-primary-600 text-white rounded-md shadow-sm opacity-0 group-hover:opacity-100 transition-opacity z-40" title="Add blank page after this one">
                            <PlusIcon class="w-4 h-4" />
                        </button>

                        <div v-if="pageStates[page] && pageStates[page].dataUrl" class="absolute top-2 right-2 w-2 h-2 bg-blue-500 rounded-full z-30" title="Edited"></div>
                    </div>
                </div>
            </div>

            <!-- Main Editor Area -->
            <div class="flex-1 flex flex-col relative h-[600px] md:h-full">
                <!-- Toolbar -->
                <div class="h-14 border-b border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 flex items-center px-4 gap-2 overflow-x-auto shrink-0">
                    <button @click="addText" class="p-2 rounded hover:bg-surface-100 dark:hover:bg-surface-700 flex items-center gap-1 transition-colors" :class="{'bg-primary-50 dark:bg-primary-900/30 text-primary-600': activeTool === 'text'}" title="Add Text">
                        <PencilSquareIcon class="w-5 h-5 text-surface-600 dark:text-surface-300" />
                        <span class="text-sm font-medium hidden lg:inline text-surface-700 dark:text-surface-200">Text</span>
                    </button>

                    <button @click="addHighlight" class="p-2 rounded hover:bg-surface-100 dark:hover:bg-surface-700 flex items-center gap-1 transition-colors" :class="{'bg-primary-50 dark:bg-primary-900/30 text-primary-600': activeTool === 'highlight'}" title="Highlight Area">
                        <StopIcon class="w-5 h-5 text-surface-600 dark:text-surface-300" />
                        <span class="text-sm font-medium hidden lg:inline text-surface-700 dark:text-surface-200">Highlight</span>
                    </button>
                    
                    <button @click="addWhiteout" class="p-2 rounded hover:bg-surface-100 dark:hover:bg-surface-700 flex items-center gap-1 transition-colors" title="Whiteout / Erase">
                        <div class="w-5 h-5 border border-surface-400 bg-white shadow-sm rounded-sm"></div>
                        <span class="text-sm font-medium hidden lg:inline text-surface-700 dark:text-surface-200">Whiteout</span>
                    </button>
                    
                    <button @click="$refs.imageInput.click()" class="p-2 rounded hover:bg-surface-100 dark:hover:bg-surface-700 flex items-center gap-1 transition-colors" title="Add Image">
                        <PhotoIcon class="w-5 h-5 text-surface-600 dark:text-surface-300" />
                        <span class="text-sm font-medium hidden lg:inline text-surface-700 dark:text-surface-200">Image</span>
                    </button>
                    <input type="file" ref="imageInput" class="hidden" accept="image/*" @change="addImage" />

                    <button @click="toggleDrawingMode" class="p-2 rounded flex items-center gap-1 transition-colors" :class="isDrawing ? 'bg-primary-100 dark:bg-primary-900/50 text-primary-600' : 'hover:bg-surface-100 dark:hover:bg-surface-700 text-surface-600 dark:text-surface-300'" title="Draw">
                        <PaintBrushIcon class="w-5 h-5" />
                        <span class="text-sm font-medium hidden lg:inline">Draw</span>
                    </button>

                    <div class="h-6 w-px bg-surface-200 dark:bg-surface-700 mx-1"></div>

                    <button @click="undo" :disabled="!canUndo" class="p-2 rounded hover:bg-surface-100 dark:hover:bg-surface-700 transition-colors" :class="{'opacity-50 cursor-not-allowed': !canUndo}" title="Undo (Ctrl+Z)">
                        <ArrowUturnLeftIcon class="w-5 h-5 text-surface-600 dark:text-surface-300" />
                    </button>

                    <button @click="deleteSelected" class="p-2 rounded hover:bg-red-50 text-red-500 transition-colors" title="Delete Selected">
                        <TrashIcon class="w-5 h-5" />
                    </button>

                    <button @click="savePdf" class="btn-primary px-4 py-1.5 rounded-lg text-sm ml-auto flex items-center gap-2 whitespace-nowrap">
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        Export PDF
                    </button>
                </div>

                <!-- Properties Sub-Toolbar -->
                <div v-if="activeTool" class="h-12 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50 flex items-center px-4 gap-4 overflow-x-auto shrink-0">

                    <!-- Text Settings -->
                    <template v-if="activeTool === 'text'">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-medium text-surface-500">Font:</label>
                            <select v-model="textOptions.fontFamily" @change="updateActiveObject" class="text-sm py-1 px-2 rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900 w-40">
                                <optgroup label="English Fonts">
                                    <option value="Arial">Arial</option>
                                    <option value="Times New Roman">Times New Roman</option>
                                    <option value="Helvetica">Helvetica</option>
                                    <option value="Courier New">Courier New</option>
                                    <option value="Georgia">Georgia</option>
                                </optgroup>
                                <optgroup label="Bangla Fonts">
                                    <option value="SutonnyMJ">SutonnyMJ</option>
                                    <option value="SutonnyMJ Bold">SutonnyMJ Bold</option>
                                    <option value="NikoshBAN">NikoshBAN</option>
                                    <option value="NikoshBAN Bold">NikoshBAN Bold</option>
                                    <option value="Hind Siliguri">Hind Siliguri</option>
                                    <option value="Kalpurush">Kalpurush</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-medium text-surface-500">Size:</label>
                            <input type="number" v-model="textOptions.fontSize" @change="updateActiveObject" min="8" max="120" class="w-16 text-sm py-1 px-2 rounded border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-900" />
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-medium text-surface-500">Color:</label>
                            <input type="color" v-model="textOptions.fill" @input="updateActiveObject" class="w-8 h-8 rounded cursor-pointer border-0 p-0" />
                        </div>
                        <div class="h-4 w-px bg-surface-300 dark:bg-surface-600 mx-1"></div>
                        <div class="flex items-center gap-1 bg-white dark:bg-surface-900 rounded border border-surface-300 dark:border-surface-600 p-0.5">
                            <button @click="toggleTextBold" class="w-7 h-7 rounded text-sm font-bold flex items-center justify-center transition-colors" :class="textOptions.fontWeight === 'bold' ? 'bg-surface-200 dark:bg-surface-700' : 'hover:bg-surface-100 dark:hover:bg-surface-800'" title="Bold">B</button>
                            <button @click="toggleTextItalic" class="w-7 h-7 rounded text-sm italic font-serif flex items-center justify-center transition-colors" :class="textOptions.fontStyle === 'italic' ? 'bg-surface-200 dark:bg-surface-700' : 'hover:bg-surface-100 dark:hover:bg-surface-800'" title="Italic">I</button>
                            <button @click="toggleBullet" class="w-7 h-7 rounded flex items-center justify-center transition-colors hover:bg-surface-100 dark:hover:bg-surface-800" title="Toggle Bullet">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-1 bg-white dark:bg-surface-900 rounded border border-surface-300 dark:border-surface-600 p-0.5">
                            <button @click="setTextAlign('left')" class="w-7 h-7 rounded flex items-center justify-center transition-colors" :class="textOptions.textAlign === 'left' ? 'bg-surface-200 dark:bg-surface-700' : 'hover:bg-surface-100 dark:hover:bg-surface-800'" title="Align Left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"></path></svg>
                            </button>
                            <button @click="setTextAlign('center')" class="w-7 h-7 rounded flex items-center justify-center transition-colors" :class="textOptions.textAlign === 'center' ? 'bg-surface-200 dark:bg-surface-700' : 'hover:bg-surface-100 dark:hover:bg-surface-800'" title="Align Center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M4 18h16"></path></svg>
                            </button>
                            <button @click="setTextAlign('right')" class="w-7 h-7 rounded flex items-center justify-center transition-colors" :class="textOptions.textAlign === 'right' ? 'bg-surface-200 dark:bg-surface-700' : 'hover:bg-surface-100 dark:hover:bg-surface-800'" title="Align Right">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M4 18h16"></path></svg>
                            </button>
                        </div>
                    </template>

                    <!-- Draw Settings -->
                    <template v-else-if="activeTool === 'draw'">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-medium text-surface-500">Brush Size:</label>
                            <input type="range" v-model="brushOptions.width" @input="updateBrush" min="1" max="50" class="w-24 accent-primary-500" />
                            <span class="text-xs text-surface-500 w-4">{{ brushOptions.width }}</span>
                        </div>
                        <div class="flex items-center gap-2 ml-2">
                            <label class="text-xs font-medium text-surface-500">Color:</label>
                            <input type="color" v-model="brushOptions.color" @input="updateBrush" class="w-8 h-8 rounded cursor-pointer border-0 p-0" />
                        </div>
                    </template>

                    <!-- Highlight Settings -->
                    <template v-else-if="activeTool === 'highlight'">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-medium text-surface-500">Highlight Color:</label>
                            <div class="flex gap-1">
                                <button v-for="color in ['#facc15', '#4ade80', '#60a5fa', '#f87171', '#c084fc']" :key="color"
                                    @click="highlightOptions.color = color; updateActiveObject()"
                                    class="w-6 h-6 rounded-full border-2 transition-transform hover:scale-110"
                                    :class="highlightOptions.color === color ? 'border-surface-800 dark:border-white' : 'border-transparent'"
                                    :style="{ backgroundColor: color }">
                                </button>
                            </div>
                        </div>
                    </template>

                </div>

                <!-- Canvas Area -->
                <div class="flex-1 bg-surface-100 dark:bg-surface-900 overflow-auto flex items-start justify-center p-4 md:p-8 relative" ref="canvasContainer">
                    <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-surface-100/80 dark:bg-surface-900/80 z-20">
                        <ArrowPathIcon class="w-10 h-10 text-primary-500 animate-spin" />
                    </div>

                    <!-- Fabric JS Overlay Canvas -->
                    <div class="relative shadow-xl inline-block transition-all" :class="{'opacity-0': isLoading}">
                        <!-- The underlying PDF Page -->
                        <VuePdfEmbed
                            v-if="pdfSource && !customPages.includes(currentPage)"
                            :source="pdfSource"
                            :page="currentPage"
                            :width="fitWidth"
                            :textLayer="false"
                            :annotationLayer="false"
                            @loaded="onPdfLoaded"
                            @rendered="onPageRendered"
                            class="block vue-pdf-strict bg-white"
                            ref="pdfEmbed"
                        />
                        <!-- Blank Page Placeholder -->
                        <div v-else-if="customPages.includes(currentPage)" class="bg-white shadow-sm block vue-pdf-strict" :style="{ width: pageDimensions.width + 'px', height: pageDimensions.height + 'px' }"></div>

                        <!-- Fabric JS Canvas Wrapper (MUST be absolute to overlay PDF) -->
                        <div class="absolute inset-0 z-10 touch-none">
                            <canvas ref="fabricCanvasEl"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bottom Navigation -->
                <div v-if="pdfSource" class="h-16 border-t border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 flex items-center justify-between px-6 shrink-0">
                    <button
                        @click="prevPage"
                        :disabled="!hasPrevPage || isLoading"
                        class="btn-secondary px-4 py-2 text-sm flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': !hasPrevPage || isLoading}"
                    >
                        &larr; Previous
                    </button>
                    <span class="text-sm font-bold text-surface-700 dark:text-surface-300">
                        Page {{ pageOrder.indexOf(currentPage) + 1 }} of {{ pageOrder.length }}
                    </span>
                    <button
                        @click="nextPage"
                        :disabled="!hasNextPage || isLoading"
                        class="btn-secondary px-4 py-2 text-sm flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': !hasNextPage || isLoading}"
                    >
                        Next &rarr;
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, shallowRef, computed } from 'vue';
import PdfUploadZone from '@/Components/PDF/PdfUploadZone.vue';
import VuePdfEmbed from 'vue-pdf-embed';
import * as fabric from 'fabric';
import {
    PencilSquareIcon, PhotoIcon, PaintBrushIcon,
    TrashIcon, ArrowDownTrayIcon, ArrowPathIcon, DocumentTextIcon, StopIcon, PlusIcon, ArrowUturnLeftIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    tool: Object
});

const file = ref(null);
const error = ref('');
const pdfSource = ref(null);
const totalPages = ref(0);
const currentPage = ref(1);
const isLoading = ref(false);

const canvasContainer = ref(null);
const fabricCanvasEl = ref(null);
const pdfEmbed = ref(null);
const imageInput = ref(null);

const fabricCanvas = shallowRef(null);
const isDrawing = ref(false);
const pageDimensions = ref({ width: 0, height: 0 });
const fitWidth = ref(800); // Default, updated on mount

// Store fabric JSON state for each page
const pageStates = ref({});
const customPages = ref([]); // Tracks which pages are entirely new blank pages
const deletedPages = ref([]); // Tracks which pages are removed
const pageOrder = ref([]); // Tracks the active sequence of pages

// Tool Settings State
const activeTool = ref(''); // 'text', 'draw', 'highlight'
const activeObject = ref(null);

const textOptions = ref({
    fontFamily: 'Arial',
    fontSize: 24,
    fill: '#000000',
    fontWeight: 'normal',
    fontStyle: 'normal',
    textAlign: 'left'
});

const historyStacks = ref({});
const isProcessingUndo = ref(false);

const canUndo = computed(() => {
    return historyStacks.value[currentPage.value] && historyStacks.value[currentPage.value].length > 1;
});

const saveHistory = () => {
    if (isProcessingUndo.value || !fabricCanvas.value) return;
    
    if (!historyStacks.value[currentPage.value]) {
        historyStacks.value[currentPage.value] = [];
    }
    
    historyStacks.value[currentPage.value].push(fabricCanvas.value.toJSON());
    if (historyStacks.value[currentPage.value].length > 20) {
        historyStacks.value[currentPage.value].shift();
    }
};

const undo = () => {
    const stack = historyStacks.value[currentPage.value];
    if (!stack || stack.length <= 1) return;
    
    isProcessingUndo.value = true;
    stack.pop(); // Remove current state
    const previousState = stack[stack.length - 1];
    
    fabricCanvas.value.loadFromJSON(previousState).then(() => {
        fabricCanvas.value.renderAll();
        isProcessingUndo.value = false;
        
        // Ensure navigation knows about undone state
        if (!pageStates.value[currentPage.value]) pageStates.value[currentPage.value] = {};
        pageStates.value[currentPage.value].json = previousState;
        pageStates.value[currentPage.value].dataUrl = fabricCanvas.value.toDataURL({ format: 'png', multiplier: 1 });
    }).catch(err => {
        console.error(err);
        isProcessingUndo.value = false;
    });
};

const brushOptions = ref({ color: '#ff0000', width: 3 });
const highlightOptions = ref({ color: '#facc15' }); // yellow

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    if (canvasContainer.value) {
        fitWidth.value = Math.max(canvasContainer.value.clientWidth - 64, 400);
    }
});

const onFileSelected = (files) => {
    error.value = '';
    const selectedFile = files[0];
    file.value = selectedFile;

    nextTick(() => {
        if (canvasContainer.value) {
            fitWidth.value = Math.max(canvasContainer.value.clientWidth - 64, 400);
        }
    });

    // Create object URL for pdf-embed
    pdfSource.value = URL.createObjectURL(selectedFile);

    // Reset states
    pageStates.value = {};
    currentPage.value = 1;
    isLoading.value = true;
};

const onPdfLoaded = (pdfDoc) => {
    if (totalPages.value === 0) {
        totalPages.value = pdfDoc.numPages;
        pageOrder.value = Array.from({length: pdfDoc.numPages}, (_, i) => i + 1);
    }
};

const onPageRendered = () => {
    if (pdfEmbed.value && pdfEmbed.value.$el) {
        pageDimensions.value = {
            width: pdfEmbed.value.$el.offsetWidth,
            height: pdfEmbed.value.$el.offsetHeight
        };
        
        // Capture thumbnail of the PDF canvas itself
        const canvasEl = pdfEmbed.value.$el.querySelector('canvas');
        if (canvasEl) {
            if (!pageStates.value[currentPage.value]) {
                pageStates.value[currentPage.value] = {};
            }
            try {
                pageStates.value[currentPage.value].thumbnail = canvasEl.toDataURL('image/jpeg', 0.5);
            } catch(e) {}
        }
    }
    isLoading.value = false;

    nextTick(() => {
        initFabric();
    });
};

const updateActiveObjectState = (e) => {
    const obj = e.selected[0];
    activeObject.value = obj;
    if (obj.type === 'i-text') {
        activeTool.value = 'text';
        textOptions.value = {
            fontFamily: obj.fontFamily,
            fontSize: obj.fontSize,
            fill: obj.fill,
            fontWeight: obj.fontWeight,
            fontStyle: obj.fontStyle,
            textAlign: obj.textAlign
        };
    } else if (obj.type === 'rect') {
        activeTool.value = 'highlight';
        highlightOptions.value.color = obj.fill;
    }
};

const initFabric = () => {
    if (fabricCanvas.value) {
        fabricCanvas.value.dispose();
    }

    if (!fabricCanvasEl.value || pageDimensions.value.width === 0) return;

    fabricCanvas.value = new fabric.Canvas(fabricCanvasEl.value, {
        width: pageDimensions.value.width,
        height: pageDimensions.value.height,
        preserveObjectStacking: true
    });

    // Setup drawing brush
    fabricCanvas.value.freeDrawingBrush = new fabric.PencilBrush(fabricCanvas.value);
    updateBrush();

    // Event listeners for active selection
    fabricCanvas.value.on('selection:created', updateActiveObjectState);
    fabricCanvas.value.on('selection:updated', updateActiveObjectState);
    fabricCanvas.value.on('selection:cleared', () => {
        activeObject.value = null;
        if (activeTool.value !== 'draw') {
            activeTool.value = '';
        }
    });

    const finalizeInit = () => {
        fabricCanvas.value.on('object:modified', saveHistory);
        fabricCanvas.value.on('object:added', saveHistory);
        fabricCanvas.value.on('object:removed', saveHistory);
        
        if (!historyStacks.value[currentPage.value] || historyStacks.value[currentPage.value].length === 0) {
            saveHistory();
        }
    };

    // Load state if exists
    if (pageStates.value[currentPage.value] && pageStates.value[currentPage.value].json) {
        fabricCanvas.value.loadFromJSON(pageStates.value[currentPage.value].json)
            .then(() => {
                fabricCanvas.value.renderAll();
                finalizeInit();
            })
            .catch(err => {
                console.error("Error loading Fabric JSON:", err);
                finalizeInit();
            });
    } else {
        finalizeInit();
    }
};

const updateBrush = () => {
    if (fabricCanvas.value && fabricCanvas.value.freeDrawingBrush) {
        fabricCanvas.value.freeDrawingBrush.color = brushOptions.value.color;
        fabricCanvas.value.freeDrawingBrush.width = parseInt(brushOptions.value.width);
    }
};

const toggleTextBold = () => {
    textOptions.value.fontWeight = textOptions.value.fontWeight === 'bold' ? 'normal' : 'bold';
    updateActiveObject();
};

const toggleTextItalic = () => {
    textOptions.value.fontStyle = textOptions.value.fontStyle === 'italic' ? 'normal' : 'italic';
    updateActiveObject();
};

const setTextAlign = (align) => {
    textOptions.value.textAlign = align;
    updateActiveObject();
};

const toggleBullet = () => {
    if (!activeObject.value || activeObject.value.type !== 'i-text') return;
    let text = activeObject.value.text;
    if (text.startsWith('• ')) {
        text = text.replace(/^• /gm, ''); // remove from all lines
    } else {
        text = text.replace(/^/gm, '• '); // add to all lines
    }
    activeObject.value.set('text', text);
    fabricCanvas.value.renderAll();
    saveHistory(); // manually trigger history save
};

const updateActiveObject = () => {
    if (!activeObject.value || !fabricCanvas.value) return;

    if (activeObject.value.type === 'i-text') {
        activeObject.value.set({
            fontFamily: textOptions.value.fontFamily,
            fontSize: parseInt(textOptions.value.fontSize),
            fill: textOptions.value.fill,
            fontWeight: textOptions.value.fontWeight,
            fontStyle: textOptions.value.fontStyle,
            textAlign: textOptions.value.textAlign,
            editable: true
        });
    } else if (activeObject.value.type === 'rect') {
        activeObject.value.set({ fill: highlightOptions.value.color });
    }
    fabricCanvas.value.renderAll();
    saveHistory();
};

const currentIndex = computed(() => pageOrder.value.indexOf(currentPage.value));

const hasPrevPage = computed(() => currentIndex.value > 0);
const hasNextPage = computed(() => currentIndex.value >= 0 && currentIndex.value < pageOrder.value.length - 1);

const prevPage = () => {
    if (hasPrevPage.value) changePage(pageOrder.value[currentIndex.value - 1]);
};

const nextPage = () => {
    if (hasNextPage.value) changePage(pageOrder.value[currentIndex.value + 1]);
};

const removePage = (pageNo, event) => {
    if (event) event.stopPropagation();
    
    if (!deletedPages.value.includes(pageNo)) {
        deletedPages.value.push(pageNo);
    }

    const index = pageOrder.value.indexOf(pageNo);
    if (index > -1) {
        pageOrder.value.splice(index, 1);
    }
    
    if (currentPage.value === pageNo) {
        // Navigate away from deleted page
        if (pageOrder.value.length > 0) {
            const nextIndex = Math.min(index, pageOrder.value.length - 1);
            changePage(pageOrder.value[nextIndex]);
        }
    }
};

const changePage = (newPage) => {
    if (newPage === currentPage.value || deletedPages.value.includes(newPage)) return;

    // Save current state
    if (fabricCanvas.value) {
        if (!pageStates.value[currentPage.value]) pageStates.value[currentPage.value] = {};
        pageStates.value[currentPage.value].dataUrl = fabricCanvas.value.toDataURL({ format: 'png', multiplier: 1 });
        pageStates.value[currentPage.value].json = fabricCanvas.value.toJSON();
    }

    isLoading.value = true;
    currentPage.value = newPage;
    
    if (customPages.value.includes(newPage)) {
        // It's a custom blank page! Manually trigger load completion
        pageDimensions.value = { width: fitWidth.value, height: fitWidth.value * 1.414 }; // Standard A4 ratio
        setTimeout(() => {
            isLoading.value = false;
            nextTick(() => {
                initFabric();
            });
        }, 50);
    }
};

const addNewPage = (afterIndex = -1) => {
    // Save current state before switching
    if (fabricCanvas.value) {
        if (!pageStates.value[currentPage.value]) pageStates.value[currentPage.value] = {};
        pageStates.value[currentPage.value].dataUrl = fabricCanvas.value.toDataURL({ format: 'png', multiplier: 1 });
        pageStates.value[currentPage.value].json = fabricCanvas.value.toJSON();
    }

    totalPages.value++;
    customPages.value.push(totalPages.value);
    
    if (afterIndex >= 0) {
        pageOrder.value.splice(afterIndex + 1, 0, totalPages.value);
    } else {
        pageOrder.value.push(totalPages.value);
    }
    
    // Switch to new page
    isLoading.value = true;
    currentPage.value = totalPages.value;
    
    // Simulate render completion for custom blank page
    pageDimensions.value = { width: fitWidth.value, height: fitWidth.value * 1.414 }; // A4 ratio
    setTimeout(() => {
        isLoading.value = false;
        nextTick(() => {
            initFabric();
        });
    }, 50);
};

const addText = () => {
    if (!fabricCanvas.value) return;

    const text = new fabric.IText('Edit this text', {
        left: 50,
        top: 50,
        fontFamily: textOptions.value.fontFamily,
        fontSize: parseInt(textOptions.value.fontSize),
        fill: textOptions.value.fill,
        fontWeight: textOptions.value.fontWeight,
        fontStyle: textOptions.value.fontStyle,
        textAlign: textOptions.value.textAlign,
        editable: true
    });

    fabricCanvas.value.add(text);
    fabricCanvas.value.setActiveObject(text);
    fabricCanvas.value.renderAll();
    isDrawing.value = false;
    fabricCanvas.value.isDrawingMode = false;
    activeTool.value = 'text';
};

const addHighlight = () => {
    if (!fabricCanvas.value) return;
    
    const rect = new fabric.Rect({
        left: 50,
        top: 50,
        width: 150,
        height: 50,
        fill: highlightOptions.value.color,
        opacity: 0.5,
        transparentCorners: false,
        cornerColor: '#00a8ff'
    });
    
    fabricCanvas.value.add(rect);
    fabricCanvas.value.setActiveObject(rect);
    fabricCanvas.value.renderAll();
    isDrawing.value = false;
    fabricCanvas.value.isDrawingMode = false;
    activeTool.value = 'highlight';
};

const addWhiteout = () => {
    if (!fabricCanvas.value) return;
    
    const rect = new fabric.Rect({
        left: 50,
        top: 50,
        width: 150,
        height: 50,
        fill: '#ffffff',
        opacity: 1,
        transparentCorners: false,
        cornerColor: '#00a8ff'
    });
    
    fabricCanvas.value.add(rect);
    fabricCanvas.value.setActiveObject(rect);
    fabricCanvas.value.renderAll();
    isDrawing.value = false;
    fabricCanvas.value.isDrawingMode = false;
    activeTool.value = '';
};

const addImage = (e) => {
    if (!fabricCanvas.value || !e.target.files.length) return;

    const imgFile = e.target.files[0];
    const reader = new FileReader();

    reader.onload = (f) => {
        const data = f.target.result;
        fabric.Image.fromURL(data).then((img) => {
            img.scaleToWidth(200);
            img.set({ left: 50, top: 50 });
            fabricCanvas.value.add(img);
            fabricCanvas.value.setActiveObject(img);
            fabricCanvas.value.renderAll();
        });
    };
    reader.readAsDataURL(imgFile);
    e.target.value = ''; // reset
};

const toggleDrawingMode = () => {
    if (!fabricCanvas.value) return;
    isDrawing.value = !isDrawing.value;
    fabricCanvas.value.isDrawingMode = isDrawing.value;

    if (isDrawing.value) {
        fabricCanvas.value.discardActiveObject();
        fabricCanvas.value.renderAll();
        activeTool.value = 'draw';
        updateBrush();
    } else {
        activeTool.value = '';
    }
};

const deleteSelected = () => {
    if (!fabricCanvas.value) return;
    const activeObjects = fabricCanvas.value.getActiveObjects();
    if (activeObjects.length) {
        fabricCanvas.value.discardActiveObject();
        activeObjects.forEach(obj => {
            fabricCanvas.value.remove(obj);
        });
    }
};

const savePdf = async () => {
    // Save current page state
    if (fabricCanvas.value) {
        pageStates.value[currentPage.value] = {
            dataUrl: fabricCanvas.value.toDataURL({ format: 'png', multiplier: 1 }),
            json: fabricCanvas.value.toJSON()
        };
    }

    isLoading.value = true;
    error.value = '';

    try {
        const formData = new FormData();
        formData.append('file', file.value);

        let hasEdits = false;
        const edits = {};

        // Loop through all pages that have state
        for (const [page, state] of Object.entries(pageStates.value)) {
            if (state && state.dataUrl) {
                // To avoid sending empty canvases, we could check if objects exist
                if (state.json.objects && state.json.objects.length > 0) {
                    edits[page] = state.dataUrl;
                    hasEdits = true;
                }
            }
        }

        if (!hasEdits && deletedPages.value.length === 0 && customPages.value.length === 0 && pageOrder.value.length === totalPages.value - customPages.value.length) {
            error.value = 'No edits, additions, or removals found. Please modify the PDF before saving.';
            isLoading.value = false;
            return;
        }

        formData.append('edits', JSON.stringify(edits));
        formData.append('deleted_pages', JSON.stringify(deletedPages.value));
        formData.append('page_order', JSON.stringify(pageOrder.value));

        const response = await axios.post('/api/pdf/edit', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (response.data.success) {
            // Trigger download
            const link = document.createElement('a');
            link.href = response.data.download_url;
            link.setAttribute('download', 'edited.pdf');
            document.body.appendChild(link);
            link.click();
            link.remove();
        } else {
            error.value = response.data.message || 'Error saving PDF.';
        }

    } catch (e) {
        console.error(e);
        error.value = e.response?.data?.message || "Failed to export PDF.";
    } finally {
        isLoading.value = false;
    }
};

const reset = () => {
    if (pdfSource.value) {
        URL.revokeObjectURL(pdfSource.value);
    }
    file.value = null;
    pdfSource.value = null;
    pageStates.value = {};
    if (fabricCanvas.value) {
        fabricCanvas.value.dispose();
        fabricCanvas.value = null;
    }
};

// Listen for keyboard delete key and undo
const handleKeyDown = (e) => {
    if ((e.key === 'Delete' || e.key === 'Backspace') && fabricCanvas.value) {
        // Prevent deleting if we're editing text
        if (fabricCanvas.value.getActiveObject()?.isEditing) return;
        deleteSelected();
    }
    if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
        // Prevent undo while typing
        if (fabricCanvas.value?.getActiveObject()?.isEditing) return;
        e.preventDefault();
        undo();
    }
};

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeyDown);
    if (pdfSource.value) URL.revokeObjectURL(pdfSource.value);
    if (fabricCanvas.value) fabricCanvas.value.dispose();
});
</script>

<style>
/* Bulletproof removal of invisible layers created by vue-pdf-embed */
div[class*="textLayer"],
div[class*="annotationLayer"],
div[class*="text-layer"],
div[class*="annotation-layer"] {
    display: none !important;
    height: 0 !important;
    width: 0 !important;
    position: absolute !important;
}

/* Ensure the wrapper only takes up the canvas space */
.vue-pdf-strict {
    display: flex;
    justify-content: center;
    align-items: center;
}
.vue-pdf-strict canvas {
    display: block !important;
}
</style>
