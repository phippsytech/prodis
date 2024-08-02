<script>
    import flatpickr from "flatpickr";

    export let value=null;
    
    export let label;
    export let placeholder="click to set" ;
    export let mode="date";
    export let minDate=new Date();
    let currentElement;
    
    $: if(currentElement) currentElement.setDate(value, true);

    /*
        If the picker is not populating or not choosing a date before today, 
        this is the culprit.
        When you set minDate, any date before minDate cannot be picked.
        It may also have a weird affect on trying to pick today's date.

    */
    $: if(currentElement) currentElement.set("minDate", minDate);

    function datepicker(element){
        
        if (mode == "date"){
            currentElement = flatpickr(element, {
                minDate: minDate,
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() + 1)),
                allowInvalidPreload: true,
                allowInput: true,
                altInput: true,
                altFormat: "D j M Y",
                dateFormat: "Y-m-d",
            });
        }

        if (mode == "time"){
            currentElement = flatpickr(element, {
                allowInvalidPreload: true,
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K",
                allowInput: true,
            });
        }
        
    }
</script>

<style>

    .form-floating {
        position:relative;
    }

    .form-floating ::-webkit-input-placeholder {
        opacity: 1;
        transition: inherit;
        color:#ccc;
    }
    
    .form-floating ::-moz-placeholder {
        opacity: 1;
        transition: inherit;
        color:#ccc;
    }
    
    .form-floating input:focus::-webkit-input-placeholder {
        opacity: 1;
    }

    .form-floating  label {
        position:absolute;
        top:-0.5rem;
        left:0.15rem;
        opacity: 0.65;
        transform: scale(0.85);
    }

    .form-floating .form-control {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }

</style>



<div class="form-floating mb-2">
    <input use:datepicker type="text" class="form-control
    inline-block
    w-full
    px-3
    py-1.5
    text-base
    font-normal
    text-gray-700
    bg-white bg-clip-padding
    border border-solid border-gray-300
    rounded
    transition
    ease-in-out
    m-0
    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" placeholder={placeholder} bind:value={value} >
    <label for="floatingInput" class="text-gray-700">{label}</label>
</div>