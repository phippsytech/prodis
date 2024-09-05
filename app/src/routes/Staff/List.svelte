<script>
	import { onMount } from "svelte";
	import { jspa } from "@shared/jspa.js";
	import StafferCard from "./StafferCard.svelte";
	import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";

	let staff = [];
	let staff_list = [];
	let showArchived = false;
	let filters = [{ label: "archived", enabled: false }];
	export let search = "";

	onMount(() => {
		jspa("/Staff", "listStaff", {}).then((result) => {
			staff = result.result;

			staff.sort(function (a, b) {
				if (a.first_name == null) return -1;
				if (b.first_name == null) return 1;

				const nameA = a.first_name.toUpperCase(); // ignore upper and lowercase
				const nameB = b.first_name.toUpperCase(); // ignore upper and lowercase
				if (nameA < nameB) return -1;
				if (nameA > nameB) return 1;
				return 0; // names must be equal
			});
		});
	});

	$: {
		showArchived = filters.find((f) => f.label === "archived").enabled;

		if (!showArchived) {
			staff_list = staff.filter((staffer) => staffer.archived != 1);
		} else {
			staff_list = staff;
		}

		if (search.length > 0) {
			const lowerSearch = search.toLowerCase(); // Normalize search input once

			// Filter by email, phone, display_name, first_name, or last_name
			staff_list = staff_list.filter((staffer) => {
				return (
					staffer.first_name?.toLowerCase().includes(lowerSearch) ||
					staffer.staff_name?.toLowerCase().includes(lowerSearch) ||
					staffer.last_name?.toLowerCase().includes(lowerSearch)
				);
			});

			// filter by client name
			// there is a problem which will mean if client.name is removed it will effectively hide the record
			// staff_list = staff_list.filter(
			// (staffer) =>
			// 	staffer.first_name &&
			// 	staffer.first_name.toLowerCase().includes(search.toLowerCase()) ==
			// 	true,
			// );
		}
	}
</script>

<div class="bg-white px-3 rounded-md pb-1">
	<Filter bind:filters />
</div>

<div
	class="grid grid-cols-1 gap-2 sm:gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4"
>
	{#each staff_list as staffer}
		<div>
			<StafferCard {staffer} />
		</div>
	{/each}
</div>
