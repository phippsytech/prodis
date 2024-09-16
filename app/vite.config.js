import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import dynamicImportVars from 'rollup-plugin-dynamic-import-variables';

// https://vitejs.dev/config/
export default defineConfig({
  resolve: {
    alias: {
      "@shared": "/app/shared",
      "@app": "/app/src"
    },
  },
  build: {
    rollupOptions: {
      external: []
    }
  },
  plugins: [
    svelte(),
    dynamicImportVars({
      include: ['src/**/*.svelte'],
    }),
  ],
  optimizeDeps: {
    include: ['@zerodevx/svelte-toast']
  },
  server: {
    port: 5173,
    watch: {
      usePolling: true,
      interval: 1000, // Set a higher interval (milliseconds)
    },
  },
  preview: {
    port: 5173,
  },
})
