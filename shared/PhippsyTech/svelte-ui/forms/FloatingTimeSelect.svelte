<script>
    import { makeUniqueId, updateValidity } from "../js/base.js";
    import ValidityStore from "../js/ValidityStore";
    import { V } from "../js/validation";

    export let value;
    export let label;
    export let validation_error = `Invalid Date`;
    export let start_year = new Date().getFullYear() - 65;
    export let end_year = new Date().getFullYear() + 5;

    const year_array = Array.from(
        { length: end_year - start_year + 1 },
        (_, i) => String(start_year + i),
    );

    let day;
    let month;
    let year;

    let display = null; // this is the visual version of the date

    $: {
        if (display == null && value) {
            display = getDisplayDate(value);
            let parts = display.split("/");

            day = parts[0];
            month = parts[1];
            year = parts[2];
        }
    }

    let id = makeUniqueId();
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

    function getDisplayDate(timestamp) {
        // remember, this is a timestamp from PHP, you need to * 1000
        let date = new Date(timestamp * 1000);

        let formattedDate = date.toLocaleDateString("en-GB", {
            day: "numeric",
            month: "numeric", // short
            year: "numeric",
        });

        return formattedDate;
    }

    function handleDateChange() {
        let date = new Date(
            parseInt(year, 10),
            parseInt(month, 10) - 1,
            parseInt(day, 10),
        );
        value = date.getTime() / 1000; // preparing timestamp for php
    }
</script>

<div
    class="bg-white border border-solid border-indigo-100 rounded px-3 py-1.5 mb-2"
>
    <div class="text-xs opacity-50">{label}</div>
    <div class="flex">
        <div>
            <select
                bind:value={day}
                on:change={handleDateChange}
                class="form-select appearance-none block px-1 pr-7 m-0 mr-1 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:outline-none"
            >
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
            </select>
        </div>

        <div>
            <select
                bind:value={month}
                on:change={handleDateChange}
                class="form-select appearance-none block px-1 pr-7 m-0 mr-1 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:outline-none"
            >
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

        <div>
            <select
                bind:value={year}
                on:change={handleDateChange}
                class="form-select appearance-none block px-1 pr-7 m-0 mr-1 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:outline-none"
            >
                {#each year_array as year_item}
                    {year_item}
                    {year}
                    <option value={year_item}>{year_item}</option>
                {/each}
            </select>
        </div>
    </div>
</div>
