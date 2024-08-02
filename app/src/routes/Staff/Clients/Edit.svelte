<script>

    import {push} from 'svelte-spa-router'
    
    import Container from '@shared/Container.svelte';
    import Footer from '@shared/Footer.svelte';
    import Breadcrumbs from '@shared/Breadcrumbs.svelte';
    import FloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';
    import Button from '@shared/PhippsyTech/svelte-ui/Button.svelte';
    import {jspa} from "@shared/jspa.js"

    export let params;

    
    const house_id = params.house_id
    const staffer_id = params.staff_id

    let house = {}
    let house_staffer = {}
    let staffer = {}
    let breadcrumbs_path = [];
    
    jspa("/SIL/House", "getHouse", {id: house_id}).then((result)=>{
        house = result.result;
        breadcrumbs_path = [
            {url:'/sil', name:'SIL'},
            {url:'/sil/'+house_id, name:house.name+" House"},
        ];
    })

    jspa("/SIL/House/Staff", "getHouseStaffer", {id: staffer_id}).then((result)=>{
        house_staffer = result.result;
        jspa("/Staff", "getStaffer", {id:house_staffer.staff_id}).then((result)=> {
            staffer = result.result
        });
    })
 
    function updateHouseStaffer(){
        house_staffer.house_id = house.id;      
        jspa("/SIL/House/Staff", "updateHouseStaffer", house_staffer).then((result)=>{
            push('/sil/'+house.id);
        })
    }
  
</script>
  
<Container>
    <Breadcrumbs path={breadcrumbs_path} target="Edit House Staffer" />
    {staffer.name}
    <FloatingSelect
        on:change
        bind:value={house_staffer.level}
        label="Level"
        instruction="Choose Level"
        options={[
            {option: "Level 1",value:"1"},
            {option: "Level 2",value:"2"},
            {option: "Level 3",value:"3"},
            {option: "Level 4",value:"4"},
            {option: "Level 5",value:"5"},
        ]}  
        hideValidation={true}
    /> 
    <div class="flex justify-between ">
        <span></span>
        <Button on:click={()=>updateHouseStaffer()} label="Update"  />
    </div>
    
<Footer />