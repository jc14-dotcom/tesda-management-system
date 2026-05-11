import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './node_modules/flowbite/**/*.js',
    ],
    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#2B2D7E',
                    hover: '#3540A3',
                    active: '#1F2161',
                    soft: '#EEF1FF',
                },
                accent: {
                    DEFAULT: '#F4B400',
                    hover: '#D99A00',
                    active: '#B67C00',
                    soft: '#FFF7D6',
                },
                grayTheme: {
                    dark: '#1F2937',
                    medium: '#6B7280',
                    light: '#F3F4F6',
                    border: '#D1D5DB',
                    hover: '#E5E7EB',
                },
                success: {
                    DEFAULT: '#22C55E',
                    hover: '#16A34A',
                    soft: '#DCFCE7',
                },
                warning: {
                    DEFAULT: '#F59E0B',
                    hover: '#D97706',
                    soft: '#FEF3C7',
                },
                danger: {
                    DEFAULT: '#EF4444',
                    hover: '#DC2626',
                    soft: '#FEE2E2',
                },
                info: {
                    DEFAULT: '#3B82F6',
                    hover: '#2563EB',
                    soft: '#DBEAFE',
                },
            },
            boxShadow: {
                card: '0 2px 8px rgba(0,0,0,0.05)',
                sidebar: '2px 0 10px rgba(0,0,0,0.08)',
                dropdown: '0 4px 12px rgba(0,0,0,0.1)',
                modal: '0 8px 24px rgba(0,0,0,0.12)',
            },
            borderRadius: {
                card: '14px',
                button: '10px',
            },
            transitionDuration: {
                250: '250ms',
            },
            fontFamily: {
                sans: ['Sora', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                fadeInUp: {
                    from: { opacity: '0', transform: 'translateY(30px)' },
                    to: { opacity: '1', transform: 'translateY(0)' },
                },
                pulseGlow: {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(59, 130, 246, 0.5)' },
                    '50%': { boxShadow: '0 0 30px rgba(59, 130, 246, 0.8)' },
                },
                rotateSlow: {
                    from: { transform: 'rotate(0deg)' },
                    to: { transform: 'rotate(360deg)' },
                },
                gradientShift: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                bounceSoft: {
                    '0%, 20%, 50%, 80%, 100%': { transform: 'translateY(0)' },
                    '40%': { transform: 'translateY(-10px)' },
                    '60%': { transform: 'translateY(-5px)' },
                },
            },
            animation: {
                float: 'float 3s ease-in-out infinite',
                'pulse-slow': 'pulseGlow 3s ease-in-out infinite',
                'gradient-shift': 'gradientShift 3s ease infinite',
                'fade-in-up': 'fadeInUp 0.8s ease-out both',
                'rotate-slow': 'rotateSlow 20s linear infinite',
                'bounce-soft': 'bounceSoft 2s infinite',
            },
        },
    },

    plugins: [forms, flowbite],
};
