import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // One bundle per area, plus one page-level script:
            //   public      — every public page (menu, lazy map, hero fade)
            //   project     — project detail page only (lightbox, calculator)
            //   admin       — admin panel only
            //   consultant  — consultant portal only
            //   auth        — login page only
            input: [
                'resources/scss/public.scss',
                'resources/js/public.js',
                'resources/js/project.js',
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
                silenceDeprecations: ['import', 'color-functions', 'global-builtin'],
            },
        },
    },
    build: {
        cssCodeSplit: true,
    },
});
