import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                surface: {
                    DEFAULT: '#ffffff',
                    muted: '#f1f5f9',
                    dark: '#0f172a',
                    'dark-muted': '#1e293b',
                },
                ink: {
                    DEFAULT: '#0f172a',
                    muted: '#334155',
                    inverse: '#f8fafc',
                },
                brand: {
                    DEFAULT: '#1d4ed8',
                    hover: '#1e40af',
                    soft: '#dbeafe',
                    dark: '#3b82f6',
                },
            },
            minHeight: {
                touch: '3rem',
            },
            fontSize: {
                'ui': ['1.0625rem', { lineHeight: '1.5' }],
            },
        },
    },

    plugins: [forms],
};
