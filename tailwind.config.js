import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // ── Tipografía ────────────────────────────────────────────────
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'ui-monospace', 'monospace'],
            },

            // ── Paleta de marca: Teal profesional ─────────────────────────
            colors: {
                brand: {
                    50:  '#F0FDFA',
                    100: '#CCFBF1',
                    200: '#99F6E4',
                    300: '#5EEAD4',
                    400: '#2DD4BF',
                    500: '#14B8A6',
                    600: '#0D9488',
                    700: '#0F766E',  // ← primario principal
                    800: '#115E59',
                    900: '#134E4A',
                    950: '#0D3330',  // ← sidebar
                },
            },

            // ── Sombras ───────────────────────────────────────────────────
            boxShadow: {
                'card':    '0 1px 3px rgba(15,23,42,0.06), 0 1px 2px rgba(15,23,42,0.04)',
                'card-md': '0 4px 12px rgba(15,23,42,0.08), 0 2px 4px rgba(15,23,42,0.04)',
                'card-lg': '0 8px 24px rgba(15,23,42,0.10), 0 4px 8px rgba(15,23,42,0.06)',
                'modal':   '0 20px 60px rgba(15,23,42,0.18)',
                'inner-sm': 'inset 0 1px 2px rgba(15,23,42,0.06)',
            },

            // ── Border radius ─────────────────────────────────────────────
            borderRadius: {
                'input': '8px',
                'card':  '14px',
                'modal': '20px',
            },

            // ── Animaciones ───────────────────────────────────────────────
            keyframes: {
                'fade-in': {
                    '0%':   { opacity: '0', transform: 'translateY(6px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'slide-up': {
                    '0%':   { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-in':  'fade-in 0.2s ease-out',
                'slide-up': 'slide-up 0.3s ease-out',
            },
        },
    },

    plugins: [forms],
};
