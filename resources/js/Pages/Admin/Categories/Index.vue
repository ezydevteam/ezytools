<template>
    <AdminLayout>
        <Head title="Manage Categories" />

        <template #header>
            Categories Management
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Add Category Form -->
            <div class="bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 p-6">
                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">
                    {{ editing ? 'Edit Category' : 'Add New Category' }}
                </h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Name (English)</label>
                        <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Description</label>
                        <input type="text" v-model="form.description" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Slug</label>
                        <input type="text" v-model="form.slug" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                        <div v-if="form.errors.slug" class="text-red-500 text-xs mt-1">{{ form.errors.slug }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300">Icon (Heroicon Outline Name)</label>
                        <input type="text" v-model="form.icon" class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-surface-900 dark:border-surface-700" />
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" v-model="form.is_active" class="rounded border-surface-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200" />
                            <span class="ml-2 text-sm text-surface-600 dark:text-surface-300">Is Active</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <button type="button" v-if="editing" @click="cancelEdit" class="text-sm text-surface-500 hover:text-surface-700">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 transition ml-auto">
                            {{ editing ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Category List with Drag & Drop -->
            <div class="md:col-span-2 bg-white dark:bg-surface-800 shadow-sm rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50/50 dark:bg-surface-900/30">
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-semibold text-surface-900 dark:text-white uppercase tracking-wider">Categories</h3>
                        <span class="text-xs bg-primary-100 dark:bg-primary-900/30 text-primary-600 px-2 py-0.5 rounded-full font-medium">{{ localCategories.length }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <transition name="fade">
                            <span v-if="orderChanged" class="text-xs text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                Unsaved
                            </span>
                        </transition>
                        <button v-if="orderChanged" @click="saveOrder" :disabled="saving"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white text-xs font-medium rounded-lg transition-colors">
                            <svg v-if="saving" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ saving ? 'Saving...' : 'Save Order' }}
                        </button>
                    </div>
                </div>

                <!-- Drag & Drop List -->
                <div ref="listRef" class="divide-y divide-surface-100 dark:divide-surface-700/50">
                    <div v-for="(category, index) in localCategories"
                         :key="category.id"
                         :data-id="category.id"
                         draggable="true"
                         @dragstart="onDragStart($event, index)"
                         @dragover.prevent="onDragOver($event, index)"
                         @dragenter.prevent="onDragEnter(index)"
                         @dragleave="onDragLeave(index)"
                         @drop.prevent="onDrop($event, index)"
                         @dragend="onDragEnd"
                         :class="[
                             dragOverIndex === index ? 'border-t-2 border-t-primary-500 bg-primary-50/50 dark:bg-primary-900/10' : '',
                             dragIndex === index ? 'opacity-40 scale-[0.98]' : '',
                         ]"
                         class="flex items-center gap-4 px-6 py-3.5 group transition-all duration-150 hover:bg-surface-50 dark:hover:bg-surface-900/30 cursor-grab active:cursor-grabbing select-none">

                        <!-- Drag Handle -->
                        <div class="flex-shrink-0 text-surface-300 dark:text-surface-600 group-hover:text-surface-500 dark:group-hover:text-surface-400 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                            </svg>
                        </div>

                        <!-- Order Number -->
                        <span class="w-6 h-6 flex items-center justify-center rounded-md bg-surface-100 dark:bg-surface-900 text-xs font-bold text-surface-500">
                            {{ index + 1 }}
                        </span>

                        <!-- Category Info -->
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-surface-900 dark:text-white">{{ category.name }}</div>
                            <div class="text-xs text-surface-400 mt-0.5">{{ category.slug }}</div>
                        </div>

                        <!-- Status -->
                        <span :class="[category.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400']"
                              class="px-2 py-0.5 text-xs font-semibold rounded-full whitespace-nowrap">
                            {{ category.is_active ? 'Active' : 'Inactive' }}
                        </span>

                        <!-- Tools Count -->
                        <span class="text-xs text-surface-500 bg-surface-100 dark:bg-surface-900 px-2 py-1 rounded-lg font-medium whitespace-nowrap">
                            {{ category.tools_count }} tools
                        </span>

                        <!-- Actions -->
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="editCategory(category)" class="p-1.5 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 text-primary-600 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button @click="deleteCategory(category)" :disabled="category.tools_count > 0"
                                    :class="[category.tools_count > 0 ? 'text-surface-300 dark:text-surface-600 cursor-not-allowed' : 'text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30']"
                                    class="p-1.5 rounded-lg transition-colors" title="Delete">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!localCategories.length" class="p-12 text-center text-surface-400">
                    <p class="text-lg">No categories yet.</p>
                    <p class="text-sm mt-1">Create your first category using the form.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    categories: Array,
});

// Local reactive copy for drag & drop
const localCategories = ref([...props.categories]);
const orderChanged = ref(false);
const saving = ref(false);

// Track original order to detect changes
const originalOrder = ref(props.categories.map(c => c.id));

// Sync when props change (after successful save)
watch(() => props.categories, (newVal) => {
    localCategories.value = [...newVal];
    originalOrder.value = newVal.map(c => c.id);
    orderChanged.value = false;
});

// Drag state
const dragIndex = ref(null);
const dragOverIndex = ref(null);

const onDragStart = (e, index) => {
    dragIndex.value = index;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', index.toString());
    // Add slight delay for visual feedback
    requestAnimationFrame(() => {
        if (dragIndex.value !== null) {
            // visual feedback applied via class binding
        }
    });
};

const onDragOver = (e, index) => {
    e.dataTransfer.dropEffect = 'move';
};

const onDragEnter = (index) => {
    if (index !== dragIndex.value) {
        dragOverIndex.value = index;
    }
};

const onDragLeave = (index) => {
    if (dragOverIndex.value === index) {
        dragOverIndex.value = null;
    }
};

const onDrop = (e, dropIndex) => {
    const fromIndex = dragIndex.value;
    dragOverIndex.value = null;

    if (fromIndex === null || fromIndex === dropIndex) return;

    // Move item
    const items = [...localCategories.value];
    const [moved] = items.splice(fromIndex, 1);
    items.splice(dropIndex, 0, moved);
    localCategories.value = items;

    // Check if order changed
    const currentOrder = items.map(c => c.id);
    orderChanged.value = JSON.stringify(currentOrder) !== JSON.stringify(originalOrder.value);
};

const onDragEnd = () => {
    dragIndex.value = null;
    dragOverIndex.value = null;
};

// Save new order
const saveOrder = () => {
    saving.value = true;
    const ids = localCategories.value.map(c => c.id);

    router.post(route('admin.categories.reorder'), { ids }, {
        preserveScroll: true,
        onSuccess: () => {
            orderChanged.value = false;
            originalOrder.value = ids;
        },
        onFinish: () => {
            saving.value = false;
        },
    });
};

// ── Form Logic ──
const editing = ref(false);
const editId = ref(null);

const form = useForm({
    name: '',
    description: '',
    slug: '',
    icon: '',
    order: 0,
    is_active: true,
});

const submit = () => {
    if (editing.value) {
        form.put(route('admin.categories.update', editId.value), {
            onSuccess: () => cancelEdit(),
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => form.reset(),
        });
    }
};

const editCategory = (category) => {
    editing.value = true;
    editId.value = category.id;
    form.name = category.name;
    form.description = category.description;
    form.slug = category.slug;
    form.icon = category.icon;
    form.order = category.order;
    form.is_active = category.is_active;
};

const cancelEdit = () => {
    editing.value = false;
    editId.value = null;
    form.reset();
};

const deleteCategory = (category) => {
    if (category.tools_count > 0) return;
    if (confirm(`Are you sure you want to delete ${category.name}?`)) {
        router.delete(route('admin.categories.destroy', category.id));
    }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
