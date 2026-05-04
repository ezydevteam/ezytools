<template>
    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Courses List -->
        <div class="lg:col-span-8 bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="flex justify-between items-center mb-6 border-b border-surface-200 dark:border-surface-700 pb-4">
                <h3 class="font-semibold text-lg text-surface-900 dark:text-white">Semester Courses</h3>
                <button @click="reset" class="text-sm text-red-500 hover:text-red-700 font-medium">Reset All</button>
            </div>

            <!-- Table Header -->
            <div class="grid grid-cols-12 gap-4 mb-2 px-2 text-xs font-semibold text-surface-500 uppercase tracking-wider">
                <div class="col-span-5">Course Name (Optional)</div>
                <div class="col-span-3 text-center">Credits</div>
                <div class="col-span-3 text-center">Grade</div>
                <div class="col-span-1"></div>
            </div>

            <!-- Rows -->
            <div class="space-y-3">
                <div v-for="(course, index) in courses" :key="index" class="grid grid-cols-12 gap-4 items-center bg-surface-50 dark:bg-surface-900/50 p-2 rounded-lg border border-surface-100 dark:border-surface-700 transition-all hover:border-primary-300">
                    <div class="col-span-5">
                        <input type="text" v-model="course.name" :placeholder="`Course ${index + 1}`" class="block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-surface-800 dark:border-surface-600 dark:text-white">
                    </div>
                    <div class="col-span-3">
                        <input type="number" v-model.number="course.credits" min="1" max="6" step="0.5" class="block w-full text-center rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-surface-800 dark:border-surface-600 dark:text-white">
                    </div>
                    <div class="col-span-3">
                        <select v-model="course.grade" class="block w-full text-center rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-surface-800 dark:border-surface-600 dark:text-white font-semibold" :class="getGradeColor(course.grade)">
                            <option value="">Select</option>
                            <option v-for="(point, letter) in gradingScale" :key="letter" :value="letter">{{ letter }} ({{ point.toFixed(2) }})</option>
                        </select>
                    </div>
                    <div class="col-span-1 flex justify-center">
                        <button @click="removeCourse(index)" class="text-surface-400 hover:text-red-500 transition-colors p-1" :disabled="courses.length <= 1">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                <button @click="addCourse" class="w-full py-2 border-2 border-dashed border-surface-300 dark:border-surface-600 text-surface-600 dark:text-surface-400 hover:bg-surface-50 dark:hover:bg-surface-800 rounded-lg text-sm font-medium transition-colors flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Another Course
                </button>
            </div>
        </div>

        <!-- Result Sidebar -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <div class="bg-gradient-to-br from-indigo-600 to-primary-800 p-8 rounded-xl shadow-lg text-white text-center">
                <h3 class="text-indigo-100 font-medium mb-1 uppercase tracking-wide text-sm">Semester GPA</h3>
                <div class="text-5xl font-bold mb-2">
                    {{ isNaN(calculatedGPA) ? '0.00' : calculatedGPA.toFixed(2) }}
                </div>
                <div class="text-indigo-200 text-sm font-medium">Out of 4.00</div>
            </div>

            <div class="bg-white dark:bg-surface-800 p-6 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm space-y-4">
                <h4 class="font-semibold text-surface-900 dark:text-white border-b border-surface-200 dark:border-surface-700 pb-2">Summary</h4>
                
                <div class="flex justify-between items-center">
                    <span class="text-surface-600 dark:text-surface-400">Total Courses</span>
                    <span class="font-bold text-surface-900 dark:text-white">{{ completedCourses }} / {{ courses.length }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-surface-600 dark:text-surface-400">Total Credits</span>
                    <span class="font-bold text-surface-900 dark:text-white">{{ totalCredits }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-surface-600 dark:text-surface-400">Total Grade Points</span>
                    <span class="font-bold text-surface-900 dark:text-white">{{ totalPoints.toFixed(2) }}</span>
                </div>
            </div>
            
            <div class="bg-surface-50 dark:bg-surface-800/50 p-4 rounded-xl border border-surface-200 dark:border-surface-700 text-xs text-surface-500 dark:text-surface-400">
                <p class="font-semibold mb-2">Standard UGC Grading Scale (BD)</p>
                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                    <div>A+ (80-100%) = 4.00</div>
                    <div>A  (75-79%) = 3.75</div>
                    <div>A- (70-74%) = 3.50</div>
                    <div>B+ (65-69%) = 3.25</div>
                    <div>B  (60-64%) = 3.00</div>
                    <div>B- (55-59%) = 2.75</div>
                    <div>C+ (50-54%) = 2.50</div>
                    <div>C  (45-49%) = 2.25</div>
                    <div>D  (40-44%) = 2.00</div>
                    <div>F  (0-39%) = 0.00</div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const gradingScale = {
    'A+': 4.00,
    'A': 3.75,
    'A-': 3.50,
    'B+': 3.25,
    'B': 3.00,
    'B-': 2.75,
    'C+': 2.50,
    'C': 2.25,
    'D': 2.00,
    'F': 0.00
};

const courses = ref([
    { name: '', credits: 3, grade: '' },
    { name: '', credits: 3, grade: '' },
    { name: '', credits: 3, grade: '' },
    { name: '', credits: 3, grade: '' },
]);

const addCourse = () => {
    courses.value.push({ name: '', credits: 3, grade: '' });
};

const removeCourse = (index) => {
    if (courses.value.length > 1) {
        courses.value.splice(index, 1);
    }
};

const reset = () => {
    courses.value = [
        { name: '', credits: 3, grade: '' },
        { name: '', credits: 3, grade: '' },
        { name: '', credits: 3, grade: '' },
        { name: '', credits: 3, grade: '' },
    ];
};

const getGradeColor = (grade) => {
    if (!grade) return 'text-surface-900 dark:text-white';
    const point = gradingScale[grade];
    if (point >= 3.5) return 'text-green-600 dark:text-green-400';
    if (point >= 3.0) return 'text-blue-600 dark:text-blue-400';
    if (point >= 2.0) return 'text-orange-500 dark:text-orange-400';
    return 'text-red-600 dark:text-red-400';
};

const completedCourses = computed(() => courses.value.filter(c => c.grade !== '').length);

const totalCredits = computed(() => {
    return courses.value.reduce((acc, course) => {
        if (course.grade !== '' && course.credits) {
            return acc + Number(course.credits);
        }
        return acc;
    }, 0);
});

const totalPoints = computed(() => {
    return courses.value.reduce((acc, course) => {
        if (course.grade !== '' && course.credits) {
            return acc + (Number(course.credits) * gradingScale[course.grade]);
        }
        return acc;
    }, 0);
});

const calculatedGPA = computed(() => {
    if (totalCredits.value === 0) return 0;
    return totalPoints.value / totalCredits.value;
});
</script>
