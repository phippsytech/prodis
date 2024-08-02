<script>
  import { onMount } from "svelte";
  import { websocketStore } from "./websocketStore.js";

  onMount(() => {
    websocketStore.connect();
  });

  // import { NotificationStore, PulseStore } from '@app/Overlays/stores.js';
  // import { websocketStore } from './websocketStore.js';

  // $: ws = $WebsocketStore;

  // function getRandomInt(min, max) {
  //     min = Math.ceil(min);
  //     max = Math.floor(max);
  //     return Math.floor(Math.random() * (max - min)) + min; //The maximum is exclusive and the minimum is inclusive
  // }

  // let hostname= window.location.hostname;
  // let websocket_server = "wss://ndismate.app:443"
  // if (hostname.includes("staging.phippsy.tech")) {
  //     websocket_server = "wss://ndismate.staging.phippsy.tech:443"
  // }

  // console.log(ws)
  // ws.addEventListener('open', function(event) {
  //     console.log('websocket open')
  // })

  // ws.addEventListener('close', function(event) {
  //     console.log('websocket closed')
  // })

  // ws.addEventListener('message', function(event) {
  //     const obj = JSON.parse(event.data)
  //     PulseStore.set({ pulse: obj });
  //     if (obj.action=="notification"){
  //         NotificationStore.set({ show: true, message: obj.data.message, title: obj.data.title, timeout: 10000, type: "success" });
  //     }
  // })
</script>
