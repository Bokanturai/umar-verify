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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#F7F3ED',
                    foreground: '#885c0a',
                },
                success: {
                    DEFAULT: '#03C95A',
                },
                warning: {
                    DEFAULT: '#CBA135',
                },
                danger: {
                    DEFAULT: '#AE2012',
                },
                info: {
                    DEFAULT: '#1D3557',
                },
            },
        },
    },

    plugins: [forms],
};
