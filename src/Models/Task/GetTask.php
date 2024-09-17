<?php
namespace NDISmate\Models\Task;

use \RedBeanPHP\R as R;

class GetTask
{
    public function __invoke($data)
    {
        $task = R::load('tasks', $data['id']);

        // creator
        $creator = R::load('users', $task->user_id);
        $task->creator = $creator;

        // assignee
        $staff = R::load('staffs', $task->assigned_to);
        $assignee = R::load('users', $staff->user_id);
        $task->assignee = $assignee;
        
        if (!$task) {
            throw new \Exception('Task Not Found', 404);
        } else {
            return $task;
        }
    }
}
