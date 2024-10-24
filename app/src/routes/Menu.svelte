<script>
  import { jwt } from "@shared/stores.js";

  import {
    UserStore,
    StafferStore,
    RolesStore,
    BreadcrumbStore,
  } from "@shared/stores.js";
  import MenuClient from "./MenuClient.svelte";
  import MenuStaff from "./MenuStaff.svelte";
  import { toastError, toastSuccess } from "@shared/toastHelper.js";
  import Role from "@shared/Role.svelte";
  import ParticipantList from "./ParticipantList.svelte";
  import TaskList from "./Task/TaskList.svelte";
  import StaffKPIReport from "./StaffKPIReport.svelte";
  import DueReports from "@app/routes/Profile/Reports/Due.svelte";
  import ClientServiceAgreements from "@app/routes/Profile/Reports/ClientServiceAgreements.svelte";

  let version = "Development";
  let versionMetaTag = document.querySelector('meta[name="version"]');

  if (versionMetaTag) {
    version = versionMetaTag.getAttribute("content");
  }

  let welcome = [
    "Good to have you back.",
    "Nice to have you.",
    "Your visit means the world.",
    "You’re here!",
    "Back again?",
    "It’s always great to see you.",
    "Good to see you.",
    "Thanks for checking in.",
    "Welcome back.",
    "It’s nice to have you here.",
    "Thanks for being here.",
    "Glad to have you back.",
    "Thanks for stopping by.",
    "Your presence is appreciated.",
    "It’s always good to see you here.",
    "Thank you for checking in.",
    "Thanks for being involved.",
    "We’re glad to have you here.",
    "Thanks for your visit.",
    "Good to have you here.",
    "You’re right where you need to be.",
    "Glad you’re here.",
  ];

  let welcomeMessage = welcome[Math.floor(Math.random() * welcome.length)];

  let staffWelcome = [
    "Glad you’re back—things were getting suspiciously quiet.",
    "Back at it again.",
    "The place feels better with you here.",
    "Here to save the day? No cape needed.",
    "Ready to tackle the day? We’ve got snacks (maybe).",
    "Nice to have you back—let’s make some magic happen.",
    "Things just got 100% more interesting.",
    "Time to get things done—after coffee, of course.",
    "Always a pleasure having you!",
    "The day feels more productive already.",
    "Look who’s here! Now things can actually happen.",
    "Everything’s ready for you...",
    "Welcome back.",
    "Everything’s better with you, even Mondays.",
    "Let’s get things moving, but first... coffee?",
    "Nice to have you—our secret ingredient.",
    "Here’s to another great day, or at least a decent one.",
    "Your presence is much appreciated.",
    "Glad to see your name pop up—it’s like good news.",
    "Ready to make things happen?",
    "You’re the spark this day needed.",
    "Everything’s set, unless you forgot the coffee.",
    "You’re the one who’s always got the right idea.",
    "We’ve got this covered, mostly... well, sort of.",
    "Glad you’re here—someone’s got to keep things sane.",
    "Time to make today productive—if the Wi-Fi cooperates.",
    "Good to see you—now things can get back to normal.",
    "Everything’s lined up for you.",
    "You’re right where you need to be.",
    "Here to get things done—unless we’re out of coffee.",
    "Here’s to a solid day of trying to remember passwords.",
    "You’re the missing piece.",
    "Thanks for bringing the calm and coffee—you brought coffee, right?",
    "The day just got brighter—maybe even bearable.",
    "With you here, things might actually work.",
    "We didn’t panic too much while you were gone!",
    "Time to make things in order... or something like that.",
    "You make all of this seem way more organized.",
    "Thanks for being the calm in the storm.",
    "You logged in just in time—let’s do this.",
    "Thanks for showing up, now things seem possible.",
    "With you here, we might just make it through the day.",
    "Glad you’re here—now we can stop pretending.",
    "Time to get the ball rolling.",
    "With you here, today might just work out.",
    "We survived while you were gone, but it was touch-and-go.",
    "Time to show today who’s in charge (it’s you).",
    "Thanks for keeping everything from falling apart.",
  ];

  let staffWelcomeMessage =
    staffWelcome[Math.floor(Math.random() * staffWelcome.length)];

  function copyJWT() {
    navigator.clipboard
      .writeText($jwt)
      .then(function () {
        toastSuccess("JWT copied to clipboard");
      })
      .catch(function (error) {
        toastError("Failed to copy text: ", error);
      });
  }

  document.title = "Dashboard";
  BreadcrumbStore.set({
    path: [],
  });

  console.log($UserStore.id)
</script>

<!-- <div class="pt-2 px-4">
    <div class="text-xs text-slate-800">Welcome</div>
    <div
        class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
    >
        {$UserStore.name}
    </div>
</div> -->

<div class="text-indigo-600 bg-white rounded-md p-2 my-2 px-4">
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Hi {$UserStore.first_name}
  </div>

  <div class="text-slate-800 text-sm">
    <Role roles={["therapist", "house", "admin"]} conditional={true}>
      <div slot="authorised">
        {staffWelcomeMessage}
      </div>
      <div slot="declined">
        {welcomeMessage}
      </div>
    </Role>
  </div>
</div>

<Role roles={["house", "therapist", "admin"]}>
  <MenuStaff />
</Role>

<div
  class="flex flex-col md:flex-row justify-between mb-4 md:cols-1 lg:cols-3 xl:cols-4 gap-x-2"
  style="z-index: -3; position: relative;"
>
  <div class="md:w-1/2" style="z-index: -3; position: relative;">
    <Role roles={["stakeholder", "therapist", "house"]}>
      <ParticipantList />
    </Role>
    <Role roles={["sysadmin"]}>
      <TaskList />
    </Role>
  </div>
  <div class="md:w-1/2">
    <Role roles={["therapist"]}>
      <StaffKPIReport staff_id={$StafferStore.id} />
      <DueReports />
      <ClientServiceAgreements />
    </Role>
  </div>
</div>

<div
  class=" backdrop-blur m-4 rounded-lg mb-2 p-4 bg-white/25 text-xs text-gray-400 border border-gray-100 flex flex-col items-center justify-center"
>
  <svg
    class="h-4 w-auto inline text-gray-400"
    viewBox="0 0 49.465382 9.5649996"
    version="1.1"
    fill="currentColor"
  >
    <path
      d="M 3.1967,0.0092 C 1.4312,0.0092 1e-4,1.4403 0,3.2057 0.01,3.6569 0.076,4.122 0.2922,4.5121 1.4575,6.6117 3.6916,8.1644 5.4265,9.565 6.0025,9.1103 6.6316,8.6319 7.2578,8.1239 L 5.8542,6.7223 H 5.0031 A 0.92795142,0.92795142 0 0 1 4.1267,7.3493 0.92795142,0.92795142 0 0 1 3.1988,6.4214 0.92795142,0.92795142 0 0 1 4.1267,5.4935 0.92795142,0.92795142 0 0 1 4.9386,5.9739 l 1.0686,-0.01 v 1e-4 A 0.37448725,0.37448725 0 0 1 6.273,6.0734 L 7.8371,7.6353 C 7.9678,7.5236 8.0977,7.4106 8.226,7.2955 L 6.006,5.074 4.7093,5.084 c -0.098,0 -0.1927,-0.038 -0.263,-0.1065 L 3.2179,3.7779 c -0.01,-0.01 -0.01,-0.01 -0.01,-0.011 A 0.92795142,0.92795142 0 0 1 2.9159,3.8159 0.92795142,0.92795142 0 0 1 1.988,2.888 0.92795142,0.92795142 0 0 1 2.9159,1.96 a 0.92795142,0.92795142 0 0 1 0.928,0.928 0.92795142,0.92795142 0 0 1 -0.08,0.3757 l 1.0969,1.0715 1.2991,-0.01 c 0.1,0 0.1953,0.039 0.2657,0.1095 L 8.7721,6.7823 C 8.8851,6.6717 8.9979,6.561 9.1068,6.4471 L 7.6349,4.9729 A 0.37448725,0.37448725 0 0 1 7.5255,4.7072 l 0.01,-0.9719 A 0.92795142,0.92795142 0 0 1 7.0631,2.9284 0.92795142,0.92795142 0 0 1 7.991,2.0005 0.92795142,0.92795142 0 0 1 8.919,2.9284 0.92795142,0.92795142 0 0 1 8.2836,3.8074 V 4.5536 L 9.6171,5.8892 C 9.9837,5.4533 10.3122,4.9929 10.5817,4.503 10.7964,4.1127 10.8616,3.6469 10.862,3.1966 10.862,1.4311 9.4308,0 7.6653,0 6.8327,0 6.0332,0.3262 5.4369,0.9074 4.8399,0.3256 4.0393,0 3.2057,0 Z M 2.9162,2.4725 A 0.41910852,0.41910852 0 0 0 2.4972,2.8916 0.41910852,0.41910852 0 0 0 2.9162,3.3107 0.41910852,0.41910852 0 0 0 3.3354,2.8916 0.41910852,0.41910852 0 0 0 2.9162,2.4725 Z m 5.0685,0.046 A 0.41910852,0.41910852 0 0 0 7.5656,2.9375 0.41910852,0.41910852 0 0 0 7.9847,3.3567 0.41910852,0.41910852 0 0 0 8.4039,2.9375 0.41910852,0.41910852 0 0 0 7.9847,2.5185 Z M 4.1267,6.0053 A 0.41910852,0.41910852 0 0 0 3.7076,6.4243 0.41910852,0.41910852 0 0 0 4.1267,6.8436 0.41910852,0.41910852 0 0 0 4.5459,6.4243 0.41910852,0.41910852 0 0 0 4.1267,6.0053 Z"
    />
  </svg>
  <div class="  tracking-tight font-fredoka-one-regular">PRODIS</div>

  <div>
    {#if version == "Development"}<div
        class="cursor-pointer hover:text-indigo-600"
        on:click={copyJWT}
      >
        Development Version
      </div>
    {:else}<span class="font-medium">Last updated</span>&nbsp;{version}{/if}
  </div>
</div>
