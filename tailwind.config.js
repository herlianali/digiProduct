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
      fontFamily: {
        'arial': ['Arial', 'sans-serif'],
        'arial-black': ['Arial Black', 'sans-serif'],
        'bell': ['"Bell MT"', 'sans-serif'],
        'barlow': ['Barlow', 'sans-serif'],
        'barlow-bold': ['Barlow Bold', 'sans-serif'],
        'barlow-condensed': ['Barlow Condensed', 'sans-serif'],
        'barlow-condensed-extrabold': ['Barlow Condensed ExtraBold', 'sans-serif'],
        'sans': ['Arial', 'system-ui', 'sans-serif'],
        'heading': ['Arial', 'sans-serif'],
        'body': ['Arial', 'sans-serif'],
      },
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

