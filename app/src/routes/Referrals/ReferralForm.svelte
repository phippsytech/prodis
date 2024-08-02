<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import Container from "@shared/Container.svelte";
    import Behaviour from "./Funding/Behaviour.svelte";
    import DevelopmentalEducator from "./Funding/DevelopmentalEducator.svelte";
    import LegoClub from "./Funding/LegoClub.svelte";
    import MinecraftTherapy from "./Funding/MinecraftTherapy.svelte";
    import OccupationalTherapy from "./Funding/OccupationalTherapy.svelte";
    import Physiotherapy from "./Funding/Physiotherapy.svelte";
    import SleepProgram from "./Funding/SleepProgram.svelte";
    import SocialWorker from "./Funding/SocialWorker.svelte";
    import SpeechTherapy from "./Funding/SpeechTherapy.svelte";
    import ToiletTraining from "./Funding/ToiletTraining.svelte";

    let referrer = {
        name: "",
        relationship: "",
        organization: "",
        date: "",
        phone: "",
        email: "",
        consent: false,
    };

    let client = {
        title: "",
        firstName: "",
        surname: "",
        preferredName: "",
        gender: "",
        pronoun: "",
        phone: "",
        email: "",
        interpreterRequired: false,
        interpreterLanguage: "",
        suburb: "",
        postCode: "",
        services: {
            positiveBehaviourSupports: {
                coreSuport: false,
                coreSupportfundingAmount: 0,
            },
        },
    };

    let ndisDetails = {
        participantNumber: "",
        dob: "",
        planStartDate: "",
        planEndDate: "",
        services: [],
        funding: {
            source: "",
            amount: "",
            managingFunds: "",
        },
    };

    let primaryContact = {
        title: "",
        relationship: "",
        firstName: "",
        surname: "",
        phone: "",
        email: "",
        interpreterRequired: false,
        interpreterLanguage: "",
        sameAddress: false,
        address: "",
    };

    let altContact = {
        title: "",
        relationship: "",
        firstName: "",
        surname: "",
        phone: "",
        email: "",
    };

    let serviceProvider = {};
    let ndisGoals = [];
    let additionalInfo = "";
</script>

<div
    class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular px-2 mt-2"
>
    Referral / Intake Form
</div>
<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">Referrer Details</h3>

<FloatingInput
    label="Referred by"
    bind:value={referrer.name}
    placeholder="eg: Alex Smith"
/>
<FloatingInput
    label="Relationship to client"
    placeholder="eg: Parent, Guardian, Support Coordinator, etc."
    bind:value={referrer.relationship}
/>
<FloatingInput
    label="Organisation"
    bind:value={referrer.organization}
    placeholder="(if relevant)"
/>
<!-- <FloatingDate label="Date" bind:value={referrer.date} /> -->

<FloatingInput
    label="Phone Number"
    bind:value={referrer.phone}
    placeholder="eg: XXXX XXX XXX"
/>

<FloatingInput
    label="Email"
    bind:value={referrer.email}
    placeholder="eg: example@example.com"
/>
<!-- 
<label>
    Do you consent to sharing this information with Crystel Care?
    <input type="checkbox" bind:checked={referrer.consent} />
</label> -->

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">Client Details</h3>
<div class="flex justify-between gap-x-2">
    <div class="flex-grow">
        <FloatingInput
            label="First Name"
            bind:value={client.firstName}
            placeholder="eg: Alex"
        />
    </div>

    <div class="flex-grow">
        <FloatingInput
            label="Surname"
            bind:value={client.surname}
            placeholder="eg: Smith"
        />
    </div>
</div>

<div class="grid grid-cols-2 gap-x-2" style="grid-template-columns: 6fr 2fr ;">
    <div class="flex-grow">
        <FloatingInput
            label="Preferred name"
            bind:value={client.preferredName}
            placeholder="eg: Lexie, Xander, etc."
        />
    </div>
    <div class="flex-grow-0">
        <FloatingDate label="Date of Birth" bind:value={ndisDetails.dob} />
    </div>
</div>
<div class="flex justify-between gap-x-2">
    <div class="flex-grow">
        <FloatingInput
            label="Gender"
            bind:value={client.gender}
            placeholder="eg: Female"
        />
    </div>

    <div class="flex-grow">
        <FloatingInput
            label="Pronoun"
            bind:value={client.pronoun}
            placeholder="eg: They/Them"
        />
    </div>
</div>
<FloatingInput
    label="Phone"
    bind:value={client.phone}
    placeholder="eg: XXXX XXX XXX"
/>
<FloatingInput
    label="Email"
    bind:value={client.email}
    placeholder="eg: example@example.com"
/>

<FloatingInput
    label="Residential Address"
    bind:value={client.address}
    placeholder="123 Example Street, Adelaide, SA 6000,"
/>

<div class="grid grid-cols-2 gap-x-2" style="grid-template-columns: 6fr 2fr ;">
    <div class="flex-grow">
        <FloatingInput
            label="Suburb"
            bind:value={client.suburb}
            placeholder="eg: Adelaide"
        />
    </div>

    <div class="flex-grow-0">
        <FloatingInput
            label="Post Code"
            bind:value={client.postCode}
            placeholder="eg: 6000"
        />
    </div>
</div>

<label class="flex mx-3 text-slate-600 text-sm items-center">
    <input
        type="checkbox"
        class="mr-1"
        bind:checked={client.interpreterRequired}
    />
    Client requires interpreter
</label>
{#if client.interpreterRequired}
    <FloatingInput
        label="Interpretation Required"
        bind:value={client.interpreterLanguage}
        placeholder="eg: Arabic, Mandarin, Vietnamese, Auslan etc."
    />
{/if}

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">NDIS Details</h3>

<FloatingInput
    label="Participant Number"
    bind:value={ndisDetails.participantNumber}
    placeholder="XXXXXXXXX"
/>

<div class="flex justify-between gap-x-2">
    <div class="flex-grow">
        <FloatingDate
            label="Plan Start Date"
            bind:value={ndisDetails.planStartDate}
        />
    </div>
    <div class="flex-grow">
        <FloatingDate
            label="Plan End Date"
            bind:value={ndisDetails.planEndDate}
        />
    </div>
</div>

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">
    Requested Services / Funding Details
</h3>

<ul
    class="bg-white rounded-lg border border-indigo-100 w-full text-slate-900 mb-4"
>
    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <Behaviour />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <DevelopmentalEducator />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <LegoClub />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <MinecraftTherapy />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <OccupationalTherapy />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <Physiotherapy />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <SleepProgram />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <SocialWorker />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
        <SpeechTherapy />
    </li>

    <li
        class=" flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 rounded-b-lg border-b border-indigo-100 w-full"
    >
        <ToiletTraining />
    </li>
</ul>

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">Primary Contact Details</h3>

<div class="flex justify-between gap-x-2">
    <div class="flex-grow">
        <FloatingInput
            label="First Name"
            bind:value={primaryContact.firstName}
            placeholder="eg: Alex"
        />
    </div>

    <div class="flex-grow">
        <FloatingInput
            label="Surname"
            bind:value={primaryContact.surname}
            placeholder="eg: Smith"
        />
    </div>
</div>

<FloatingInput
    label="Relationship to Client"
    placeholder="eg: Parent, Guardian, Support Coordinator, etc."
    bind:value={primaryContact.relationship}
/>

<FloatingInput
    label="Phone"
    bind:value={primaryContact.phone}
    placeholder="eg: XXXX XXX XXX"
/>
<FloatingInput
    label="Email"
    bind:value={primaryContact.email}
    placeholder="eg: example@example.com"
/>

<label class="flex mx-3 text-slate-600 text-sm items-center">
    <input
        type="checkbox"
        class="mr-1"
        bind:checked={primaryContact.interpreterRequired}
    />
    Requires interpreter
</label>
{#if primaryContact.interpreterRequired}
    <FloatingInput
        label="Interpretation Required"
        bind:value={primaryContact.interpreterLanguage}
        placeholder="eg: Arabic, Mandarin, Vietnamese, Auslan etc."
    />
{/if}

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">
    Alternative Contact Details
</h3>

<div class="flex justify-between gap-x-2">
    <div class="flex-grow">
        <FloatingInput
            label="First Name"
            bind:value={altContact.firstName}
            placeholder="eg: Alex"
        />
    </div>

    <div class="flex-grow">
        <FloatingInput
            label="Surname"
            bind:value={altContact.surname}
            placeholder="eg: Smith"
        />
    </div>
</div>

<FloatingInput
    label="Relationship to Client"
    placeholder="eg: Parent, Guardian, Support Coordinator, etc."
    bind:value={altContact.relationship}
/>

<FloatingInput
    label="Phone"
    bind:value={altContact.phone}
    placeholder="eg: XXXX XXX XXX"
/>
<FloatingInput
    label="Email"
    bind:value={altContact.email}
    placeholder="eg: example@example.com"
/>

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">NDIS Goals</h3>

<FloatingTextArea
    label="NDIS Goals"
    bind:value={serviceProvider.ndisGoals}
    placeholder="example:
1. I want to develop ...
2. I aim to complete ...
etc..."
/>

<h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">Additional Information</h3>
<FloatingTextArea
    label="Additional Information"
    bind:value={additionalInfo}
    placeholder="eg: has ample funding"
/>

By submitting the above information, you consent to sharing this information
with Crystel Care.

<!-- <button type="submit">Submit</button> -->
