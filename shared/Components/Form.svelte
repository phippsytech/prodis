<script>
    export let api_url = "https://api.ndismate.app/";

    let feedback = {};
    let status = "ready"; //ready, sending, sent, error

    function send() {
        status = "sending";

        fetch(api_url + "Register/Feedback", {
            method: "POST",
            body: JSON.stringify({
                action: "addFeedback",
                data: feedback,
            }),
            headers: {
                "Content-Type": "application/json; charset=utf-8",
            },
            mode: "cors",
        })
            .then(async (response) => {
                response
                    .json()
                    .then((data) => {
                        if (response.status >= 200 && response.status < 300) {
                            status = "sent";
                            return;
                        } else {
                            data.status = response.status;
                            status = "error";
                            return;
                        }
                    })
                    .catch((error) => {
                        status = "error";
                        return;
                    });
            })
            .catch((error) => {
                status = "error";
                return;
            });
    }
</script>

<h1>Feedback</h1>
{#if status == "ready"}
    <div class="field">
        <select bind:value={feedback.type}>
            <option value="compliment">Compliment</option>
            <option value="complaint">Complaint</option>
        </select>
        <label>Feedback Type</label>
    </div>

    <div class="field">
        <input
            type="text"
            placeholder="eg: Jane Appleseed"
            bind:value={feedback.name}
        />
        <label>Name</label>
    </div>

    <div class="field">
        <input
            type="email"
            placeholder="eg: jane@example.com"
            bind:value={feedback.email}
        />
        <label>Email</label>
    </div>

    <div class="field">
        <input
            type="text"
            placeholder="eg: 04XX XXX XXX"
            bind:value={feedback.phone}
        />
        <label>Phone</label>
    </div>

    <div class="field">
        <textarea
            type="text"
            placeholder="eg: Thank you for your care"
            bind:value={feedback.message}
        ></textarea>
        <label>Message</label>
    </div>

    <div style="justify-content: right; display: flex;">
        <button on:click={() => send()}>Send</button>
    </div>
{/if}
{#if status == "sending"}
    <p>Sending...</p>
{/if}
{#if status == "sent"}
    <p>
        Thank you for your feedback. We will get back to you as soon as
        possible.
    </p>
{/if}
{#if status == "error"}
    <p>There was an error sending your feedback. Please try again later.</p>
{/if}

<style>
    input,
    textarea,
    select {
        -webkit-text-size-adjust: 100%;
        tab-size: 4;
        font-feature-settings: normal;
        box-sizing: border-box;
        font-family: inherit;
        margin: 0px;
        display: inline-block;
        width: 100%;
        border-radius: 0.25rem;
        border-width: 1px;
        border-style: solid;
        border-color: rgb(209 213 219 / 1);
        background-color: rgb(255 255 255 / 1);
        background-clip: padding-box;
        font-size: 1rem;
        font-weight: 400;
        color: rgb(55 65 81 / 1);
        height: calc(3.5rem + 2px);
        line-height: 1.25;
        padding: 0.5rem 0.75rem;
        padding-top: 1rem;
        padding-bottom: 0.15rem;
    }

    textarea {
        height: 150px;
        padding-top: 1.5rem;
        padding-bottom: 0.15rem;
    }

    select {
        padding-left: 0.5rem;
    }
    /**
    * Make the field a flex-container, reverse the order so label is on top.
    */
    .field {
        display: flex;
        flex-flow: column-reverse;
        margin-bottom: 0.5rem;
    }

    label {
        letter-spacing: 0.05em;
        transform-origin: left top;
        transform: translate(0.75rem, 1rem) scale(0.75);
        opacity: 0.5;
        line-height: 0;
    }

    button {
        -webkit-text-size-adjust: 100%;
        tab-size: 4;
        font-feature-settings: normal;
        box-sizing: border-box;
        border-width: 0;
        border-style: solid;
        border-color: #e5e7eb;
        font-family: inherit;
        margin: 0;
        cursor: pointer;
        background-image: none;
        display: inline-block;
        border-radius: 0.25rem;
        background-color: rgb(33 37 41 / 1);
        padding: 1rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 800;
        text-transform: uppercase;
        line-height: 1.375;
        color: rgb(255 255 255 / 1);
    }

    h1 {
        font-size: 1.5rem;
        font-weight: 500;
        line-height: 1.2;
        margin-bottom: 0.5rem;
        color: rgb(33 37 41 / 1);
        margin-top: 1.5rem;
    }
</style>
