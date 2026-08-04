import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                sans: ['Tajawal', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: 'hsl(217, 100%, 50%)',
                    foreground: '#ffffff',
                    light: 'hsl(217, 100%, 65%)',
                    dark: 'hsl(217, 100%, 40%)'
                },
                destructive: 'hsl(0, 84%, 60%)',
                success: 'hsl(142, 71%, 45%)',
                warning: 'hsl(38, 92%, 50%)',
            }
        },
    },
    plugins: [],
};
