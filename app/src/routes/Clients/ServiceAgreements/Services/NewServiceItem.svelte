<script>
    import { convertMinutesToHoursAndMinutes } from "@shared/utilities.js";
    import BudgetBar from "@shared/BudgetBar.svelte";
    import BudgetBarWeekly from "@shared/BudgetBarWeekly.svelte";

    export let service_agreement = {};
    export let service = {};
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
                        {#if service.remainingMinutes / (service.totalServiceDuration / (60 * 24 * 7)) > 1}
                            <span class="italic text-xs text-slate-400"
                                >({@html convertMinutesToHoursAndMinutes(
                                    service.remainingMinutes /
                                        (service.totalServiceDuration /
                                            (60 * 24 * 7)),
                                )} / wk)
                            </span>{/if}
                    {/if}
                    {#if !service.is_active}
                        - NOT ACTIVE{/if}
                </div>
                <span
                    class="text-xs text-indigo-200 uppercase hidden sm:inline-block"
                    >{service.plan_manager_name}
                </span>
            </div>
            <BudgetBarWeekly
                totalBudget={service.budget}
                hourlyRate={service.rate}
                spentBudget={service.spent}
                startDate={service_agreement.service_agreement_signed_date}
                endDate={service_agreement.service_agreement_end_date}
                bind:spendStatus={service.spend_status}
                bind:remainingBudget={service.remainingBudget}
                bind:remainingMinutes={service.remainingMinutes}
                bind:totalServiceDuration={service.totalServiceDuration}
            />
            {#if service.budget && service.budget > 0}
                <span class="text-xs text-slate-400 ml-1"
                    >{@html convertMinutesToHoursAndMinutes(
                        Math.abs(service.spend_status),
                    )}
                    {service.spend_status > 0
                        ? "of overflow"
                        : "over budget"}</span
                >
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
                    class="text-xs text-indigo-200 uppercase hidden sm:inline-block"
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
            {#if service.budget && service.budget > 0 && service.rate && service.remainingMinutes > 1}<span
                    class="text-xs text-slate-400 ml-1"
                    >{@html convertMinutesToHoursAndMinutes(
                        service.remainingMinutes,
                    )} remaining</span
                >{/if}
        </div>
    {/if}
</div>
