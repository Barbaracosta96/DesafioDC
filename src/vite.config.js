import { defineConfig } from 'vite';
import { resolve } from 'path';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ command, mode }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: command === 'serve',
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
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        // Minificação máxima para produção
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug'],
            },
        },
        // Code splitting agressivo para carregamento rápido
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/vue') || id.includes('node_modules/@vue')) {
                        return 'vue-vendor';
                    }
                    if (id.includes('node_modules/@inertiajs')) {
                        return 'inertia-vendor';
                    }
                    if (id.includes('node_modules/@vueuse')) {
                        return 'vueuse-vendor';
                    }
                    if (id.includes('node_modules/axios')) {
                        return 'axios-vendor';
                    }
                },
                // Nomes de chunk com hash para cache-busting
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
            },
        },
        // Aumenta o aviso de chunk para 1MB
        chunkSizeWarningLimit: 1000,
        // CSS com code splitting
        cssCodeSplit: true,
        // Source maps desabilitados em produção para segurança e velocidade
        sourcemap: false,
        // Reporta resultados de compressão
        reportCompressedSize: false,
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
    // Otimizações de deps para dev rápido
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', '@vueuse/core', 'axios'],
    },
}));
