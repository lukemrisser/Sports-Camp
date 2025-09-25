import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
        system: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
      },
      colors: {
        'primary-blue': '#0a3f94',
        'primary-blue-hover': '#083570',
        'secondary-blue': '#3b82f6',
        'accent-yellow': '#fbbf24',
        'accent-yellow-hover': '#f59e0b',
        'success-green': '#10b981',
        'success-green-hover': '#059669',
        'danger-red': '#ef4444',
        'danger-red-hover': '#dc2626',
      },
    },
  },
  plugins: [forms],
};
