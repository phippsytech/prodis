<?php
namespace NDISmate\Models\Task;

use \RedBeanPHP\R as R;

class ListTasks
{
    public function __invoke()
    {
        $beans = R::getAll(
            'SELECT * FROM tasks 
            '
        );
        return $beans;
    }
}
