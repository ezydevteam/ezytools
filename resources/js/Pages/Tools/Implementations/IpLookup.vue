<template>
    <div class="space-y-6">
        <!-- Input -->
        <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6">
            <div class="max-w-2xl mx-auto">
                <label class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2 block">Enter IP address or domain</label>
                <div class="flex gap-3">
                    <input v-model="input" type="text" placeholder="8.8.8.8 or google.com (leave empty for your IP)"
                           @keyup.enter="lookup"
                           class="flex-1 px-4 py-3 rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500" />
                    <button @click="lookup" :disabled="isLoading"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 disabled:opacity-50 text-white font-semibold rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
                        <svg v-if="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        🌐 Lookup
                    </button>
                </div>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-400 font-medium">{{ error }}</div>

        <!-- Result -->
        <div v-if="result" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- IP Card -->
            <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-6 md:col-span-2">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center text-white text-2xl shadow-lg">🌐</div>
                    <div>
                        <p class="text-3xl font-black text-surface-900 dark:text-white font-mono">{{ result.ip }}</p>
                        <p class="text-sm text-surface-500 mt-1">{{ result.city ? result.city + ', ' : '' }}{{ result.region ? result.region + ', ' : '' }}{{ result.country }}</p>
                    </div>
                    <div class="ml-auto" v-if="result.country_code">
                        <span class="text-5xl">{{ countryFlag(result.country_code) }}</span>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div v-for="(item, i) in infoCards" :key="i" class="bg-white dark:bg-surface-800 rounded-xl shadow-sm border border-surface-200 dark:border-surface-700 p-5">
                <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider mb-1">{{ item.label }}</p>
                <p class="text-base font-semibold text-surface-900 dark:text-white break-all">{{ item.value || '—' }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const input = ref('');
const isLoading = ref(false);
const error = ref(null);
const result = ref(null);

const infoCards = computed(() => {
    if (!result.value) return [];
    return [
        { label: 'Country', value: result.value.country },
        { label: 'Region', value: result.value.region },
        { label: 'City', value: result.value.city },
        { label: 'ZIP', value: result.value.zip },
        { label: 'Timezone', value: result.value.timezone },
        { label: 'Latitude', value: result.value.lat },
        { label: 'Longitude', value: result.value.lon },
        { label: 'ISP', value: result.value.isp },
        { label: 'Organization', value: result.value.org },
        { label: 'AS Number', value: result.value.as },
        { label: 'Continent', value: result.value.continent },
    ];
});

const countryFlag = (code) => {
    if (!code) return '';
    return String.fromCodePoint(...[...code.toUpperCase()].map(c => 0x1F1E6 + c.charCodeAt(0) - 65));
};

const lookup = async () => {
    isLoading.value = true;
    error.value = null;
    result.value = null;

    try {
        const res = await axios.post('/api/web-tools/ip-lookup', { ip: input.value || null });
        result.value = res.data;
    } catch (e) {
        error.value = e.response?.data?.message || 'IP lookup failed.';
    } finally {
        isLoading.value = false;
    }
};
</script>
