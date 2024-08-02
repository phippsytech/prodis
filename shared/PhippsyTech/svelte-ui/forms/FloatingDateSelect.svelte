<script>
    import { onMount } from "svelte";
    import { createEventDispatcher } from "svelte";
    import { makeUniqueId, updateValidity } from "../js/base.js";

    import ValidityStore from "../js/ValidityStore";
    import { V } from "../js/validation";
    import { formatDate } from "@shared/utilities";

    export let value;
    export let label;
    export let validation_error = `Invalid Date`;
    export let readOnly = false;
    export let start_year = new Date().getFullYear() - 70;
    export let end_year = new Date().getFullYear() + 5;

    const year_array = Array.from(
        { length: end_year - start_year + 1 },
        (_, i) => String(start_year + i),
    );
    const dispatch = createEventDispatcher();

    let day;
    let month;
    let year;
    let display = null; // this is the visual version of the date
    let id = makeUniqueId();

    $: {
        if (display == null && value) {
            display = value;
            let parts = display.split("-");
            year = parts[0];
            month = parts[1];
            day = parts[2];
        }
    }

    updateValidity(ValidityStore, id, null);

    $: validity = $ValidityStore;
    // Observe the validation state.
    $: valid = validity[id];

    function validate() {
        if (valid != true) {
            V.date({
                value: value,
            })
                .then(function () {
                    updateValidity(ValidityStore, id, true);
                })
                .catch(function (reason) {
                    validation_error = reason.error_message;
                    updateValidity(ValidityStore, id, false);
                });
        }
    }

    function handleDateChange() {
        value = year + "-" + month + "-" + day;
        dispatch("change", value);
    }
</script>

{#if readOnly}
    <div class="bg-white rounded-sm px-4 py-2 mb-2">
        <div class="text-xs opacity-50">{label}</div>
        {formatDate(value)}
    </div>
{:else}
    <!-- RAW: {value} -->

    <div
        class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
    >
        <div class="block text-xs text-gray-900/50">{label}</div>
        <div class="flex">
            <div class="">
                <select
                    bind:value={day}
                    on:change={handleDateChange}
                    class="block
        w-full
        -mr-2
        text-base
        font-normal
        text-gray-700
        bg-white bg-clip-padding
        block border-0 text-gray-900 placeholder:text-gray-400 outline-none"
                >
                    <option disabled>day</option>
                    <option value="01">1</option>
                    <option value="02">2</option>
                    <option value="03">3</option>
                    <option value="04">4</option>
                    <option value="05">5</option>
                    <option value="06">6</option>
                    <option value="07">7</option>
                    <option value="08">8</option>
                    <option value="09">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                    <option value="22">22</option>
                    <option value="23">23</option>
                    <option value="24">24</option>
                    <option value="25">25</option>
                    <option value="26">26</option>
                    <option value="27">27</option>
                    <option value="28">28</option>
                    <option value="29">29</option>
                    <option value="30">30</option>
                    <option value="31">31</option>
                </select>
            </div>

            <div class="">
                <select
                    bind:value={month}
                    on:change={handleDateChange}
                    class="
        block
        w-full
        -mr-4
        text-base
        font-normal
        text-gray-700
        bg-white bg-clip-padding
        block border-0 text-gray-900 placeholder:text-gray-400 outline-none
        "
                >
                    <option disabled>month</option>

                    <option value="01">Jan</option>
                    <option value="02">Feb</option>
                    <option value="03">Mar</option>
                    <option value="04">Apr</option>
                    <option value="05">May</option>
                    <option value="06">Jun</option>
                    <option value="07">Jul</option>
                    <option value="08">Aug</option>
                    <option value="09">Sep</option>
                    <option value="10">Oct</option>
                    <option value="11">Nov</option>
                    <option value="12">Dec</option>
                </select>
            </div>

            <div class="">
                <select
                    bind:value={year}
                    on:change={handleDateChange}
                    class="
        block
        w-full
        text-base
        font-normal
        text-gray-700
        bg-white bg-clip-padding
        block w-full border-0 text-gray-900 placeholder:text-gray-400 outline-none
        "
                >
                    <option disabled>year</option>
                    {#each year_array as year_item}
                        <option value={year_item}>{year_item}</option>
                    {/each}
                </select>
            </div>
        </div>
    </div>
{/if}
