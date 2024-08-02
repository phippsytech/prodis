<script>

    import { onMount, onDestroy } from 'svelte';

    export let value= null
    export let instruction= 'Tap to take photo'

    let video= null
    let error = false

    let videoElement;
    let canvasElement;

    onMount(() => {
        initialiseCamera();
    });

    onDestroy(() => {
        // videoElement.srcObject.getTracks().forEach(track => track.stop());
    });
    


    function resetCamera(){
        value = null;
        valid = false;
        initialiseCamera();
    }

    function takePhoto () {
        let width = videoElement.videoWidth;
        let height = videoElement.videoHeight;
        canvasElement.width = width;
        canvasElement.height = height;
        let context = canvasElement.getContext('2d');
        context.drawImage(videoElement, 0, 0, width, height);
        let fullQuality = canvasElement.toDataURL('image/jpeg', 0.5);        
        // console.log(fullQuality);
        // conn.send(JSON.stringify({
        //     "event": "photo",
        //     "photo": fullQuality
        // })); 
        value = fullQuality;

    }

    


    function initialiseCamera(){
 
        var constraints = { 
            audio: false, 
            video: { 
                "width": { 
                    "min": 500, 
                    "ideal": window.innerWidth, 
                    "max": 500 },
                "height": { 
                    "min": 500, 
                    "ideal": window.innerHeight, 
                    "max": 500 
                },
                "facingMode": "environment"
            } 
        };

        navigator.mediaDevices.getUserMedia(constraints)
        .then(function(mediaStream) {
          
            videoElement.srcObject = mediaStream;
            video = videoElement;

        })
        .catch(function(err) { console.log(err.name + ": " + err.message); });

    }
    




</script>

<style>
        .field {
        padding: 5px;
    }
    
    .vert-center-text {
        position: absolute; 
        top: 50%;
        left: 0;
        text-align: center; 
        width: 100%; 
        text-shadow: 0 0 2px black; 
     }
     
     
      p { 
         color: white;
     }
     
     .vert-center-container {
       position:relative;
       
     }

    


</style>


{#if error}
    {error}
{:else}
    <div >
        <div class="field"><div class="row">
        <div class="col-xs-12 vert-center-container">
            <video bind:this={videoElement} on:click={()=>takePhoto()} style="width:100%;display:block;" class=" img-responsive" muted autoplay></video>
            <canvas on:click={()=>resetCamera()} bind:this={canvasElement} class="" style="height: auto; width: 100%; display:none;"></canvas>
            <div on:click={()=>takePhoto()} on:keydown={(event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    takePhoto();
                }
            }} role="button" tabindex="0" aria-label="Take photo"  class="vert-center-text" >
                <div class=""><p   ><i class="bi-camera"></i><br/>{instruction}</p></div>
            </div>
        </div>
        </div>
        </div>
        <span class="help-block" ></span>
    </div>
{/if}    