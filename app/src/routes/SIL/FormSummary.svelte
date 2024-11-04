<script>
  import Container from "@shared/Container.svelte";
  import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { formatDate } from "@shared/utilities.js";
  import ExpandIndicator from "@app/Layout/ExpandIndicator.svelte";

  BreadcrumbStore.set({
    path: [{ url: null, name: "SIL" }],
  });

  let participants = [
    {
      name: "test",
      dates: [
        {
          date: "2023-01-01",
          reports: [
            {
              report_type: "test",
              count: 12,
            },
          ],
        },
      ],
    },
  ];

  jspa("/SIL/House/Form", "getFormSummary", {}).then((result) => {
    participants = result.result;

    participants.sort(function (a, b) {
      if (a.participant_name == null) return -1;
      if (b.participant_name == null) return 1;
      const nameA = a.participant_name.toUpperCase(); // ignore upper and lowercase
      const nameB = b.participant_name.toUpperCase(); // ignore upper and lowercase
      if (nameA < nameB) return -1;
      if (nameA > nameB) return 1;
      return 0; // names must be equal
    });
    participants = participants;
    // push('/clients/'+client.id);
  });
</script>

<Container>
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    SIL Form Summary
  </div>

  {#each participants as participant}
    <div class="mb-4">
      <div
        on:click={() => (participant.expanded = !participant.expanded)}
        class="font-bold text-xl cursor-pointer hover:text-indigo-600"
      >
        {participant.participant_name}
        <ExpandIndicator
          expanded={participant.expanded}
          class="inline h-5 w-5 flex-none text-gray-400 shrink-0"
        />
      </div>

      {#if participant.expanded}
        {#if participant.dates}
          {#each participant.dates as date}
            {#if date.date}
              <div
                on:click={() => (date.expanded = !date.expanded)}
                class="font-semibold ml-2 cursor-pointer hover:text-indigo-600"
              >
                {formatDate(date.date)}
                <ExpandIndicator
                  expanded={date.expanded}
                  class="inline h-5 w-5 flex-none text-gray-400 shrink-0"
                />
              </div>
              {#if date.expanded}
                <div class="mb-2">
                  {#each date.reports as report}
                    <div class="ml-4">
                      {report.report_type} ({report.count})
                    </div>
                  {/each}
                </div>
              {/if}
            {/if}
          {/each}
        {/if}
      {/if}
    </div>
  {/each}
</Container>
