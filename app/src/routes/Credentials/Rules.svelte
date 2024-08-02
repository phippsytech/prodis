<script>
    import { onMount } from "svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import Container from "@shared/Container.svelte";
    import Badge from "@shared/PhippsyTech/Tailwind/App/Elements/Badge.svelte";

    import Rule from "./Rules/Rule.svelte";
    import { jspa } from "@shared/jspa.js";

    let credentials = [];
    let credential_options = [];

    onMount(async () => {
        jspa("/Credential", "listCredentials", {}).then((result) => {
            credentials = result.result;

            credentials.sort(function (a, b) {
                const nameA = a.name.toUpperCase(); // ignore upper and lowercase
                const nameB = b.name.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });

            credentials.forEach((credential) => {
                let options = {
                    option: credential.name,
                    value: credential.id,
                };
                credential_options.push(options);
            });
        });
    });
</script>

<div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Rules
        </div>
        <p class=" text-sm text-gray-700">
            Manage the rules for required credentials.
        </p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
            on:click={() => push("/credentials/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add Rule</button
        >
    </div>
</div>

<Rule {credential_options} />

<select>
    <option>Any of the following</option>
    <option>All of the following</option>
    <option>Meet points requirement</option>
</select>
<div>
    Birth Certificate, citizenship certificate, or passport, ImmiCard (non
    citizens)
</div>

<Container>
    <div class="font-semibold">100 Points of ID</div>

    <select>
        <option>Meet points requirement</option>
    </select>
    <div>Number of points required</div>
    <div>Accepted credentials: Credential, point value</div>

    <div>Required for all staff in the organisation</div>

    <div>Primary identification documents (70 points each)</div>
    <div>
        Current AHRPA registration Birth certificate citizenship certificate
        current passport expired passport that was not cancelled and was current
        within the proceeding two years
    </div>
    <div>Secondary identification documents (40 points each)</div>
    <div>Australian Drivers licence</div>
    <div>Identification card for an Australian public Employee</div>
    <div>A state or territory issued personal identification card</div>
    <div>
        Student card issued by an Australian tertiary education institution
    </div>
</Container>

<Container>
    <div class="font-semibold">COVID-19 training</div>
    <div>evidence of completion</div>
</Container>

<Container>
    <div class="font-semibold">Working with children check</div>
    <div>???</div>
</Container>

<Container>
    <div class="font-semibold">NDIS Worker Screening</div>
    <div>???</div>
</Container>

<Container>
    <div class="font-semibold">NDIS Worker Orientation Module</div>
    <div>Certificate of Completion</div>
</Container>

<Container>
    <div class="font-semibold">Qualifications and experience</div>
    <div>???</div>
</Container>

<Container>
    <div class="font-semibold">Professional Body Membership</div>
    <div>???</div>
</Container>

<Container>
    <div class="font-semibold">First Aid Qualification</div>
    <div>which credentials</div>
</Container>

<Container>
    <div class="font-semibold">Employment Contract / Agreement</div>
    <div>Certificate of Completion</div>
</Container>

<Container>
    <div class="font-semibold">Professional Development Record</div>
    <div>
        To demonstrate ongoing professional development. A professional
        development plan is also advantageous
    </div>
</Container>

<Container>
    <div class="font-semibold">Insurances</div>
    <div>
        Currency certificates of all required insurance policies. If you have
        contractors in place, they require their own insurance, and you will
        need a copy on file.
    </div>
</Container>

<Container>
    <div class="font-semibold">
        Comprehensive Car Insurance and Registration
    </div>
    <div>
        Copies of registration and comprehensive car insurance policy for any
        staff that tranpsorts participants in ther car.
    </div>
</Container>

<Container>
    <div class="font-semibold">NDIS Worker Orientation Module</div>
    <div>Certificate of Completion</div>
</Container>

<Container>
    <div class="font-semibold">Signed Staff Orientation Checklist</div>
    <div>
        This document verifies that the employee underwent an orientation and
        has a thorough understanding of policies and procedures
    </div>
</Container>

<Container>
    <div class="font-semibold">Evidence of Staff Training</div>
    <div>
        Ensure you have evidence of any staff training completed, including
        in-house traning for: complaints management, indident management, risk
        management
    </div>
</Container>

<Container>
    <div class="font-semibold">Annual performance reviews</div>
    <div>
        Ensure you have evidence that staff undergo annual performance
        management reviews. the position description will be reviewed,
        performance discussed, and a traning needs analysis completed to direct
        the staff training plan.
    </div>
</Container>

<Container>
    <div class="font-semibold">Conflict of Interest</div>
    <div>
        If an employee has declared a conflict of interest, a completed conflict
        of interest declartion mnust be on file.
    </div>
</Container>

<Container>
    <div class="font-semibold">Privacy and Confidentiality Agreement</div>
    <div>
        Every employee must have a signed privacy and confidentiality agreement
        on file
    </div>
</Container>

<Container>
    <div class="font-semibold">Code of Conduct Agreement</div>
    <div>
        Every employee must have a signed code of conduct agreement on file.
    </div>
</Container>
