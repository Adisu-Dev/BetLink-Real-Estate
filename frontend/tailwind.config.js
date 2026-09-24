/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50:  '#f8f9fa',
          100: '#f1f3f5',
          200: '#e9ecef',
          300: '#dee2e6',
          400: '#ced4da',
          500: '#adb5bd',
          600: '#6c757d',
          700: '#495057',
          800: '#343a40',
          900: '#212529',
          950: '#0a0c0d',
        },
        // Dashboard color system
        navy: {
          50: '#f0f4f8',
          100: '#d9e2ec',
          200: '#bcccdc',
          300: '#9fb3c8',
          400: '#829ab1',
          500: '#627d98',
          600: '#486581',
          700: '#334e68',
          800: '#243b53',
          900: '#172A4A',
          950: '#0F1B33',
        },
        betlink: {
          green: '#006B57',
          'green-light': '#008A6E',
          'green-dark': '#005243',
        },
        active: {
          blue: '#3B82F6',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      boxShadow: {
        'card': '0 2px 16px 0 rgba(0,0,0,0.08)',
        'card-hover': '0 8px 32px 0 rgba(0,0,0,0.14)',
        'search': '0 4px 32px 0 rgba(4,120,87,0.12)',
      },
      backgroundImage: {
        'hero-gradient': 'linear-gradient(135deg, rgba(0,0,0,0.75) 0%, rgba(33,37,41,0.85) 100%)',
        'cta-gradient':  'linear-gradient(135deg, #212529 0%, #343a40 100%)',
      }
    }
  },
  plugins: []
}
