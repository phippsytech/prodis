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
        $assignee = R::load('users', $task->assignee_to);
        $task->assignee = $assignee;
        
        if (!$task) {
            throw new \Exception('Task Not Found', 404);
        } else {
            return $task;
        }
    }
}
