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
            colors: {
                zass: {
                    bark: '#714329',
                    caramel: '#B08463',
                    sage: '#89937B',
                    linen: '#D0B9A7',
                    stone: '#B5A192',
                    cream: '#F7F1EC',
                    ink: '#2F241D',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                soft: '0 18px 50px -24px rgba(47, 36, 29, 0.45)',
                lift: '0 24px 70px -30px rgba(113, 67, 41, 0.55)',
            },
            keyframes: {
                fadeUp: {
                    '0%': { opacity: '0', transform: 'translateY(18px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                floatSlow: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                shimmer: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
            },
            animation: {
                'fade-up': 'fadeUp .7s ease-out both',
                'float-slow': 'floatSlow 5s ease-in-out infinite',
                shimmer: 'shimmer 2.8s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
