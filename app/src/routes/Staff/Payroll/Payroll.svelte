<script>
    import { onMount } from "svelte";
    import Role from "@shared/Role.svelte";
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    import SearchAddress from "@shared/PhippsyTech/svelte-ui/SearchAddress.svelte";
    import PayGradeSelector from "../../Payroll/PayGrades/PayGradeSelector.svelte";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";

    // You need to define the component prop "params"
    export let params = {};

    let staff_id = params.staff_id;
    let staffer = {};
    let stored_staffer = {};
    let mounted = false;
    let show = false;

    $: {
        if (mounted) {
            show = !(
                JSON.stringify(staffer) === JSON.stringify(stored_staffer)
            );
        }
    }

    onMount(async () => {
        staffer = await jspa("/Staff", "getStaffer", { id: staff_id }); //getStaffer(staff_id)
        staffer = staffer.result;

        stored_staffer = Object.assign({}, staffer);
        if (staffer.groups == null) staffer.groups = [];

        if (staffer.xero_employee_ref) staffer.employment_type = "employee";
        mounted = true;

        getEmployee(staffer.xero_employee_ref);

        BreadcrumbStore.set({
            path: [
                { url: "/staff", name: "Staff" },
                { url: null, name: staffer.name },
            ],
        });
    });

    function getEmployee(employee_id) {
        jspa("/Payroll", "getEmployee", { employee_id: employee_id }).then(
            (result) => {},
        );
    }

    function undo() {
        staffer = Object.assign({}, stored_staffer);
    }

    async function save() {
        try {
            jspa("/Staff", "updateStaffer", staffer).then((result) => {
                stored_staffer = Object.assign({}, staffer);
            });
        } catch (error) {
            console.error(error);
        }
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(staffer) === JSON.stringify(stored_staffer)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<SearchAddress />

<Role roles={["payroll"]} conditional={true}>
    <div slot="authorised">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
            style="color:#220055;"
        >
            Pay Settings
        </div>

        <Container>
            <FloatingInput
                bind:value={staffer.xero_employee_ref}
                label="Xero Employee Ref"
                placeholder="this will contain a xero id."
            />

            <FloatingSelect
                bind:value={staffer.employment_basis}
                label="Employment Basis"
                instruction="Select Employment Basis"
                options={[
                    {
                        option: "Full-time",
                        value: "FULLTIME",
                    },
                    {
                        option: "Part-time",
                        value: "PARTTIME",
                    },
                    {
                        option: "Casual",
                        value: "CASUAL",
                    },
                ]}
            />

            <FloatingInput
                bind:value={staffer.hours_per_week}
                label="Hours Per Week"
                placeholder="eg: 38"
            />
            <FloatingInput
                bind:value={staffer.ordinary_hours_rate}
                label="Hourly Rate"
                placeholder="eg: 48.07"
            />

            {#if staffer.groups && staffer.groups.includes("sil")}
                <PayGradeSelector bind:value={staffer.paygrade_id} />
            {/if}
        </Container>
    </div>

    <div slot="declined">
        <div class="text-sm text-gray-500">
            You are not authorised to view this page.
        </div>
    </div>
</Role>
