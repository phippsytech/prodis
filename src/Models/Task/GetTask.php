<?php
namespace NDISmate\Models\Task;

use \RedBeanPHP\R as R;

class GetTask
{
    public function __invoke($data)
    {
        $task = R::load('tasks', $data['id']);
        
        if (!$task) {
            throw new \Exception('Task Not Found', 404);
        } else {
            return $task;
        }
    }
}
