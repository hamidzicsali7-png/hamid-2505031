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
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f4f4ff',
                    100: '#e8e8ff',
                    200: '#d3d4ff',
                    300: '#b2b4ff',
                    400: '#8a8cf4',
                    500: '#6265dc',
                    600: '#474bc0',
                    700: '#2E2E8F',
                    800: '#25256f',
                    900: '#1b1b50',
                    DEFAULT: '#2E2E8F',
                },
                soft: '#F4F5FB',
                success: '#16845b',
                danger: '#d64550',
                warning: '#bf7914',
            },
        },
    },

    plugins: [forms],
};
