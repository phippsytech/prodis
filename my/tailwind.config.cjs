const config = {
  content: [
    "./src/**/*.{html,js,svelte,ts}",
    './node_modules/tw-elements/dist/js/**/*.js',
    "./shared/**/*.{html,js,svelte,ts}",
],

  theme: {
    extend: {},
  },

  plugins: [
    require('tw-elements/dist/plugin')
  ],
};

module.exports = config;
