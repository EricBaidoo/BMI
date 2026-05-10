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
      fontFamily: {
        display: ["Montserrat", "system-ui", "sans-serif"],
        sans: ["Source Sans 3", "system-ui", "sans-serif"]
      }
    }
  },
  plugins: []
};
