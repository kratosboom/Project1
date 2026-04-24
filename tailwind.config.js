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
                sans: ['Outfit', 'Figtree', 'DM Sans', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', 'Georgia', 'serif'],
            },
            colors: {
                primary: { DEFAULT: '#fb2323', dark: '#b30000' },
                ink: { 950: '#0b0f1a', 900: '#111827', 800: '#1e293b' },
            },
        },
    },

    plugins: [forms],
};
