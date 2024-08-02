const config = {
  content: [
    "./**/*.{twig,partial,html,js,ts}",
    "./../content/**/*.{html,md}",
  ],
  safelist: [
    'text-4xl',  'text-3xl', 'text-2xl', 'leading-6',
    'text-gray-800',
    'mx-2', 'ml-2',
    'mb-4', 'mb-2', 'mb-3',
    'font-bold', 'font-fredoka-one-regular','font-medium'
  ],
  theme: {
    extend: {
    fontFamily: {
        'fredoka-one-regular': 'fredoka_oneregular, sans-serif',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
};

module.exports = config;
