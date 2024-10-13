import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    mode: 'jit',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
        './resources/views/livewire/**/*.blade.php',
    ],
    safelist: [
        'bg-rose-500',
        'text-amber-500',
        'bg-amber-500',
        'border-yellow-500',
        'border-amber-400',
        'hover:bg-green-500'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                carbon: {
                    50: '#F6F6F6',
                    100: '#E7E7E7',
                    200: '#D1D1D1',
                    300: '#B0B0B0',
                    400: '#888888',
                    500: '#6D6D6D',
                    600: '#5D5D5D',
                    700: '#4F4F4F',
                    800: '#454545',
                    900: '#3D3D3D',
                    950: '#252525'
                },
            }
        },
    },

    plugins: [forms, typography],
};
