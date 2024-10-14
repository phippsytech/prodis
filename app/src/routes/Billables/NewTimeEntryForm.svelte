<script>
    import Container from "@shared/Container.svelte";
    import FloatingDurationSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDurationSelect.svelte";
    import PlanManagerSelector from "@app/routes/Accounts/PlanManagers/PlanManagerSelector.svelte";
    import ClaimSelector from "./ClaimSelector.svelte";
    import RTE from "@shared/RTE/RTE.svelte";
    import { convertMinutesToHoursAndMinutes } from "@shared/utilities";
    import Role from "@shared/Role.svelte";

    export let timetracking = {};
    export let mode;
    export let readOnly;
    export let budget_exceeded;
    export let available_session_duration;

</script>
{#if timetracking.staff_id && timetracking.client_id && timetracking.service_booking_id && timetracking.service_booking_id != "Choose service"}
    {#if mode == "edit"}
        <Role roles={["admin"]}>
            <PlanManagerSelector
                bind:planmanager_id={timetracking.planmanager_id}
                {readOnly}
            />
        </Role>
    {/if}
    {#if available_session_duration > 0}
        <ClaimSelector
            bind:claim_type={timetracking.claim_type}
            bind:cancellation_reason={timetracking.cancellation_reason}
            {readOnly}
        />

        <FloatingDurationSelect
            label="Session Duration"
            bind:value={timetracking.session_duration}
            {readOnly}
        />

        {#if budget_exceeded}
            <div class="text-center text-red-500 font-bold">
                This will take you over budget. There is only {@html convertMinutesToHoursAndMinutes(
                    available_session_duration,
                )} available.
            </div>
        {/if}
    {:else}
        <div class="text-center text-red-500">
            No available session duration for this service.
        </div>
    {/if}

    <!-- <FloatingInput
        label="Billing Summary"
        placeholder="eg: Developmental assessment conducted via video call."
        bind:value={timetracking.actual_travel_time}
        {readOnly}
    /> -->

    <Container>
        <div style="z-index:1">
            <div class="mb-2 opacity-50 text-xs">Case Note</div>
            <div class="flex items-start px-2 mb-2">
                <div class="flex h-6 items-center">
                    <input
                        id="comments"
                        aria-describedby="comments-description"
                        name="comments"
                        type="checkbox"
                        bind:checked={timetracking.internal}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                    />
                </div>
                <div class="ml-2 text-sm leading-6">
                    <label
                        for="comments"
                        class="font-medium text-gray-900"
                        >Internal Only</label
                    >
                    <span
                        id="comments-description"
                        class="text-gray-500 text-xs italic"
                        ><span class="sr-only">Internal Only </span> (Tick
                        to prevent stakeholders reading this case note)</span
                    >
                </div>
            </div>

            <RTE bind:content={timetracking.notes} />
        </div>
    </Container>
{/if}