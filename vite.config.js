import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/loopple.css',
                'resources/css/theme.css',
                'resources/js/loopple.js'

            ],
            refresh: true,
        }),
    ],
});
