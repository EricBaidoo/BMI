/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./includes/**/*.php",
    "./admin/**/*.php",
    "./assets/js/**/*.js",
    "./components/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        obsidian: {
          950: '#030303', // Deepest black
          900: '#0a0a0c', // Subtle charcoal/blue tint
          800: '#121214',
          700: '#1a1a1c',
          600: '#27272a',
          500: '#3f3f46',
          400: '#52525b',
          300: '#a1a1aa',
          200: '#d4d4d8',
          100: '#f4f4f5',
          50:  '#fafafa',
        },
        accent: {
          DEFAULT: '#f59e0b', // Luminous Amber/Gold
          glow: 'rgba(245, 158, 11, 0.4)',
        },
        brand: {
          950: '#000000',
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['"Playfair Display"', 'Georgia', 'serif'],
        accent: ['Outfit', 'system-ui', 'sans-serif'],
      },
      letterSpacing: {
        'widest-xl': '0.3em',
        'widest-2xl': '0.5em',
      },
      boxShadow: {
        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
        'glow': '0 0 20px rgba(245, 158, 11, 0.3)',
      },
      animation: {
        'marquee': 'marquee 25s linear infinite',
        'shimmer': 'shimmer 2s linear infinite',
        'float': 'float 6s ease-in-out infinite',
      },
      keyframes: {
        marquee: {
          '0%': { transform: 'translateX(0%)' },
          '100%': { transform: 'translateX(-100%)' },
        },
        shimmer: {
          'from': { backgroundPosition: '200% 0' },
          'to': { backgroundPosition: '-200% 0' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-10px)' },
        }
      }
    }
  },
  plugins: []
};
