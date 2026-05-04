<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        
        <div class="mb-8">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-4">Location Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">City</label>
                    <input type="text" v-model="city" @change="fetchPrayerTimes" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="e.g. Dhaka">
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">Country</label>
                    <input type="text" v-model="country" @change="fetchPrayerTimes" class="block w-full rounded-xl border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-900 text-surface-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 transition-colors" placeholder="e.g. Bangladesh">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button @click="fetchPrayerTimes" :disabled="loading" class="px-5 py-2.5 bg-gradient-to-r from-primary-600 to-purple-600 hover:from-primary-700 hover:to-purple-700 text-white font-medium rounded-xl transition-colors shadow-md text-sm flex items-center gap-2">
                    <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Update Times
                </button>
            </div>
        </div>

        <div v-if="error" class="p-4 bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded-xl mb-6 text-sm text-center">
            {{ error }}
        </div>

        <div v-if="prayerTimes" class="bg-surface-50 dark:bg-surface-900 rounded-2xl border border-surface-200 dark:border-surface-700 overflow-hidden">
            <div class="bg-primary-600 text-white p-4 text-center">
                <h4 class="font-bold text-lg">{{ dateGregorian }}</h4>
                <p class="text-primary-100 text-sm">{{ dateHijri }}</p>
            </div>
            
            <div class="divide-y divide-surface-200 dark:divide-surface-700">
                <div v-for="(time, name) in filteredTimes" :key="name" class="flex justify-between items-center p-4 hover:bg-surface-100 dark:hover:bg-surface-800 transition-colors" :class="{ 'bg-primary-50 dark:bg-primary-900/20': isNextPrayer(name) }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="getPrayerIconBg(name)">
                            <svg class="w-5 h-5" :class="getPrayerIconColor(name)" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getPrayerIcon(name)" />
                            </svg>
                        </div>
                        <span class="font-bold text-surface-900 dark:text-white text-lg">{{ name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold text-surface-900 dark:text-white">{{ formatTime(time) }}</span>
                        <span v-if="isNextPrayer(name)" class="block text-xs font-medium text-primary-600 dark:text-primary-400 mt-1">UPCOMING</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="!prayerTimes && !loading && !error" class="text-center p-8 text-surface-500">
            Click update to load prayer times for your location.
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const city = ref('Dhaka');
const country = ref('Bangladesh');
const loading = ref(false);
const error = ref('');
const prayerTimesData = ref(null);

const fetchPrayerTimes = async () => {
    if (!city.value || !country.value) return;
    
    loading.value = true;
    error.value = '';
    
    try {
        const response = await fetch(`https://api.aladhan.com/v1/timingsByCity?city=${encodeURIComponent(city.value)}&country=${encodeURIComponent(country.value)}&method=1`);
        const data = await response.json();
        
        if (data.code === 200) {
            prayerTimesData.value = data.data;
        } else {
            error.value = 'Could not fetch prayer times for this location.';
        }
    } catch (e) {
        error.value = 'Network error. Please try again later.';
    } finally {
        loading.value = false;
    }
};

const prayerTimes = computed(() => prayerTimesData.value?.timings || null);
const dateGregorian = computed(() => prayerTimesData.value?.date?.readable || '');
const dateHijri = computed(() => {
    const h = prayerTimesData.value?.date?.hijri;
    if (!h) return '';
    return `${h.day} ${h.month.en} ${h.year} AH`;
});

const filteredTimes = computed(() => {
    if (!prayerTimes.value) return {};
    const wanted = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
    const result = {};
    wanted.forEach(w => {
        if (prayerTimes.value[w]) result[w] = prayerTimes.value[w];
    });
    return result;
});

// Convert HH:MM to 12-hour AM/PM format
const formatTime = (timeStr) => {
    if (!timeStr) return '';
    // timeStr is usually "HH:MM" occasionally "HH:MM (PKT)"
    const base = timeStr.split(' ')[0];
    const [h, m] = base.split(':');
    let hours = parseInt(h);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    return `${hours}:${m} ${ampm}`;
};

// Check which prayer is next based on current local time
const isNextPrayer = (name) => {
    if (!prayerTimes.value) return false;
    
    const now = new Date();
    const currentTimeStr = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
    
    const wanted = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
    
    for (let i = 0; i < wanted.length; i++) {
        const pTime = prayerTimes.value[wanted[i]].split(' ')[0];
        if (currentTimeStr < pTime) {
            return name === wanted[i];
        }
    }
    // If we passed Isha, next is Fajr tomorrow
    return name === 'Fajr';
};

const getPrayerIcon = (name) => {
    switch (name) {
        case 'Fajr': return 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'; // Moon
        case 'Sunrise': return 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'; // Sun
        case 'Dhuhr': return 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'; // Sun
        case 'Asr': return 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'; // Cloud/Afternoon
        case 'Maghrib': return 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'; // Moon
        case 'Isha': return 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'; // Moon
        default: return 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'; // Clock
    }
};

const getPrayerIconBg = (name) => {
    if (['Fajr', 'Maghrib', 'Isha'].includes(name)) return 'bg-indigo-100 dark:bg-indigo-900/30';
    return 'bg-orange-100 dark:bg-orange-900/30';
};

const getPrayerIconColor = (name) => {
    if (['Fajr', 'Maghrib', 'Isha'].includes(name)) return 'text-indigo-600 dark:text-indigo-400';
    return 'text-orange-500 dark:text-orange-400';
};

// Auto fetch on load
onMounted(() => {
    fetchPrayerTimes();
});
</script>
