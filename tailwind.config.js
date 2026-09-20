/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./includes/**/*.php",
    "./admin/**/*.php",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          950: '#000000', // True Black Base
          900: '#0a0a0a', // Deep Charcoal
          800: '#171717', // Neutral Gray Dark
          700: '#262626',
          600: '#404040',
          500: '#737373',
          400: '#a3a3a3',
          300: '#d4d4d4',
          200: '#e5e5e5',
          100: '#f5f5f5',
          50:  '#fafafa',
        },
        accent: '#06b6d4', // Retain Cyan for glows
        give:   '#f59e0b', // Retain Amber for the give CTA
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['"Playfair Display"', 'Georgia', 'serif'], // Elegant Serif
        accent: ['Outfit', 'system-ui', 'sans-serif'], // Modern Geometric
      }
    }
  },
  plugins: []
};
