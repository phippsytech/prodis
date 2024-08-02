<script>
    import flatpickr from "flatpickr";

    let clazz="";
    export {clazz as class}
    export let value=null;
    
    export let placeholder="click to set" ;
    export let mode="date";
    export let minDate=new Date();
    let currentElement;

    $: if(value==null) value=null

    $: if(currentElement){
        currentElement.set("minDate", minDate);
    }

    function datepicker(element){
        
        if (mode == "date"){
            currentElement = flatpickr(element, {
                minDate: minDate,
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
                allowInput: true,
                altInput: true,
                altFormat: "D j M Y",
                dateFormat: "Y-m-d"
            });
        }

        if (mode == "time"){
            flatpickr(element, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                allowInput: true
            });
        }
        
    }
</script>

<svelte:head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</svelte:head>


    <input use:datepicker type="text" placeholder={placeholder} bind:value={value} class={clazz} >
    
