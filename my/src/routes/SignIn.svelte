<script>

    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";

</script>
<!--
  This example requires some changes to your config:
  
  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
-->
<!--
  This example requires updating your template:

  ```
  <html class="h-full bg-white">
  <body class="h-full">
  ```
-->
<div class="flex min-h-full items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-sm space-y-10">
      <div>
        <img class="mx-auto h-10 w-auto" src="/NDISmate-logo-text-color.svg" alt="NDISmate">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to your account</h2>
      </div>

      <div>
      <FloatingInput label="Email address" placeholder="eg: you@ndisprovider.com.au" type="email" />
        <div>
          <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
        </div>
    </div>  
      <p class="text-center text-sm leading-6 text-gray-500">
        Need an account?
        <a href="/#/" class="font-semibold text-indigo-600 hover:text-indigo-500">Sign up now.</a>
      </p>
    </div>
  </div>
  