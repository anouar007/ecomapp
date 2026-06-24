import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Admin dashboard bundle
                'resources/css/app.css',
                'resources/js/app.js',
                // Customer-facing storefront bundle
                'resources/css/frontend.css',
                'resources/js/frontend.js',
            ],
            refresh: true,
        }),
    ],
});
