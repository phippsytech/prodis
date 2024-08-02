<script>
    import FloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';

    export let rows = [];
    export let columns = [];
    export let values = [];
    export let scores = [[]]
    export let scoreLabels = []
    export let score = ""

    const options = columns.map((column) => ({
        option: column,
        value: column,
    }));


    function getScore(){
        let value_scores = [];
        for (const [key, value] of Object.entries(values)) {
            const row_index = rows.findIndex((val) => val === key);
            const column_index = columns.findIndex((val) => val === value);

            if (row_index !== -1 && column_index !== -1) {
                value_scores.push(lookupScore(row_index, column_index));
            }
        }
        
        // get the largest value from an array  
        // const largest = Math.max(...value_scores);  
        const largest = value_scores.reduce((a, b) => Math.max(a, b), -Infinity); // Copilot suggested this was better
        score = scoreLabels[largest];

    }


    function lookupScore(row_index,column_index){
        return scores[row_index][column_index];
    }


    $: if(values){
        getScore()
    }


</script>

{#each rows as row}

    <div class="px-2">{row}</div>

        <FloatingSelect 
            bind:value={values[row]}
            label="Likelihood"
            instruction="Select Likelihood"
            options={options}  
        />

{/each}
