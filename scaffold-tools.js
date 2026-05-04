import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const seederPath = path.join(__dirname, 'database', 'seeders', 'ToolSeeder.php');
const implementationsDir = path.join(__dirname, 'resources', 'js', 'Pages', 'Tools', 'Implementations');

const seederContent = fs.readFileSync(seederPath, 'utf8');
const componentRegex = /'component_name'\s*=>\s*'([^']+)'/g;

let match;
const components = [];
while ((match = componentRegex.exec(seederContent)) !== null) {
    components.push(match[1]);
}

const placeholderTemplate = `<template>
    <div class="bg-white dark:bg-surface-800 p-8 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 text-center">
        <div class="w-20 h-20 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-6 dark:bg-primary-900/50 dark:text-primary-400">
            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-surface-900 dark:text-white mb-2">Under Construction</h2>
        <p class="text-surface-500 dark:text-surface-400 max-w-md mx-auto mb-8">
            This tool is currently being developed and will be available very soon. Check back later!
        </p>
    </div>
</template>

<script setup>
// Placeholder for future implementation
</script>
`;

let createdCount = 0;
components.forEach(comp => {
    const filePath = path.join(implementationsDir, comp + '.vue');
    if (!fs.existsSync(filePath)) {
        fs.writeFileSync(filePath, placeholderTemplate);
        createdCount++;
        console.log("Created missing component: " + comp + ".vue");
    }
});

console.log("Finished scaffolding. Created " + createdCount + " new components.");
