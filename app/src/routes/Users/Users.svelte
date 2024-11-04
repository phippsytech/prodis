<script>
  import { push } from "svelte-spa-router";
  import Role from "@shared/Role.svelte";
  import { slide } from "svelte/transition";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { timeAgoOLD } from "@shared/utilities.js";

  import Filter from "./Filter.svelte";

  let users = [];
  let search = "";

  jspa("/User", "listUsers", {}).then((result) => {
    users = result.result;
    users.forEach((user, index) => {
      users[index].roles = JSON.parse(user.roles);
    });
  });

  // Handle display of archived users
  let showArchived = false;
  let userList = [];
  $: {
    userList = users.filter(
      (user) => user.email.toLowerCase().includes(search.toLowerCase()) == true
    );

    // sort emails alphabetically
    userList.sort(function (a, b) {
      const nameA = a.email.toUpperCase(); // ignore upper and lowercase
      const nameB = b.email.toUpperCase(); // ignore upper and lowercase
      if (nameA < nameB) return -1;
      if (nameA > nameB) return 1;
      return 0; // emails must be equal
    });

    BreadcrumbStore.set({ path: [{ url: "/users", name: "Users" }] });
  }
</script>

<div class="flex items-center mb-2 px-4">
  <div class="flex-auto">
    <div
      class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
      Users
    </div>
    <p>Manage users and roles they have.</p>
  </div>
</div>

<Role roles={["super"]} conditional={true}>
  <div slot="authorised">
    <div class="flex grow flex-col gap-y-2 overflow-y-auto bg-white">
      <div class="flex h-16 shrink-0 items-center">
        <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-2 px-6">
          <div class="relative flex flex-1">
            <label for="search-field" class="sr-only">Search</label>
            <svg
              class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-slate-400"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                clip-rule="evenodd"
              />
            </svg>
            <input
              bind:value={search}
              id="search-field"
              class="block h-full w-full border-0 py-0 pl-8 pr-0 text-slate-900 placeholder:text-slate-400 focus:ring-0 sm:text-sm outline-none"
              placeholder="search by email..."
              type="search"
              name="search"
            />
          </div>

          <div class="flex items-center gap-x-4 lg:gap-x-6">
            <button
              on:click={() => push("/users/add")}
              type="button"
              class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
              >Add User</button
            >
          </div>
        </div>
      </div>

      <div class="px-6">
        <Filter />
      </div>
    </div>

    <table class="min-w-full divide-y divide-indigio-100">
      <thead class=" text-xs">
        <tr>
          <th class="p-2 text-left font-medium">User Email</th>
          <th class="p-2 text-left font-medium">Last Seen</th>
          <th class="p-2 text-left font-medium">Roles</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200 bg-white">
        {#each userList as user, index (user.id)}
          <tr
            in:slide={{ duration: 200 }}
            out:slide={{ duration: 200 }}
            on:click={() => push("/users/" + user.id)}
            class="px-6 py-2 hover:bg-indigo-600 hover:text-white focus:outline-none focus:ring-0 focus:bg-slate-200 focus:text-slate-600 cursor-pointer {userList.length -
              1 ==
            index
              ? 'rounded-b-lg'
              : ''}border-b border-slate-200 w-full {user.archived == 1
              ? 'text-slate-400 '
              : 'text-slate-900 '}"
          >
            <td class=" flex pr-2 pl-2">
              <!-- <div class="flex-none rounded-full bg-emerald-500/20 p-1 inline-block mr-2 opacity-0"> -->
              <!-- <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div> -->

              <svg
                class="h-4 w-4 mt-1"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
              >
                <path
                  d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z"
                ></path>
              </svg>

              <!-- </div> -->
              <div class="pl-2">
                <div>{user.name}</div>
                <div class="block text-xs w-full">
                  {user.email}
                </div>
              </div>
            </td>
            <td
              class="whitespace-nowrap py-2 text-slate-900 text-left pl-0 text-xs"
            >
              {#if user.last_seen}
                {timeAgoOLD(user.last_seen)}
              {/if}
            </td>
            <td
              class="whitespace-nowrap py-2 text-sm font-medium text-slate-900 text-left pl-0"
            >
              {#if user.roles}
                {#each user.roles as role}
                  <span
                    class="inline-block whitespace-nowrap rounded-[0.25rem] bg-slate-50 px-2 pb-1 pt-1 text-center align-baseline text-[0.75em] leading-none text-black mx-1"
                    >{role}</span
                  >
                {/each}
              {/if}
            </td>
          </tr>
        {:else}
          loading...
        {/each}
      </tbody>
    </table>
  </div>
  <div slot="declined" class="text-slate-400">
    You do not have permission to view this page.
  </div>
</Role>
