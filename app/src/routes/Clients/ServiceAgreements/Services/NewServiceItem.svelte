<script>
    import { convertMinutesToHoursAndMinutes } from "@shared/utilities.js";
    import BudgetBar from "@shared/BudgetBar.svelte";
    import BudgetBarWeekly from "@shared/BudgetBarWeekly.svelte";
    import { formatDate, timeAgo } from "@shared/utilities.js";

    export let service_agreement = {};
    export let service = {};

    function weeksLeft(serviceAgreementEndDate) {
        
        // Get the current date        
        const startDate= new Date();
        const endDate = new Date(serviceAgreementEndDate);

        // Get the difference in time (in milliseconds)
        const timeDifference = endDate.getTime() - startDate.getTime();
        
        // Convert time difference from milliseconds to days
        const daysDifference = timeDifference / (1000 * 3600 * 24);
        
        // Convert days to weeks
        const weeksDifference = daysDifference / 7;
        
        return weeksDifference;

    }

</script>

<div class={service.is_active ? "" : "opacity-50"}>
    {#if service.budget_display == "weekly"}
        <div class="block w-full">
            <div
                class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
                style="line-height:0.8rem"
            >
                <div>
                    <span class="text-slate-800 font-bold">{service.code}</span>
                    {#if service.budget && service.budget > 0}
                        <span class="text-slate-600"
                            >{@html convertMinutesToHoursAndMinutes(
                                (service.budget / service.rate) * 60,
                            )}</span
                        >
                        {#if service.adjust_weekly_time}
                            
                        {#if service.remainingMinutes / weeksLeft(service_agreement.service_agreement_end_date) > 1}
                        
                       
                        <span class="text-xs text-slate-400 ml-1">
                            ({@html convertMinutesToHoursAndMinutes(
                service.remainingMinutes /weeksLeft(service_agreement.service_agreement_end_date) )} / wk
                            )
                        </span>
                        {/if}
            {:else}
                {#if service.remainingMinutes / (service.totalServiceDuration / (60 * 24 * 7)) > 1}
                            <span class="italic text-xs text-slate-400"
                                >({@html convertMinutesToHoursAndMinutes(
                                    service.remainingMinutes /
                                        (service.totalServiceDuration /
                                            (60 * 24 * 7)),
                                )} / wk)
                            </span>{/if}

                        {/if}
                    {/if}
                    {#if !service.is_active}
                        - NOT ACTIVE{/if}
                </div>
                <span
                    class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
                    >{service.plan_manager_name}
                </span>
            </div>
            <!-- startDate={service_agreement.service_agreement_signed_date} -->
            <!-- startDate={service.budget_start_date} -->
            <BudgetBarWeekly
                
                totalBudget={service.budget}
                hourlyRate={service.rate}
                spentBudget={service.spent}
                startDate={service.budget_start_date}
                endDate={service_agreement.service_agreement_end_date}
                bind:spendStatus={service.spend_status}
                bind:remainingBudget={service.remainingBudget}
                bind:remainingMinutes={service.remainingMinutes}
                bind:totalServiceDuration={service.totalServiceDuration}
            />
            {#if service.budget && service.budget > 0}
            {#if !service.adjust_weekly_time && service.spend_status != 0}
                <span class="text-xs text-slate-400 ml-1"
                    >{@html convertMinutesToHoursAndMinutes(
                        Math.abs(service.spend_status),
                    )}
                    {service.spend_status > 0
                        ? "of overflow"
                        : "over budget"}</span
                >
                {/if}
            {/if}

        </div>
    {:else}
        <!--20528.84-->
        <div class="block w-full">
            <div
                class="mt-0 p-0 mx-0 sm:flex sm:justify-between"
                style="line-height:0.8rem"
            >
                <div>
                    <span class="text-slate-800 font-bold">{service.code}</span>
                    <span class="text-slate-600"
                        >{@html convertMinutesToHoursAndMinutes(
                            (service.budget / service.rate) * 60,
                        )}</span
                    >
                    {#if !service.is_active}
                        - NOT ACTIVE{/if}
                </div>
                <span
                    class="text-xs text-indigo-300 uppercase hidden sm:inline-block"
                >
                    {service.plan_manager_name}
                </span>
            </div>
            <BudgetBar
                totalBudget={service.budget}
                hourlyRate={service.rate}
                spentBudget={service.spent}
                bind:remainingMinutes={service.remainingMinutes}
            />
            {#if service.last_session_date && service.budget && service.budget > 0 && service.rate && service.remainingMinutes > 1}<span
                    class="text-xs text-slate-400 ml-1"
                    >{@html convertMinutesToHoursAndMinutes(
                        service.remainingMinutes,
                    )} remaining</span
                >{/if}
        </div>
    {/if}

        {#if service.last_session_date}
        <div class=" px-1 text-xs text-slate-400">
        Last billed {timeAgo(service.last_session_date)} {formatDate(service.last_session_date)}
        </div>
        <!-- {:else}
        <div class=" px-1 text-xs text-slate-400">
        No sessions billed yet
    </div> -->
        {/if}

</div>
