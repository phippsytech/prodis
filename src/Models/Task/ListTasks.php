<?php
namespace NDISmate\Models\Task;

use \RedBeanPHP\R as R;

class ListTasks
{
    public function __invoke()
    {
        $tasks = R::getAll(
            'SELECT tasks.*,
                users.name AS creator_name,
                staffs.name AS assigned_staff_name,
                users_assigned.name AS assigned_staff_user_name
             FROM tasks
             LEFT JOIN users ON tasks.user_id = users.id
             LEFT JOIN staffs ON tasks.assigned_to = staffs.id
             LEFT JOIN users AS users_assigned ON staffs.user_id = users_assigned.id
             ORDER BY tasks.created DESC'
        );

        return $tasks;
    }
}
