<?php
namespace NDISmate\Controllers;

use NDISmate\Models\Task;
use NDISmate\CORE\BaseController;
use NDISmate\Models\Task\GetTask;
use NDISmate\Models\Task\ListTasks;
use NDISmate\Models\Task\UpdateTask;
// use NDISmate\Services\TaskService\AddTask;

class TaskController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addTask' => [new Task, 'create', true, []],
            'updateTask' => [new Task, 'update', true, []],
            'listTasks' => [new ListTasks, null, true, []],
            'getTask' => [new GetTask, null, true, []],
            // 'archiveTask' => [new Task, 'archive', true, []],
            // 'restoreTask' => [new Task, 'restore', true, []],
        ];
    }
}
