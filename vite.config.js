import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/prueba6.css',
                'resources/js/prueba6.js',
                'resources/css/admin.css',
                'resources/css/maquina-cafe.css'
            ],
            refresh: true,
        }),
    ],
});
