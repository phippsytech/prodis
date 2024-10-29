<script>
  import { createEventDispatcher, onMount } from "svelte";
  
  let permissionStatus = "unknown"; // 'unknown', 'granted', 'denied', 'prompt'
  export let hasPermission;
  const dispatch = createEventDispatcher();

  // Check the status of the camera permission
  async function checkCameraPermission() {
    try {
      const permission = await navigator.permissions.query({ name: "camera" });

      permissionStatus = permission.state; // 'granted', 'denied', or 'prompt'
      dispatch("status", { status: permissionStatus });

      // Listen for changes in permission state
      permission.onchange = () => {
        permissionStatus = permission.state;
        dispatch("status", { status: permissionStatus });
      };

      hasPermission = permissionStatus === "granted";
    } catch (error) {
      console.error("Error checking camera permission:", error);
      permissionStatus = "denied";
      dispatch("status", { status: permissionStatus });
    }
  }

  // Request camera access directly
  async function requestCameraAccess() {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: true });
      stream.getTracks().forEach((track) => track.stop()); // Stop stream after checking
      permissionStatus = "granted";
      dispatch("status", { status: permissionStatus });
    } catch (error) {
      console.error("Camera access denied:", error);
      permissionStatus = "denied";
      dispatch("status", { status: permissionStatus });
    }
  }

  onMount(checkCameraPermission);
</script>

<div class="permission-status">
  {#if permissionStatus === 'unknown' || permissionStatus === 'prompt'}
    <p>Camera access is required. Please grant permission.</p>
    <button on:click={requestCameraAccess}>Grant Camera Access</button>
  {:else if permissionStatus === 'granted'}
    <p>Camera access granted ✅</p>
  {:else if permissionStatus === 'denied'}
    <p>Camera access denied ❌</p>
  {/if}
</div>

<style>
  .permission-status {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
  }

  button {
    padding: 0.5rem 1rem;
    border: none;
    background-color: #007bff;
    color: white;
    border-radius: 5px;
    cursor: pointer;
  }

  button:hover {
    background-color: #0056b3;
  }
</style>
