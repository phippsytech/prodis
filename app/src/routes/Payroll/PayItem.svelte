<script>
    import { jspa } from "@shared/jspa.js";

    export let value = null;

    let payItems = [];

    jspa("/Payroll", "listPayItems", {}).then((result) => {
        payItems = result.result;
    });

    function getPayItemNameByRef(xeroRef) {
        const item = payItems.find((item) => item.payItemXeroRef === xeroRef);
        return item ? item.payItemName : null;
    }

    $: payItemName = getPayItemNameByRef(value);
</script>

{payItemName}
