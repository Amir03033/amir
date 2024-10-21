import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
// import { defineConfig } from 'vite';

let config = {
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/home.css',
                'resources/js/app.js',
            ],
        }),
    ],
    build: {
        assetsDir: '',
    }
}

export default defineConfig(({command, mode, ssrBuild}) => {
    if (command === 'serve') {
        config.publicDir = 'public';
        config.build = {
            assetsDir: '',
            copyPublicDir: false,
            emptyOutDir: true,
        };
    }

    return config;
});
