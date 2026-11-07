import typography from '@tailwindcss/typography';

// Shared with ServerScope: same palette, radius and type so both read as one family.
const token = (name) => `rgb(var(--${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    // Status colors are picked at runtime in status-panel.blade.php.
    safelist: ['bg-scope-green', 'bg-scope-red', 'bg-scope-yellow', 'text-scope-green', 'text-scope-red', 'text-scope-yellow'],
    theme: {
        extend: {
            colors: {
                ink: {
                    bg: token('bg'),
                    panel: token('panel'),
                    panel2: token('panel-2'),
                    line: token('border'),
                    strong: token('border-strong'),
                    text: token('text'),
                    muted: token('muted'),
                    soft: token('soft'),
                },
                scope: {
                    accent: token('accent'),
                    green: token('green'),
                    yellow: token('yellow'),
                    red: token('red'),
                },
            },
            fontFamily: {
                sans: ['"Inter Variable"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['"JetBrains Mono Variable"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
            },
            borderRadius: {
                panel: '18px',
            },
            keyframes: {
                pulseDot: {
                    '0%': { transform: 'scale(1)', opacity: '.6' },
                    '100%': { transform: 'scale(2.6)', opacity: '0' },
                },
                marquee: {
                    from: { transform: 'translateX(0)' },
                    to: { transform: 'translateX(-50%)' },
                },
                blink: {
                    '50%': { opacity: '0' },
                },
                nudge: {
                    '0%, 100%': { transform: 'translateX(0)' },
                    '50%': { transform: 'translateX(4px)' },
                },
                scrollcue: {
                    '0%': { transform: 'translateY(-100%)' },
                    '100%': { transform: 'translateY(200%)' },
                },
            },
            animation: {
                'pulse-dot': 'pulseDot 2s cubic-bezier(0, 0, .2, 1) infinite',
                marquee: 'marquee 40s linear infinite',
                blink: 'blink 1s step-end infinite',
                scrollcue: 'scrollcue 1.8s cubic-bezier(.6, 0, .4, 1) infinite',
            },
        },
    },
    plugins: [typography],
};
