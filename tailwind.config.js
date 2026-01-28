/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue"
  ],
  theme: {
    extend: {
      colors: {
        'brand-yellow': '#ffe000',
        'brand-yellow-light': '#fff9cc',
        brand: {
          50:  '#fff9cc',
          100: '#fff3a3',
          200: '#ffeb66',
          300: '#ffe433',
          400: '#ffdd1a',
          500: '#ffe000',
          600: '#e6c900',
          700: '#bfa700',
        }
      }
    },
  },
  plugins: [],
}

