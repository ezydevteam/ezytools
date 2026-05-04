<template>
    <div class="bg-white dark:bg-surface-800 p-6 md:p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700">
        <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">
            <!-- Subjects Input -->
            <div class="space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-surface-900 dark:text-white">Subjects</h3>
                    <button @click="addSubject" class="px-3 py-1.5 bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400 font-medium rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-colors text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Subject
                    </button>
                </div>

                <div class="space-y-3">
                    <div v-for="(subject, index) in subjects" :key="index" class="flex items-center gap-3 bg-surface-50 dark:bg-surface-900 p-3 rounded-xl border border-surface-200 dark:border-surface-700">
                        <!-- Subject Name -->
                        <div class="flex-1">
                            <input type="text" v-model="subject.name" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500 transition-colors" :placeholder="`Subject ${index + 1}`">
                        </div>
                        
                        <!-- Grade Selection -->
                        <div class="w-28">
                            <select v-model="subject.grade" class="block w-full rounded-lg border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-800 text-surface-900 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500 transition-colors font-medium">
                                <option value="5.0">A+ (5.0)</option>
                                <option value="4.0">A (4.0)</option>
                                <option value="3.5">A- (3.5)</option>
                                <option value="3.0">B (3.0)</option>
                                <option value="2.0">C (2.0)</option>
                                <option value="1.0">D (1.0)</option>
                                <option value="0.0">F (0.0)</option>
                            </select>
                        </div>
                        
                        <!-- 4th Subject Toggle -->
                        <div class="w-10 flex justify-center">
                            <button @click="toggleFourthSubject(index)" :class="[subject.isFourth ? 'text-primary-600 dark:text-primary-400 bg-primary-100 dark:bg-primary-900/30 border-primary-300 dark:border-primary-700' : 'text-surface-400 dark:text-surface-500 bg-white dark:bg-surface-800 border-surface-300 dark:border-surface-600']" class="w-8 h-8 rounded-lg border flex items-center justify-center transition-colors" title="Toggle 4th Subject">
                                <span class="text-xs font-bold">4th</span>
                            </button>
                        </div>

                        <!-- Remove -->
                        <button @click="removeSubject(index)" class="text-surface-400 hover:text-red-500 transition-colors p-1" title="Remove Subject" :disabled="subjects.length <= 1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Panel -->
            <div class="bg-surface-50 dark:bg-surface-900 p-6 rounded-2xl border border-surface-200 dark:border-surface-700 flex flex-col justify-start">
                <h3 class="text-lg font-bold text-surface-900 dark:text-white mb-6 text-center">Result</h3>
                
                <div class="text-center mb-8">
                    <span class="text-sm font-medium text-surface-500 dark:text-surface-400 block mb-2 uppercase tracking-wider">Final GPA</span>
                    <span class="text-6xl font-black block" :class="[hasFailed ? 'text-red-600 dark:text-red-400' : 'text-primary-600 dark:text-primary-400']">
                        {{ hasFailed ? 'F' : calculateGPA }}
                    </span>
                    <span v-if="!hasFailed" class="text-sm font-bold mt-2 inline-block px-3 py-1 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300">
                        {{ getGradeLetter(calculateGPA) }}
                    </span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center pb-2 border-b border-surface-200 dark:border-surface-700">
                        <span class="text-surface-600 dark:text-surface-400">Total Subjects (excl. 4th)</span>
                        <span class="font-bold text-surface-900 dark:text-white">{{ mainSubjectsCount }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-surface-200 dark:border-surface-700">
                        <span class="text-surface-600 dark:text-surface-400">Main Subjects GP Total</span>
                        <span class="font-bold text-surface-900 dark:text-white">{{ mainSubjectsGpTotal.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-surface-200 dark:border-surface-700">
                        <span class="text-surface-600 dark:text-surface-400">4th Subject GP Added</span>
                        <span class="font-bold text-green-600 dark:text-green-400">+{{ fourthSubjectExtraGp.toFixed(2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const defaultSubjects = [
    { name: 'Bangla', grade: '5.0', isFourth: false },
    { name: 'English', grade: '4.0', isFourth: false },
    { name: 'Mathematics', grade: '5.0', isFourth: false },
    { name: 'Science / Gen. Science', grade: '5.0', isFourth: false },
    { name: 'Religion', grade: '5.0', isFourth: false },
    { name: 'Optional / 4th Subject', grade: '5.0', isFourth: true }
];

const subjects = ref(JSON.parse(JSON.stringify(defaultSubjects)));

const addSubject = () => {
    subjects.value.push({ name: '', grade: '5.0', isFourth: false });
};

const removeSubject = (index) => {
    if (subjects.value.length > 1) {
        subjects.value.splice(index, 1);
    }
};

const toggleFourthSubject = (index) => {
    // Only allow one 4th subject
    if (!subjects.value[index].isFourth) {
        subjects.value.forEach(s => s.isFourth = false);
    }
    subjects.value[index].isFourth = !subjects.value[index].isFourth;
};

const mainSubjects = computed(() => subjects.value.filter(s => !s.isFourth));
const fourthSubject = computed(() => subjects.value.find(s => s.isFourth));

const hasFailed = computed(() => {
    // Fail if any MAIN subject is F (0.0)
    return mainSubjects.value.some(s => Number(s.grade) === 0);
});

const mainSubjectsCount = computed(() => mainSubjects.value.length);

const mainSubjectsGpTotal = computed(() => {
    return mainSubjects.value.reduce((total, s) => total + Number(s.grade), 0);
});

const fourthSubjectExtraGp = computed(() => {
    if (!fourthSubject.value) return 0;
    let grade = Number(fourthSubject.value.grade);
    // 4th subject GP only adds above 2.0
    return grade > 2.0 ? grade - 2.0 : 0;
});

const calculateGPA = computed(() => {
    if (hasFailed.value || mainSubjectsCount.value === 0) return '0.00';
    
    let totalGp = mainSubjectsGpTotal.value + fourthSubjectExtraGp.value;
    let gpa = totalGp / mainSubjectsCount.value;
    
    // Max GPA is 5.00
    if (gpa > 5.0) gpa = 5.0;
    
    return gpa.toFixed(2);
});

const getGradeLetter = (gpa) => {
    let g = Number(gpa);
    if (g >= 5.0) return 'A+';
    if (g >= 4.0) return 'A';
    if (g >= 3.5) return 'A-';
    if (g >= 3.0) return 'B';
    if (g >= 2.0) return 'C';
    if (g >= 1.0) return 'D';
    return 'F';
};
</script>
