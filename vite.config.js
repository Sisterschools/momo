import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import eslint from 'vite-plugin-eslint';
import vue from '@vitejs/plugin-vue'; 

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.scss',
            'resources/js/app.js',
        ]),
        eslint(),
        vue({ 
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: { 
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@assets': '../resources/'
        },
    },
    css: {
        preprocessorOptions: {
          scss: {
            api: 'modern-compiler'
          }
        }
    },
    server:{ host: 'blueberry.local', port:3000},
});
