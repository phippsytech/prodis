// vite.config.js
import { defineConfig } from "file:///app/node_modules/vite/dist/node/index.js";
import { svelte } from "file:///app/node_modules/@sveltejs/vite-plugin-svelte/src/index.js";
import dynamicImportVars from "file:///app/node_modules/rollup-plugin-dynamic-import-variables/index.js";
var vite_config_default = defineConfig({
  resolve: {
    alias: {
      "@shared": "/app/shared",
      "@app": "/app/src"
    }
  },
  build: {
    rollupOptions: {
      external: []
    }
  },
  plugins: [
    svelte(),
    dynamicImportVars({
      include: ["src/**/*.svelte"]
    })
  ],
  optimizeDeps: {
    include: ["@zerodevx/svelte-toast"]
  },
  server: {
    port: 5173,
    watch: {
      usePolling: true,
      interval: 1e3
      // Set a higher interval (milliseconds)
    }
  },
  preview: {
    port: 5173
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvYXBwXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCIvYXBwL3ZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9hcHAvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJ1xuaW1wb3J0IHsgc3ZlbHRlIH0gZnJvbSAnQHN2ZWx0ZWpzL3ZpdGUtcGx1Z2luLXN2ZWx0ZSdcbmltcG9ydCBkeW5hbWljSW1wb3J0VmFycyBmcm9tICdyb2xsdXAtcGx1Z2luLWR5bmFtaWMtaW1wb3J0LXZhcmlhYmxlcyc7XG5cbi8vIGh0dHBzOi8vdml0ZWpzLmRldi9jb25maWcvXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICByZXNvbHZlOiB7XG4gICAgYWxpYXM6IHtcbiAgICAgIFwiQHNoYXJlZFwiOiBcIi9hcHAvc2hhcmVkXCIsXG4gICAgICBcIkBhcHBcIjogXCIvYXBwL3NyY1wiXG4gICAgfSxcbiAgfSxcbiAgYnVpbGQ6IHtcbiAgICByb2xsdXBPcHRpb25zOiB7XG4gICAgICBleHRlcm5hbDogW11cbiAgICB9XG4gIH0sXG4gIHBsdWdpbnM6IFtcbiAgICBzdmVsdGUoKSxcbiAgICBkeW5hbWljSW1wb3J0VmFycyh7XG4gICAgICBpbmNsdWRlOiBbJ3NyYy8qKi8qLnN2ZWx0ZSddLFxuICAgIH0pLFxuICBdLFxuICBvcHRpbWl6ZURlcHM6IHtcbiAgICBpbmNsdWRlOiBbJ0B6ZXJvZGV2eC9zdmVsdGUtdG9hc3QnXVxuICB9LFxuICBzZXJ2ZXI6IHtcbiAgICBwb3J0OiA1MTczLFxuICAgIHdhdGNoOiB7XG4gICAgICB1c2VQb2xsaW5nOiB0cnVlLFxuICAgICAgaW50ZXJ2YWw6IDEwMDAsIC8vIFNldCBhIGhpZ2hlciBpbnRlcnZhbCAobWlsbGlzZWNvbmRzKVxuICAgIH0sXG4gIH0sXG4gIHByZXZpZXc6IHtcbiAgICBwb3J0OiA1MTczLFxuICB9LFxufSlcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBOEwsU0FBUyxvQkFBb0I7QUFDM04sU0FBUyxjQUFjO0FBQ3ZCLE9BQU8sdUJBQXVCO0FBRzlCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQzFCLFNBQVM7QUFBQSxJQUNQLE9BQU87QUFBQSxNQUNMLFdBQVc7QUFBQSxNQUNYLFFBQVE7QUFBQSxJQUNWO0FBQUEsRUFDRjtBQUFBLEVBQ0EsT0FBTztBQUFBLElBQ0wsZUFBZTtBQUFBLE1BQ2IsVUFBVSxDQUFDO0FBQUEsSUFDYjtBQUFBLEVBQ0Y7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNQLE9BQU87QUFBQSxJQUNQLGtCQUFrQjtBQUFBLE1BQ2hCLFNBQVMsQ0FBQyxpQkFBaUI7QUFBQSxJQUM3QixDQUFDO0FBQUEsRUFDSDtBQUFBLEVBQ0EsY0FBYztBQUFBLElBQ1osU0FBUyxDQUFDLHdCQUF3QjtBQUFBLEVBQ3BDO0FBQUEsRUFDQSxRQUFRO0FBQUEsSUFDTixNQUFNO0FBQUEsSUFDTixPQUFPO0FBQUEsTUFDTCxZQUFZO0FBQUEsTUFDWixVQUFVO0FBQUE7QUFBQSxJQUNaO0FBQUEsRUFDRjtBQUFBLEVBQ0EsU0FBUztBQUFBLElBQ1AsTUFBTTtBQUFBLEVBQ1I7QUFDRixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
