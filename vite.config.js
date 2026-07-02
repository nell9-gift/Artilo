import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
    'resources/css/app.css',
    'resources/css/auth-register-login.css',
    'resources/css/auth-register-artisan.css',
    'resources/css/auth-register-customer.css',
    'resources/css/auth-register-unified.css',
    'resources/js/app.js',
],
            refresh: true,
        }),
    ],
});