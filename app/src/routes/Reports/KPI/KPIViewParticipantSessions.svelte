<script>
    import KpiViewCaseNote from "./KPIViewCaseNote.svelte";
    import { slide } from "svelte/transition";
    import { quintOut } from "svelte/easing";
    import {
        formatDate,
        convertToHoursAndMinutes,
        convertMinutesToHoursAndMinutes,
        convertDecimalHoursToHoursAndMinutes,
    } from "@shared/utilities.js";

    export let line_items;
    let selected_line_item_id;

    let claim_types = [
        { option: "Direct Service", value: "" },
        { option: "Cancellation", value: "CANC" },
        { option: "NDIA Required Report", value: "REPW" },
        { option: "Provider Travel", value: "TRAN" },
        {
            option: "Non-Face-to-Face Services",
            value: "NF2F",
        },
    ];

    function getOption(value) {
        if (value === null) value = "";
        let option = "";
        claim_types.forEach((item) => {
            if (item.value == value) {
                option = item.option;
            }
        });
        return option;
    }

    function select(id, event) {
        if (selected_line_item_id == id) {
            selected_line_item_id = null;
        } else {
            selected_line_item_id = id;
        }
        event.stopPropagation();
    }
</script>

<ul class="border-y border-gray-200 w-full text-sm">
    {#each line_items as item, index (item.id)}
        <li
            transition:slide|global={{ duration: 250, easing: quintOut }}
            on:click={(event) => select(item.id, event)}
            class=" cursor-pointer hover:bg-gray-50 border-b border-gray-200 py-1 {selected_line_item_id ==
            item.id
                ? 'text-indigo-600 font-medium bg-purple-100'
                : ''}"
        >
            <div class="flex justify-between">
                <div class="pl-2">
                    {formatDate(item.session_date)}
                    {item.service_code}
                    <span class="text-xs">({getOption(item.claim_type)})</span>
                </div>

                <div class=" text-right">
                    {@html convertDecimalHoursToHoursAndMinutes(
                        item.session_duration / 60,
                    )}
                </div>
            </div>
        </li>

        {#if selected_line_item_id == item.id}
            <KpiViewCaseNote line_item={item} />
        {/if}
    {/each}
</ul>
