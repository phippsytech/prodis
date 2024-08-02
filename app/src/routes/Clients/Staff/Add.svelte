<script>
    import { push } from "svelte-spa-router";

    import Container from "@shared/Container.svelte";
    import Footer from "@shared/Footer.svelte";
    import Breadcrumbs from "@shared/Breadcrumbs.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { jspa } from "@shared/jspa.js";
    import { HouseStore } from "@shared/stores.js";

    $: houseStore = $HouseStore;
    $: {
        if (houseStore.id) {
            breadcrumbs_path = [{ url: "/", name: houseStore.name + " House" }];

            // getClient(houseStore.client_id);
        }
    }

    // let house = {}
    let house_staffer = {};
    let breadcrumbs_path = [];
    let staff = [];
    let staffList = [];

    // jspa("/SIL/House", "getHouse", {id: houseStore.id}).then((result)=>{
    //     house = result.result;
    //     breadcrumbs_path = [
    //         {url:'/sil', name:'SIL'},
    //         {url:'/sil/'+house_id, name:houseStore.name+" House"},
    //     ];
    // })

    jspa("/Staff", "listStaff", {}).then((result) => {
        staff = result.result;
        staff.sort(function (a, b) {
            const nameA = a.name.toUpperCase(); // ignore upper and lowercase
            const nameB = b.name.toUpperCase(); // ignore upper and lowercase
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0; // names must be equal
        });
        staff.forEach((staffer) => {
            if (staffer.archived != 1)
                staffList.push({ option: staffer.name, value: staffer.id });
        });
        staffList = staffList;
    });

    function addHouseStaffer() {
        house_staffer.house_id = houseStore.id;
        jspa("/SIL/House/Staff", "addHouseStaffer", house_staffer).then(
            (result) => {
                push("/");
            },
        );
    }
</script>

<FloatingSelect
    bind:value={house_staffer.staff_id}
    label="Staffer"
    instruction="Choose staffer"
    options={staffList}
    hideValidation={true}
/>
<FloatingSelect
    bind:value={house_staffer.level}
    label="Level"
    instruction="Choose Level"
    options={[
        { option: "Level 1", value: "1" },
        { option: "Level 2", value: "2" },
        { option: "Level 3", value: "3" },
        { option: "Level 4", value: "4" },
        { option: "Level 5", value: "5" },
    ]}
    hideValidation={true}
/>
<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addHouseStaffer()} label="Add" />
</div>
