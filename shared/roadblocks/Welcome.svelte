<script context="module">
    import Welcome from "./Welcome.svelte";
    import NotificationsList from "../NotificationsList.svelte";
    import { jspa } from "../jspa.js";
    import { StafferStore, AppData } from "../stores.js";

    let date = new Date();
    let current_date = date.toISOString().slice(0, 10);

    function setKeyValue(key, value) {
        jspa("/App", "setKeyValue", { key: key, value: value });
    }

    function getKeyValue(key) {
        jspa("/App", "getKeyValue", { key: key }).then((result) => {
            return result;
        });
    }

    // let welcomed = getKeyValue('welcomed').result;

    export function checkWelcomeRoadblock() {
        return new Promise(function (resolve, reject) {
            let appData = {};

            AppData.subscribe((data) => {
                appData = JSON.parse(data);
            });

            if (appData.welcomed === current_date) {
                resolve(true);
            } else {
                reject(Welcome);
            }
        });
    }

    function authenticate() {
        // setKeyValue('welcomed', 1);
        // welcomed = 1

        // appData.welcomed=true;
        // localStorage.setItem("AppData",JSON.stringify(appData));

        AppData.update((currentData) => {
            let newData = JSON.parse(currentData);
            newData["welcomed"] = current_date;
            return JSON.stringify(newData);
        });
    }

    function firstWord(str) {
        if (!str) return null;
        return str.split(" ")[0];
    }

    function greeting() {
        // return a random greeting
        let greetings = [
            "Hi",
            "Hello",
            "Greetings",
            "Welcome",
            "G'day",
            "Bonjour",
            "Hola",
            "Namaste",
            "Aloha",
        ];
        return greetings[Math.floor(Math.random() * greetings.length)];
    }
</script>

<div class="flex items-center justify-center p-4">
    <div class="w-full" style="max-width:400px;">
        <div class="text-2xl mb-2">
            {greeting()}
            {firstWord($StafferStore.name)}
        </div>

        <NotificationsList />

        <div
            class="border-t border-b border-slate-300/50 bg-slate-200/50 text-xs text-gray-500 mb-4 p-2"
        >
            <b>Tip:</b> You can check your notifications any time. Just select the
            notifications icon in the menu.
        </div>

        <div class="flex justify-between">
            <span></span>
            <button
                on:click={() => authenticate()}
                type="button"
                class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
            >
                Continue
            </button>
        </div>
    </div>
</div>
