<script>
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import TaskForm from "./TaskForm.svelte";

    // Page metadata
    document.title = "Add Task";
    BreadcrumbStore.set({
        path: [{ url: "/tasks", name: "Tasks" }, { name: "Add Task" }]
    });

    let task = {        
        title:"",
        description:""
    };

</script>

<Container>
    <div class="text-2xl mb-2 tracking-tight font-fredoka-one-regular text-indigo-900">
        Add Task
    </div>

    <TaskForm bind:task />

</Container>