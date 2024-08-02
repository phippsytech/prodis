<script>
    import { jspa } from "@shared/jspa.js";
    import ProgressBar from "@shared/PhippsyTech/svelte-ui/ProgressBar.svelte";
    import { onMount, onDestroy } from "svelte";
    import { websocketStore, manageSubscription } from "@app/websocketStore.js";
    import {
        convertMicrosoftDate,
        formatDate,
        formatCurrency,
    } from "@shared/utilities.js";
    import { fly } from "svelte/transition";

    export let payrun;
    export let staff_id;

    let generator = {
        status: "idle",
        progress: 0,
        count: 0,
    };

    let ready = false;

    let unsubscribeFromIsConnected;
    let unsubscribeFromOnMessage;

    onMount(() => {
        unsubscribeFromIsConnected = websocketStore.isConnected.subscribe(
            (connected) => {
                if (connected) {
                    unsubscribeFromOnMessage =
                        websocketStore.onMessage.subscribe((value) => {
                            try {
                                let obj = JSON.parse(value);
                                if (obj) {
                                    if (obj.action == "updateGeneratorStatus") {
                                        generator = obj.data;
                                    }
                                }
                            } catch (error) {
                                console.error("Error parsing JSON:", error);
                            }
                        });
                    manageSubscription("payslip_generator", "subscribe");
                    jspa("/Payroll/Payrun/Payslip", "getGeneratorStatus", {})
                        .then((result) => {
                            generator = result.result;
                        })
                        .finally(() => {});
                    ready = true;
                }
            },
        );
    });

    onDestroy(() => {
        manageSubscription("payslip_generator", "unsubscribe");
        unsubscribeFromIsConnected && unsubscribeFromIsConnected();
        unsubscribeFromOnMessage && unsubscribeFromOnMessage();
    });

    function generatePayrun() {
        generator = {
            status: "processing",
            progress: 0,
            count: 0,
        };

        let data = {
            payrun_id: payrun.PayRunID,
            start_date: convertMicrosoftDate(payrun.PayRunPeriodStartDate),
            end_date: convertMicrosoftDate(payrun.PayRunPeriodEndDate),
        };

        if (staff_id) {
            data.staff_id = staff_id;
        }

        jspa("/Payroll/Payrun", "doPayrun", {
            payrun_id: payrun.PayRunID,
            // staff_id:70, // Dean Ridley (just one staff for testing),
            start_date: convertMicrosoftDate(payrun.PayRunPeriodStartDate),
            end_date: convertMicrosoftDate(payrun.PayRunPeriodEndDate),
        })
            .then((result) => {
                generator = {
                    status: "processing",
                    progress: 0,
                    count: 0,
                };
            })
            .catch((error) => {
                generator = {
                    status: "idle",
                    progress: 0,
                    count: 0,
                };
            });
    }

    function calculateProgress(generator) {
        let progress = 0;
        if (generator.count > 0) {
            progress = parseFloat(
                ((generator.processed / generator.count) * 100).toFixed(2),
            );
        }

        return progress;
    }

    $: generator_progress = calculateProgress(generator);
</script>

{#if ready}
    {#if generator.status === "idle"}
        <button
            in:fly={{ y: -10, duration: 200, delay: 200, opacity: 0 }}
            out:fly={{ y: -10, duration: 200, opacity: 0 }}
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
            on:click={() => generatePayrun()}
            >{#if payrun.Wages > 0}Regenerate{:else}Generate{/if} Pay Run</button
        >
    {/if}

    {#if generator.status === "processing"}
        <div
            in:fly={{ y: 10, duration: 200, delay: 200, opacity: 0 }}
            out:fly={{ y: 10, duration: 200, opacity: 0 }}
        >
            <div class="mt-2 mb-0 text-lg font-medium">Generating Payslips</div>
            <p class="mt-0 mb-2 text-sm text-gray-700">
                This takes a few minutes. You can continue using the app - this
                process will not be interrupted. <!--We'll alert you when done.-->
            </p>

            <ProgressBar value={generator_progress} />
        </div>
    {/if}
{/if}
