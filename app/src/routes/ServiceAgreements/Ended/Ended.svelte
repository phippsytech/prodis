<script>
    export let service_agreements = [];
</script>

<p>
    These are Service Agreements that have expired, and either need : Renew or
    End Agreement
</p>

<table class="min-w-full divide-y divide-gray-300">
    <thead>
        <tr>
            <th
                scope="col"
                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                >Participant</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Expired</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Action</th
            >
            <th
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >Role</th
            >
            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                <span class="sr-only">Edit</span>
            </th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        <tr>
            <td
                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                >Lindsay Walton</td
            >
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                >16 Aug 2024 (5 days ago)</td
            >
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                >To be renewed</td
            >
            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                >Member</td
            >
            <td
                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
            >
                <a href="#" class="text-indigo-600 hover:text-indigo-900"
                    >Edit<span class="sr-only">, Lindsay Walton</span></a
                >
            </td>
        </tr>

        <!-- More people... -->
    </tbody>
</table>
