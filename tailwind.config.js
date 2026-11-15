import typography from '@tailwindcss/typography';

// amirs.life design tokens (see DESIGN.md). Colours, radii and shadows are *replaced*, not extended,
// so Tailwind's defaults (blue-500, rounded-2xl, shadow-lg…) don't exist here and can't creep back in.
const token = (name) => `rgb(var(--${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            inherit: 'inherit',
            white: '#ffffff',
            bg: token('bg'),
            surface: token('surface'),
            fill: token('fill'),
            line: token('line'),
            'line-strong': token('line-strong'),
            fg: token('text'),
            'fg-2': token('text-2'),
            muted: token('muted'),
            accent: token('accent'),
            'accent-fg': token('accent-text'),
            ok: token('ok'),
            err: token('err'),
            warn: token('warn'),
            // Fixed, theme-independent surfaces (terminal, primary button in light).
            night: token('night'),
            day: token('day'),
        },
        borderRadius: {
            none: '0',
            0: '0',
            1: 'var(--r-1)',
            pill: '9999px',
            full: '9999px',
        },
        boxShadow: {
            none: 'none',
            float: 'var(--shadow-float)',
        },
        fontFamily: {
            sans: ['"Instrument Sans"', '"Instrument Sans Fallback"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            mono: ['"Martian Mono"', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
        },
        extend: {
            fontSize: {
                hero: ['var(--t-hero)', { lineHeight: '1', letterSpacing: '-0.03em' }],
                statement: ['var(--t-statement)', { lineHeight: '1.04', letterSpacing: '-0.025em' }],
                title: ['var(--t-title)', { lineHeight: '1.08', letterSpacing: '-0.02em' }],
                h3: ['var(--t-h3)', { lineHeight: '1.2', letterSpacing: '-0.01em' }],
                lead: ['var(--t-lead)', { lineHeight: '1.45', letterSpacing: '-0.005em' }],
                body: ['var(--t-body)', { lineHeight: '1.6' }],
                small: ['var(--t-small)', { lineHeight: '1.5' }],
                label: ['var(--t-label)', { lineHeight: '1.3', letterSpacing: '0.1em' }],
            },
            maxWidth: {
                content: 'var(--w-content)',
                read: 'var(--w-read)',
            },
            spacing: {
                gutter: 'var(--gutter)',
                section: 'var(--space-section)',
                stack: 'var(--space-stack)',
            },
            transitionTimingFunction: {
                DEFAULT: 'cubic-bezier(0.22, 1, 0.36, 1)',
                out: 'cubic-bezier(0.22, 1, 0.36, 1)',
                inout: 'cubic-bezier(0.65, 0, 0.35, 1)',
            },
            transitionDuration: {
                DEFAULT: '200ms',
                ui: '200ms',
                slow: '300ms',
                reveal: '800ms',
            },
            zIndex: {
                nav: '50',
                sheet: '70',
                toast: '80',
                skip: '90',
            },
        },
    },
    plugins: [typography],
};
