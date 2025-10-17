/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.php",
    "./includes/**/*.php",
    "./public/index.php",
    "./public/project_modal.php",
    "./public/assets/js/**/*.js"
  ],
  theme: {
    extend: {
      fontFamily: {
        orbitron: ['Orbitron', 'sans-serif'],
        poppins: ['Poppins', 'sans-serif']
      },
      colors: {
        primary: {
          DEFAULT: '#1e3a8a',  // Navy Blue 800
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1e40af',
          800: '#1e3a8a',  // Main Navy
          900: '#1e3a8a',
        },
        secondary: {
          DEFAULT: '#06b6d4',  // Cyan accent
          50: '#ecfeff',
          100: '#cffafe',
          200: '#a5f3fc',
          300: '#67e8f9',
          400: '#22d3ee',
          500: '#06b6d4',
          600: '#0891b2',
          700: '#0e7490',
          800: '#155e75',
          900: '#164e63',
        }
      }
    },
  },
  plugins: [],
}