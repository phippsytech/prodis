<?php
namespace NDISmate\Services\TaskService;

use NDISmate\Models\Task;

class AddTrip
{
    function __invoke($data)
    {
        // Create the trip
        $task = (new Task)->create($data);

        return;  // 201
    }
}
