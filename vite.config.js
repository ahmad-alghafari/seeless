import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/dash.js',
            ],
            refresh: true,
        }),
        vue(),
    ],

    build: {
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                'service-worker': 'public/service-worker.js',
            },
            output: {
                entryFileNames: '[name].js',
            },
        },
    },
});
