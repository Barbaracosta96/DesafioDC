import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    build: {
        // Faster chunk splitting — vendor libs separated from app code
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/vue') || id.includes('node_modules/@vue')) {
                        return 'vue-vendor';
                    }
                    if (id.includes('node_modules/@inertiajs')) {
                        return 'inertia-vendor';
                    }
                    if (id.includes('node_modules/@heroicons')) {
                        return 'icons-vendor';
                    }
                    if (id.includes('node_modules/apexcharts') || id.includes('node_modules/vue3-apexcharts')) {
                        return 'charts-vendor';
                    }
                    if (id.includes('node_modules/')) {
                        return 'vendor';
                    }
                },
            },
        },
        // Increase warning threshold to avoid noise
        chunkSizeWarningLimit: 800,
        // Minify aggressively
        minify: 'esbuild',
        target: 'es2020',
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
