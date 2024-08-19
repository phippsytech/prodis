import "./app.postcss";
import App from "./App.svelte";
import { loadConfig } from './configStore';

// Load the configuration data before initializing the app
loadConfig().then(() => {
  const app = new App({
    target: document.getElementById("app"),
  });
});

export default app;

