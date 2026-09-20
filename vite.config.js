import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // One bundle per area. The public site never downloads admin CSS
            // or JS, neither panel downloads the other's, and the login page
            // downloads neither.
            input: [
                'resources/scss/public.scss',
                'resources/js/public.js',
                'resources/scss/admin.scss',
                'resources/js/admin.js',
                'resources/scss/consultant.scss',
                'resources/js/consultant.js',
                'resources/scss/auth.scss',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                silenceDeprecations: ['import', 'mixed-decls', 'color-functions', 'global-builtin'],
            },
        },
    },
    build: {
        cssCodeSplit: true,
    },
});
