import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'

// https://vitejs.dev/config/
export default defineConfig({
  resolve: {
    alias: {
      "@shared": "/var/www/staging.phippsy.tech/ndismate/shared",
      "@app": "/var/www/staging.phippsy.tech/ndismate/my/src",
    },
  },
  plugins: [svelte()],
  server: {
    port: 5175,
  },
  preview: {
    port: 5175,
  },
})
