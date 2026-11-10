import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // BUSina palette, taken from the group mockup. Change it here to restyle the whole site.
                //   brand-500 = pink buttons and logo badge, brand-800 = maroon accents ("BUS", links, icons)
                brand: {
                    50: '#fdf2f5',
                    100: '#fbe4ec',
                    200: '#f8cadb',
                    300: '#f2a3bd',
                    400: '#ec7e9f',
                    500: '#e8648b',
                    600: '#d6466f',
                    700: '#b02c56',
                    800: '#8a0a45',
                    900: '#6e0838',
                    950: '#42031f',
                },
                blush: '#fcecec',   // pale pink page background (login, register, "How it works")
                ink: {
                    DEFAULT: '#2f333f', // dark hero background
                    900: '#1b2020',     // footer
                },
            },
        },
    },

    plugins: [forms, typography],
};
