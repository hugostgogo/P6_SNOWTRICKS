module.exports = {
  content: [
    "templates/*/*.{html.twig,js}",
    "assets/js/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'text': '#000000',
        'textInverted': '#ffffff',
        'primary': '#2882ff',
        'error': '#ff0000',
        'warning': '#ffa500',
        'secondary': '#999999'
      },

    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
