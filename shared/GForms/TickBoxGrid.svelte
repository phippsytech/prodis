<script>
    export let rows = [
        "Fatalities, ill health or permanent disability",
        "Serious injury or long-term illness",
        "Medical treatment and several days off work",
        "Minor injury, illness or damage",
    ];

    export let columns = [
        "Very likely",
        "Likely",
        "Unlikely",
        "Very unlikely",
    ];

    let selectedValues = {};
    $: {
        rows.forEach(row => {
            if (!selectedValues[row]) {
                selectedValues[row] = {};
            }
            columns.forEach(column => {
                if (!selectedValues[row][column]) {
                    selectedValues[row][column] = false;
                }
            });
        });
    }
</script>

<table>
    <thead>
        <tr>
            <th></th>
            {#each columns as column}
                <th>{column}</th>
            {/each}
        </tr>
    </thead>
    <tbody>
        {#each rows as row}
            <tr>
                <th>{row}</th>
                {#each columns as column}
                    <td>
                        <label>
                            <input
                                type="checkbox"
                                name="{row}"
                                value="{column}"
                                bind:checked={selectedValues[row][column]}
                            />
                        </label>
                    </td>
                {/each}
            </tr>
        {/each}
    </tbody>
</table>

<pre>{JSON.stringify(selectedValues, null, 2)}</pre>