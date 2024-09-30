<script>
  import Container from "@shared/Container.svelte";
  import { slide } from "svelte/transition";
  import { PlusIcon, XMarkIcon } from "heroicons-svelte/24/outline";
  import Role from "@shared/Role.svelte";
  import NewServiceAgreementForm from "./NewServiceAgreementForm.svelte";
  import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
  import {
    EnvelopeIcon,
    PencilSquareIcon,
    ArchiveBoxIcon,
    DocumentTextIcon,
    DocumentArrowUpIcon,
    TrashIcon,
    UserIcon,
    ChevronDownIcon,
  } from "heroicons-svelte/24/outline";

  import FileSignatureIcon from "@shared/PhippsyTech/svelte-ui/icons/FileSignature.svelte";
  import FileContractIcon from "@shared/PhippsyTech/svelte-ui/icons/FileContract.svelte";
  import { CloudArrowUpIcon } from "heroicons-svelte/24/outline";
  import FileUploader from "@shared/FileUploader.svelte";

  export let props = {};

  let showGenerator = false;

  function toggleGenerator() {
    showGenerator = !showGenerator;
  }
</script>

<ul class=" bg-white rounded-xl border border-b w-full text-slate-800 mb-4">
  <li
    class=" group flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
  gap-x-2"
  >
    <div class="staff-member">
      <div class="flex text-sm inline">
        <!-- href="/#/signaturerequests/" -->
        <a
          class=" font-bold text-indigo-600 flex items-center hover:underline mr-1"
          href="https://api.prodis.phippsy.phippsy.tech/PDF"
          title="View Service Agreement awaiting signature"
          ><DocumentTextIcon class="h-4 w-4 inline mr-1" />Awaiting Signature: 9
          May 2024 to 29 Sept 2024</a
        > <span class="text-slate-400"> &mdash; sent 1 day ago</span>
      </div>
    </div>
    <div class="flex">
      <button
        class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
        title="Email Document"><EnvelopeIcon class="h-4 w-4" /></button
      >
      <!-- <span
        class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-slate-200 transition duration-300"
        title="Can't Email"><EnvelopeIcon class="h-4 w-4" /></span
      > -->

      <button
        class="flex p-1 mr-2 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
        title="Upload Signed Agreement"
        ><CloudArrowUpIcon class="h-4 w-4" /></button
      >

      <button
        type="button"
        class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
        title="Delete Document"
        ><XMarkIcon class="h-4 w-4 inline m-0 p-0" /></button
      >
    </div>
  </li>
</ul>
