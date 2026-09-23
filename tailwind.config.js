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
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
                serif: ['"Playfair Display"', 'Cinzel', 'Georgia', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                danger: '#dc2626',
                warning: '#d97706',
                dark: '#071914',
                primary: {
                    DEFAULT: '#0c261e',
                    light: '#163d2e',
                    dark: '#071914',
                },
                accent: {
                    DEFAULT: '#c28d32',
                    light: '#e2ad50',
                    dark: '#8c6014',
                },
                cream: '#faf8f3',
            },
        },
    },
    corePlugins: {
        preflight: false,
        collapse: false, // Fix Bootstrap conflict
    },
    plugins: [],
};
