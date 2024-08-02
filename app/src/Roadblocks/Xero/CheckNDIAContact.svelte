<script context="module">
    import CheckNDIAContact from "./CheckNDIAContact.svelte";
    import FloatingSelect from "../../PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import Button from "../../PhippsyTech/svelte-ui/Button.svelte";
    import { jspa } from "../../jspa.js";
    import { AppData } from "../../stores.js";

    let xero_contacts = [];
    let xeroContactList = [];
    let xero_ndia_contact_id = null;

    export function checkXeroNDIAContactRoadblock() {
        return new Promise(function (resolve, reject) {
            jspa("/Xero", "getNDIAContact", {})
                .then((result) => {
                    if (result.xero_contact_id) {
                        resolve(true);
                    } else {
                        reject(CheckNDIAContact);
                    }
                })
                .catch((error) => {
                    reject(CheckNDIAContact);
                });
        });
    }

    function getXeroContacts() {
        return new Promise(function (resolve, reject) {
            jspa("/Xero", "getContacts", {}).then((result) => {
                xeroContactList = []; // clear the xeroContactList
                xero_contacts = result.result;
                xero_contacts.forEach((contact) => {
                    let options = {
                        option: contact.Name,
                        value: contact.ContactID,
                        selected: false,
                    };
                    if (contact.ContactStatus == "ACTIVE")
                        xeroContactList.push(options);
                });
                xeroContactList = xeroContactList;
                resolve(xeroContactList);
            });
        });
    }

    function setXeroNDIAContact() {
        jspa("/Xero", "setNDIAContact", {
            xero_contact_id: xero_ndia_contact_id,
        }).then((result) => {
            document.location.reload();
        });
    }
</script>

<div class="flex items-center justify-center p-4" style="height:90vh">
    <div class="w-full" style="max-width:400px;">
        <div class="text-3xl mb-2">Set the Xero Contact for NDIA</div>
        <p class="mb-4">
            Before you can generate invoices for NDIA, you need to select the
            Xero Contact for NDIA.
        </p>

        {#await getXeroContacts()}
            Loading contacts from Xero...
        {:then xero_contacts}
            <FloatingSelect
                bind:value={xero_ndia_contact_id}
                label="Xero Contacts"
                instruction="Choose NDIA Contact"
                options={xero_contacts}
                hideValidation={true}
            />
            <div class="flex justify-between">
                <span></span>
                <Button
                    on:click={() => setXeroNDIAContact()}
                    label="Set NDIA Contact"
                />
            </div>
        {/await}
    </div>
</div>
