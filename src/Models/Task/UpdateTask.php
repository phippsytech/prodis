<?php
namespace NDISmate\Models\Task;

use \RedBeanPHP\R as R;

class UpdateTask
{
    public function __invoke($data)
    {
        $task = (new Task)->update($data);

        if (!$task) {
            throw new \Exception('Task Not Found', 404);
        } else {
            return $task;
        }
    }
}
