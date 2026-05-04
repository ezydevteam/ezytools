<template>
    <div class="p-4 bg-white dark:bg-surface-800 rounded-xl border transition-all hover:shadow-sm"
         :class="statusStyles.border">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <!-- Status icon -->
                <div class="mt-0.5 w-6 h-6 flex-shrink-0 flex items-center justify-center rounded-full"
                     :class="statusStyles.iconBg">
                    <component :is="statusStyles.icon" class="w-4 h-4" :class="statusStyles.iconColor" />
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="text-sm font-semibold text-surface-900 dark:text-white leading-none">
                            {{ title }}
                        </h4>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-md" :class="statusStyles.badge">
                            {{ status === 'passed' ? 'Passed' : status === 'warning' ? 'Warning' : 'Critical' }}
                        </span>
                        <span v-if="value" class="text-xs text-surface-400 dark:text-surface-500 font-mono bg-surface-50 dark:bg-surface-900 px-1.5 py-0.5 rounded border border-surface-200/50 dark:border-surface-700/50">
                            {{ value }}
                        </span>
                    </div>
                    
                    <p class="text-xs md:text-sm text-surface-600 dark:text-surface-300 leading-relaxed">
                        {{ message }}
                    </p>

                    <!-- Recommendations box -->
                    <div v-if="recommendation" class="mt-3 bg-surface-50 dark:bg-surface-900/40 p-3 rounded-xl border border-surface-100 dark:border-surface-700/60 text-xs">
                        <span class="font-bold text-surface-700 dark:text-surface-200 block mb-1">💡 Suggestion:</span>
                        <p class="text-surface-600 dark:text-surface-400 leading-relaxed">{{ recommendation }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { CheckCircleIcon, ExclamationTriangleIcon, XCircleIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    title: { type: String, required: true },
    status: { type: String, default: 'passed' },
    message: { type: String, required: true },
    value: { type: String, default: '' },
    recommendation: { type: String, default: '' }
});

const statusStyles = computed(() => {
    switch (props.status) {
        case 'warning':
            return {
                border: 'border-amber-200 dark:border-amber-900/40 bg-amber-50/20 dark:bg-amber-950/10',
                iconBg: 'bg-amber-100 dark:bg-amber-900/50',
                iconColor: 'text-amber-600 dark:text-amber-400',
                badge: 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200',
                icon: ExclamationTriangleIcon
            };
        case 'critical':
            return {
                border: 'border-red-200 dark:border-red-900/40 bg-red-50/20 dark:bg-red-950/10',
                iconBg: 'bg-red-100 dark:bg-red-900/50',
                iconColor: 'text-red-600 dark:text-red-400',
                badge: 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-200',
                icon: XCircleIcon
            };
        default:
            return {
                border: 'border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800',
                iconBg: 'bg-green-100 dark:bg-green-900/50',
                iconColor: 'text-green-600 dark:text-green-400',
                badge: 'bg-green-100 text-green-800 dark:bg-green-900/60 dark:text-green-200',
                icon: CheckCircleIcon
            };
    }
});
</script>
