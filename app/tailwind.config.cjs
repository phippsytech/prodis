const config = {
  content: [
    "./src/**/*.{html,js,svelte,ts}",
    './node_modules/tw-elements/dist/js/**/*.js',
    // './node_modules/flowbite-svelte/**/*.{html,js,svelte,ts}',
    // "./shared/**/*.{html,js,svelte,ts}",
    "/app/shared/**/*.{html,js,svelte,ts}",
  ],

  theme: {
    extend: {

      backgroundImage: {
        'radial-gradient': 'radial-gradient(ellipse 50% 25% at 50% 100%, rgba(224,231,255,1) 0%, rgba(224,231,255,0) 100%)'
      },
      colors: {
        'slate-transparent': {
          DEFAULT: '#f8fafc',
          0: 'rgba(248, 250, 252, 0)'  // Define transparent version
        }
      },
      // colors: {
      //     // flowbite-svelte
      //     primary: {
      //       50: '#FFF5F2',
      //       100: '#FFF1EE',
      //       200: '#FFE4DE',
      //       300: '#FFD5CC',
      //       400: '#FFBCAD',
      //       500: '#FE795D',
      //       600: '#EF562F',
      //       700: '#EB4F27',
      //       800: '#CC4522',
      //       900: '#A5371B'
      //     }
      //   }
    },
    fontFamily: {
      'fredoka-one-regular': ['fredoka_oneregular', 'sans-serif'],
    },
  },

  plugins: [
    require('tw-elements/dist/plugin'),
    // require('flowbite/plugin')
  ],
};

module.exports = config;
