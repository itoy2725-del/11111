/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        crm: {
          50: '#f4f6f8',
          100: '#e8edf1',
          200: '#cfdae3',
          300: '#a6bfce',
          400: '#759fb5',
          500: '#54839c',
          600: '#42687f',
          700: '#365467',
          800: '#2f4756',
          900: '#2a3b48',
          950: '#1a2630',
        },
      },
      fontFamily: {
        sans: [
          'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'
        ],
      },
    },
  },
  plugins: [],
}
