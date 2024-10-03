<script>
import RTE from "@shared/RTE/RTE.svelte";
import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
import NewFloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';

let compliment - {};

</script>

<!-- date of compliment -->
<FloatingDate />

<!-- name of person giving the compliment -->
<FloatingInput />

<!-- description -->
<RTE/>

<!-- staff being complimented  -->
<FloatingCombo />

<!-- action taken  -->
<FloatingTextArea />

<!-- status  -->
<NewFloatingSelect />

<!-- date of acknowledgement , 
 check if this is necessary 
 once we set the status to acknowledged it should put the current date
-->
<FloatingDate />




