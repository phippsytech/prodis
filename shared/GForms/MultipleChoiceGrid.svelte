<script>
    export let rows = [];
    export let columns = [];
    export let values = [];
    export let scores = [[]];
    export let scoreLabels = [];
    export let score = "";
    const id = Math.random().toString(36).substring(7);

    function getScore() {
        const value = Object.values(values);
        let value_scores = [];
        value.forEach((item, row_index) => {
            const column_index = columns.findIndex((val) => val === item);

            value_scores.push(lookupScore(row_index, column_index));
        });
        // get the largest value from an array
        const largest = Math.max(...value_scores);
    }

    function lookupScore(row_index, column_index) {
        const score = scores[row_index][column_index];
        return score;
    }
</script>

<table class="table-auto w-full">
    <thead>
        <tr>
            <!-- <th class="px-4 text-left"></th> -->
            {#each columns as column}
                <th class="px-4 text-center">{column}</th>
            {/each}
        </tr>
    </thead>
    <tbody>
        {#each rows as row, row_index}
            <tr>
                <td colspan={columns.length} class="px-4 text-left">{row}</td>
            </tr>
            <tr>
                <!-- <th class="px-4 text-left">{row}</th> -->
                {#each columns as column, column_index}
                    <td class="px-4 py-2 text-center">
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                name="{row}-{id}"
                                value={column}
                                id="{id}-{row_index}-{column_index}"
                                class="w-6 h-6 text-indigo-600 border-gray-300 rounded-full focus:ring-0"
                                bind:group={values[row]}
                                on:change={() => getScore()}
                            />
                        </label>
                    </td>
                {/each}
            </tr>
        {/each}
    </tbody>
</table>

<!-- 

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    {#each rows as row}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-2 text-lg font-medium text-gray-800 bg-gray-100">{row}</div>
            <div class="px-4 py-2 grid grid-cols-2 gap-2">
                {#each columns as column}
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="{row}"
                            value="{column}"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded-full focus:ring-0"
                            bind:group={values[row]}
                        />
                        <span class="ml-2 text-sm font-medium text-gray-700">{column}</span>
                    </label>
                {/each}
            </div>
        </div>
    {/each}
</div> -->
