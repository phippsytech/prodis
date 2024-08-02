<script>
    
    import EndOfShift from './TimelineItems/EndOfShift.svelte';
    import Expense from './TimelineItems/Expense.svelte';
    import FoodIntake from './TimelineItems/FoodIntake.svelte';
    import Incident from './TimelineItems/Incident.svelte';
    import Injury from './TimelineItems/Injury.svelte';
    import MaintenanceRequest from './TimelineItems/MaintenanceRequest.svelte';
    import Medication from './TimelineItems/Medication.svelte';
    import Note from './TimelineItems/Note.svelte';
    import NotFound from './TimelineItems/NotFound.svelte';
    import ParentalContact from './TimelineItems/ParentalContact.svelte';
    import RiskAssessment from './TimelineItems/RiskAssessment.svelte';
    import SleepDisturbance from './TimelineItems/SleepDisturbance.svelte';
    import SleepTime from './TimelineItems/SleepTime.svelte';
    import Toilet from './TimelineItems/Toilet.svelte';
    import WakeUp from './TimelineItems/WakeUp.svelte';

    export let activity={};

    const components = {
        EndOfShift: EndOfShift,
        Expense: Expense,
        FoodIntake: FoodIntake,
        Incident: Incident,
        Injury: Injury,
        MaintenanceRequest: MaintenanceRequest,
        Medication: Medication,
        Note: Note,
        NotFound: NotFound,
        ParentalContact: ParentalContact,
        RiskAssessment: RiskAssessment,
        SleepDisturbance: SleepDisturbance,
        SleepTime: SleepTime,
        Toilet: Toilet,
        WakeUp: WakeUp,
    }

    let component;

    switch(activity.report_type){
        case "Wake Up": activity.report_type="WakeUp"; break;
        case "Sleep Time": activity.report_type="SleepTime"; break;
        case "End of Shift": activity.report_type="EndOfShift"; break;
        // case "Incident": activity.report_type="Incident"; break;
        case "Maintenance Request": activity.report_type="MaintenanceRequest"; break;
        case "Note": activity.report_type="Note"; break;
        case "Risk Assessment": activity.report_type="RiskAssessment"; break;
        case "Sleep Disturbance": activity.report_type="SleepDisturbance"; break;
        case "Sleep Time": activity.report_type="SleepTime"; break;
        case "Toilet Log": activity.report_type="Toilet"; break;
        case "Wake Up": activity.report_type="WakeUp"; break;

    }

    
    $: {
        try {
            if (activity.report_type in components) {
                component = components[activity.report_type];
            } else {
                component = NotFound;
            }
        } catch (e) {
            component = NotFound;
        }
    }

</script>

<svelte:component this={component} key={activity.id} bind:activity={activity} />
