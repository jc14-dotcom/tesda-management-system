import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // Split Alpine and Flowbite into named vendor chunks so browsers
                // can cache them independently from application code.
                // Vite 8+ requires manualChunks to be a function.
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('alpinejs')) {
                            return 'vendor-alpine';
                        }
                        if (id.includes('flowbite')) {
                            return 'vendor-flowbite';
                        }
                    }
                },
            },
        },
    },
});
