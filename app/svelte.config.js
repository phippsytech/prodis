import preprocess from "svelte-preprocess";

const config = {
  preprocess: [
    preprocess({
      postcss: true,
    }),
  ],
  output: {
    preloadStrategy: "preload-mjs",
  }
};

export default config;
