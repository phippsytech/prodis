<script>
    import { DocumentMagnifyingGlassIcon } from "@app/../node_modules/heroicons-svelte/dist/24/outline";
    import { XMarkIcon } from "@app/../node_modules/heroicons-svelte/dist/24/outline";

    export let value;
    export let name;
    export let extension;

    // let width
    // let height

    let size;
    // let name
    // let error=false;

    function clearFile() {
        value = extension = size = name = null;
    }

    function importData() {
        let input = document.createElement("input");
        input.type = "file";
        input.onchange = (_) => {
            // error = false;
            encodeImageFileAsURL(input.files[0]);
        };
        input.click();
    }

    // function handleOnError(){
    //     value=null
    //     error = "The file '"+name+"' does not contain a usable image."
    // }

    // https://stackoverflow.com/questions/15900485/correct-way-to-convert-size-in-bytes-to-kb-mb-gb-in-javascript
    function niceBytes(a) {
        let b = 0,
            c = parseInt(a, 10) || 0;
        for (; 1024 <= c && ++b; ) c /= 1024;
        return (
            c.toFixed(10 > c && 0 < b ? 1 : 0) +
            " " +
            ["bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"][b]
        );
    }

    //Author James Harrington 2014
    // src: https://stackoverflow.com/questions/6150289/how-to-convert-image-into-base64-string-using-javascript/28243128#28243128
    // Modified by Phippsy 10 Oct 2022
    function base64(file, callback) {
        var coolFile = {};

        function readerOnload(e) {
            var base64 = btoa(e.target.result);
            coolFile.base64 = base64;
            callback(coolFile);
        }

        var reader = new FileReader();
        reader.onload = readerOnload;

        size = niceBytes(file.size);
        name = file.name;
        extension = name.split(".").pop();

        reader.readAsBinaryString(file);
    }

    function encodeImageFileAsURL(file) {
        var fileReader = new FileReader();

        fileReader.onload = function (fileLoadedEvent) {
            var srcData = fileLoadedEvent.target.result; // <--- data: base64
            value = fileLoadedEvent.target.result;
            // image.src=value;
        };

        size = niceBytes(file.size);
        name = file.name;
        extension = name.split(".").pop();

        fileReader.readAsDataURL(file);
    }
</script>

{#if !value}
    <button
        on:click={() => importData()}
        type="button"
        class="inline-block px-6 py-2.5 bg-slate-200 text-slate-700 font-medium leading-tight rounded hover:bg-slate-300 focus:bg-slate-300 focus:outline-none focus:ring-0 active:bg-slate-400 transition duration-150 ease-in-out"
        ><DocumentMagnifyingGlassIcon class="w-5 h-5 inline" /> Choose file to upload</button
    >
{:else}
    <div class="mb-2">
        <div
            class="
                inline-block
                w-full
                px-3
                py-1.5
                text-base
                font-normal
                text-gray-700
                bg-white bg-clip-padding
                border border-solid border-gray-300
                rounded
                transition
                ease-in-out
                m-0
                focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none flex justify-between"
        >
            <div>
                <div class="opacity-70 text-xs">File</div>
                {name} <span class="italic text-sm opacity-50">{size}</span>
            </div>
            <div on:click={() => clearFile()}>
                <XMarkIcon class="w-5 h-5 m-2 cursor-pointer" />
            </div>
        </div>
    </div>
{/if}
