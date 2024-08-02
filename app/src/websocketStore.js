// websocketStore.js
import { writable, derived } from 'svelte/store';
import { NotificationStore } from '@app/Overlays/stores.js';
import { VersionStore } from '@shared/stores.js';
import RobustWebSocket from 'robust-websocket';

function createWebSocketStore() {
    const { subscribe, set, update } = writable(null);
    let isConnected = writable(false);

    // Create a new writable store for WebSocket messages
    const messageStore = writable(null);

    // Create a derived store that emits a value every time setOnMessage is called
    const onMessage = derived(messageStore, $messageStore => $messageStore, null);

    function connect() {
        // Determine the appropriate WebSocket server URL
        let hostname = window.location.hostname;
        let websocket_server = "wss://ndismate.app:443";
        if (hostname.includes("phippsy.tech")) {
            websocket_server = "wss://prodis.phippsy.phippsy.tech:443";
        }

        // Create a new RobustWebSocket instance
        const ws = new RobustWebSocket(websocket_server, null, {
            timeout: 4000,
            shouldReconnect: function (event, ws) {
                if (event.code === 1008 || event.code === 1011) return;
                return getRandomInt(1000, 4000) + Math.pow(1.5, ws.attempts) * 500;
            },
            automaticOpen: true,
            ignoreConnectivityEvents: false
        });

        // Set up event listeners
        ws.addEventListener('open', function (event) {
            isConnected.set(true);
        });
        ws.addEventListener('close', function (event) {
            isConnected.set(false);
        });

        ws.addEventListener('message', function (event) {
            // this will send websocket messages to any component that is subscribed to the onMessage store
            messageStore.set(event.data);

            const obj = JSON.parse(event.data)

            if (obj.action == "setDeviceId") {
                localStorage.setItem('device_id', obj.data.device_id);
            }

            if (obj.action == "version_updated") {
                VersionStore.set({ updated: true });
            }

            if (obj.action == "notification") {
                NotificationStore.set({ show: true, message: obj.data.message, title: obj.data.title, timeout: 10000, type: "success" });
            }
        })

        set(ws);
    }

    function send(message) {
        update(ws => {
            ws.send(message);
            return ws;
        });
    }

    return {
        subscribe,
        connect,
        send,
        isConnected: { subscribe: isConnected.subscribe },
        onMessage,
    };
}

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min;
}

export const websocketStore = createWebSocketStore();

export function manageSubscription(channel, action) {
    const device_id = localStorage.getItem("device_id");
    const json = {
        action: action,
        device: device_id,
        channel: channel,
    };
    websocketStore.send(JSON.stringify(json));
}

