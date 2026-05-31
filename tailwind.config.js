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
          900: '#010115', // Deepest Navy (Backgrounds, Dark Text)
          800: '#03033A', // Dark Navy
          700: '#05055A', // Navy
          500: '#0A0AA0', // Dark Royal Blue (Hover states)
          400: '#0D0DC0', // Bright Royal Blue (Buttons, Accents)
          300: '#1010E0', // Vibrant Electric Blue
          light: '#F4F7F9' // Very light cool gray for section backgrounds
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['Outfit', 'system-ui', 'sans-serif'],
      }
    }
  },
  plugins: []
};
