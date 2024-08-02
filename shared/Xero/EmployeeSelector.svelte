<script>
    // A drop down for selecting Xero Employee records isn't the right approach
    // The right approach is for the CRM to create the Xero Employee record and then
    // manage it from the CRM.  If there was a webhook received when the Xero Employee

    // <XeroEmployeeSelector bind:xero_employee_id={employee.xero_employee_ref} />

    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let xero_employee_id;

    let readOnly = false;

    let employees = [];
    let employeeList = [];
    let employeeSelectElement = null;

    jspa("/Xero", "getEmployees", {})
        .then((result) => {
            employees = result.result;
            employees.forEach((employee) => {
                employeeList.push({
                    option: employee.name,
                    value: employee.id,
                });
            });

            employeeList.sort(function (a, b) {
                const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });

            employeeList = employeeList;
        })
        .catch(() => {});

    function setXeroEmployeeId(xero_employee_id) {
        xero_employee_id = xero_employee_id;
    }
</script>

<FloatingSelect
    on:change
    bind:value={xero_employee_id}
    label="Xero Employee Record"
    instruction="Choose Xero Employee Record"
    options={employeeList}
    {readOnly}
/>
