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
            '@media': path.resolve(__dirname, 'resources/js/packages/vue-media'),
            '@menu': path.resolve(__dirname, 'resources/js/packages/ts-menu'),
            '@rss-watcher': path.resolve(__dirname, 'resources/js/packages/vue-rss-watcher'),
            '@theme': path.resolve(__dirname, 'resources/js/packages/vue-theme'),
            '@user': path.resolve(__dirname, 'resources/js/packages/vue-user'),
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
