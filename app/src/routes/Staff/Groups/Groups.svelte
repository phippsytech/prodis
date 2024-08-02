<script>

import { BreadcrumbStore } from '@shared/stores.js';

BreadcrumbStore.set({
    path: [
        {url: "/staff", name: 'Staff'},
        {url: null, name: 'Groups'},
    ]
});
</script>

<h2 class="text-xl font-semibold leading-7 text-gray-900 sm:truncate sm:text-2xl sm:tracking-tight">Staff Groups</h2>
    

<p class="mb-4">Staff Groups are used to organise staff into different functions.  For example, you might have a group for <strong>Therapists</strong>, <strong>SIL</strong> and <strong>Managers</strong>.  One staffer can be a member of multiple staff groups</p>

