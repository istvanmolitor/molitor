import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@admin': path.resolve(__dirname, 'resources/js/packages/vue-admin'),
            '@article-scraper': path.resolve(__dirname, 'resources/js/packages/vue-article-scraper'),
            '@cms': path.resolve(__dirname, 'resources/js/packages/vue-cms'),
            '@cms-relations': path.resolve(__dirname, 'resources/js/packages/vue-cms-post-relations'),
            '@currency': path.resolve(__dirname, 'resources/js/packages/vue-currency'),
            '@media': path.resolve(__dirname, 'resources/js/packages/vue-media'),
            '@menu': path.resolve(__dirname, 'resources/js/packages/ts-menu'),
            '@product': path.resolve(__dirname, 'resources/js/packages/vue-product'),
            '@rss-watcher': path.resolve(__dirname, 'resources/js/packages/vue-rss-watcher'),
            '@theme': path.resolve(__dirname, 'resources/js/packages/vue-theme'),
            '@user': path.resolve(__dirname, 'resources/js/packages/vue-user'),
            '@language': path.resolve(__dirname, 'resources/js/packages/vue-language'),
            '@gallery': path.resolve(__dirname, 'resources/js/packages/vue-gallery'),
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('lucide-vue-next')) {
                            return 'vendor-lucide';
                        }
                        if (id.includes('radix-vue') || id.includes('reka-ui')) {
                            return 'vendor-ui';
                        }
                        return 'vendor';
                    }
                },
            },
        },
    },
});
