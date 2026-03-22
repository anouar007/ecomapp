import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                danger: '#e60000',
                warning: '#ffc107',
                dark: '#000000', // Pure Black
                primary: '#D4AF37', // Metallic Gold
                accent: '#C5A028', // Darker Gold
            },
        },
    },
    corePlugins: {
        preflight: false,
        collapse: false, // Fix Bootstrap conflict
    },
    plugins: [],
};
