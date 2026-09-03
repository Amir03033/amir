import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                cyber: {
                    bg: '#030712',
                    card: '#0b0f19',
                    accent: '#06b6d4',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['IBM Plex Mono', 'ui-monospace', 'monospace'],
            },
        },
    },
    plugins: [typography],
};
