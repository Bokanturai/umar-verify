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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#E6F7F6',
                    foreground: '#0F766E',
                },
                success: {
                    DEFAULT: '#10B981',
                },
                warning: {
                    DEFAULT: '#F59E0B',
                },
                danger: {
                    DEFAULT: '#EF4444',
                },
                info: {
                    DEFAULT: '#38BDF8',
                },
                secondary: {
                    DEFAULT: '#0F172A',
                }
            },
        },
    },

    plugins: [forms],
};
