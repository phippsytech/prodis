<script>
  import { createEventDispatcher, onMount, onDestroy } from "svelte";
  import { XMarkIcon, ArrowRightIcon } from "heroicons-svelte/24/outline";

  export let width = 340;
  export let autoOpen = true;
  export let mirrorDisplay = true;
  export let useAudio = true;
  export let needFrames = false;

  let ratio, height;
  let isOpened = false;
  let videoRef, canvasRef, ctx, stream;
  let cameras = [];
  let currentCameraIndex = 0;
  let imageData = null;
  let snapshotAnimation = false;

  const dispatch = createEventDispatcher();

  $: adjustResolution(width);

  export const captureImage = async (isImageData = false) => {
    if (ctx && canvasRef && isOpened) {
      imageData = isImageData
        ? ctx.getImageData(0, 0, width, height)
        : canvasRef.toDataURL("image/png");
      dispatch("image", imageData);

      // Trigger the snapshot animation
      snapshotAnimation = true;
      setTimeout(() => (snapshotAnimation = false), 500); // Reset animation after 500ms

      return imageData;
    }
    console.error("Camera is not open or context is not ready.");
    return null;
  };

  export const open = () => canvasRef && openCamera();
  export const close = () => canvasRef && closeCamera();
  export const resetCamera = () => {
    imageData = null;
    dispatch("image", null);
  };

  export const switchCamera = async () => {
    if (cameras.length > 1) {
      closeCamera();
      currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
      await openCamera(cameras[currentCameraIndex].deviceId);
    } else {
      console.error("No other camera device available.");
    }
  };
  
  const getCameraDevices = async () => {
    try {
      const devices = await navigator.mediaDevices.enumerateDevices();
      cameras = devices.filter(device => device.kind === "videoinput");
      dispatch("init", cameras);
      return cameras;
    } catch (error) {
      console.error("Failed to get devices:", error);
    }
  };

  const initCamera = async () => {
    ctx = canvasRef.getContext("2d", { willReadFrequently: true });

    videoRef.addEventListener("canplay", () => {
      ratio = videoRef.videoHeight / videoRef.videoWidth;
      adjustResolution(width);
      isOpened = true;
      drawCanvas();
    });

    const devices = await getCameraDevices();

    if (devices.length) {
      // Find the rear-facing or environment camera
      const mainCamera = devices.find(device =>
        device.label.toLowerCase().includes('back') ||
        device.label.toLowerCase().includes('environment')
      );

      // Determine which camera to open
      const cameraToOpen = mainCamera || devices[0];

      // Update currentCameraIndex based on the selected camera
      currentCameraIndex = devices.findIndex(
        device => device.deviceId === cameraToOpen.deviceId
      );

      // Open the selected camera
      await openCamera(cameraToOpen.deviceId);
    }
  };

  const openCamera = async (deviceId) => {
    try {
      const constraints = { video: { deviceId } };
      stream = await navigator.mediaDevices.getUserMedia(constraints);
      videoRef.srcObject = stream;
      videoRef.play();

      // Wait briefly, then fetch the list of devices again
      setTimeout(async () => {
        const devices = await getCameraDevices();
        console.log("Updated devices list:", devices); // Optional: log for testing
      }, 500); // Adjust delay if needed
    } catch (error) {
      console.error("Failed to open camera:", error);
    }
  };


  const closeCamera = () => {
    if (stream) stream.getTracks().forEach(track => track.stop());
    isOpened = false;
  };

  const adjustResolution = (width) => (height = width * ratio);
  const drawCanvas = () => {
    if (isOpened) {
      ctx.clearRect(0, 0, width, height);
      ctx.drawImage(videoRef, 0, 0, width, height);
      requestAnimationFrame(drawCanvas);
    }
  };

  onMount(initCamera);

  onDestroy(() => {
      closeCamera();
  });
</script>

<div class="camera-wrap shadow-lg" style="max-width: {width}px;">
  <div class="camera-container relative w-full">
    <video bind:this={videoRef} muted autoplay playsinline style="display: none;" />
    <canvas class="w-full" bind:this={canvasRef} {height} style="{imageData ? 'display: none;' : ''}" />
    {#if imageData}
      <img
        class="w-full"
        src={imageData}
        alt="Captured Image"
        class:snapshot-animation={snapshotAnimation}
      />
    {/if}
  </div>

  <div class="camera-controls flex gap-2 justify-between items-center p-2">
    <div class="w-10 h-10"></div>
    <div>
      {#if !imageData}
        <div class="w-10 h-10 rounded-full bg-blue-600 opacity-80 border-2 border-white shadow-lg cursor-pointer" on:click={() => captureImage(false)} />
      {:else}
        <div class="w-10 h-10 rounded-full border-2 border-white shadow-lg flex items-center justify-center cursor-pointer" on:click={resetCamera}>
          <XMarkIcon class="w-8 h-8 duration-200 ease-in-out transform hover:rotate-90" />
        </div>
      {/if}
    </div>
    <div class="w-10 h-10">
      {#if cameras.length > 1}
        {#if !imageData}
          <div class="cursor-pointer w-10 h-10" on:click={switchCamera}>
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#000">
              <path d="M21 6h-1.5a.5.5 0 0 1-.5-.5A1.502 1.502 0 0 0 17.5 4h-6A1.502 1.502 0 0 0 10 5.5a.5.5 0 0 1-.5.5H8V5H4v1H3a2.002 2.002 0 0 0-2 2v10a2.002 2.002 0 0 0 2 2h18a2.002 2.002 0 0 0 2-2V8a2.002 2.002 0 0 0-2-2zM8.2 13h-1a4.796 4.796 0 0 1 8.8-2.644V9h1v3h-3v-1h1.217A3.79 3.79 0 0 0 8.2 13zm7.6 0h1A4.796 4.796 0 0 1 8 15.644V17H7v-3h3v1H8.783a3.79 3.79 0 0 0 7.017-2z"/>
            </svg>
          </div>
        {/if}
      {/if}
    </div>
  </div>
</div>

<style>
  .snapshot-animation {
    animation: flash 0.5s ease-in-out;
  }

  @keyframes flash {
    0% { opacity: 0; }
    50% { opacity: 1; }
    100% { opacity: 0; }
  }
</style>