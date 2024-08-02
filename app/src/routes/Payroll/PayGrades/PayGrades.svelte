<script>
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";

    import { BreadcrumbStore } from "@shared/stores.js";

    let paygrades = [];

    jspa("/Payroll/PayGrade", "listPayGrades", {}).then((result) => {
        paygrades = result.result;
    });

    BreadcrumbStore.set({
        path: [
            { url: "/payroll", name: "Pay Roll" },
            { url: null, name: "Pay Grades" },
        ],
    });
</script>

<div class="flex items-center mb-2">
    <div class="flex-auto">
        <div
            class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
        >
            Pay Grades
        </div>
    </div>
    <a
        href="/#/payroll/paygrades/add"
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add</a
    >
</div>

<!-- <div class="font-medium px-2">Once off</div> -->

<ul
    role="list"
    class="divide-y divide-gray-200 bg-white border border-gray-200 rounded mb-4"
>
    {#each paygrades as paygrade}
        <li
            on:click={() => push("/payroll/paygrades/" + paygrade.id)}
            class="relative p-2 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
        >
            <div>{paygrade.name}</div>
            <!-- <div class="text-xs font-light text-gray-600">Dentist Appointment</div> -->
        </li>
    {/each}
</ul>
