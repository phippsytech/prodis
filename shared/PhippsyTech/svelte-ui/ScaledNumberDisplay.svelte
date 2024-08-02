<script>
    
    export let value=0;
    
    function getInt(value) {
        return Math.trunc(value);
    }


    function getDec(value){
        let decimalPart = value.toString().split('.')[1] || '0';
        let paddedDecimalPart = Number('0.' + decimalPart).toFixed(4);
        return paddedDecimalPart.slice(2);
    }


</script>

<span class="w-full flex-none text-2xl  tracking-tight {(value <0)?'text-red-600':'text-gray-900'}" >{getInt(value)}<span class="text-sm font-medium ">.{getDec(value)}</span></span>