const colors = require('tailwindcss/colors')

module.exports = {
  purge: [
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
  ],
  darkMode: false, // or 'media' or 'class'

//   mode: 'jit',
  theme: {
    extend: {
      colors: {
        sky: colors.sky,
        cyan: colors.cyan,
      },
    },
  },
  variants: {},
  plugins: [],
}
