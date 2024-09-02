<?php
namespace NDISmate\Models\Task;

use \RedBeanPHP\R as R;

class ListTasks
{
    public function __invoke($data)
    {
        $tasks = [];
        if (isset($data['staff_id'])) {
            $tasks = R::getAll(
                'SELECT tasks.*
                 FROM tasks
                 WHERE archived = 0 
                   AND assigned_to = :staff_id
                 ORDER BY 
                   CASE 
                     WHEN due_date IS NOT NULL AND due_date <= NOW() THEN 0  -- Overdue tasks first
                     WHEN due_date IS NOT NULL AND due_date > NOW() THEN 1   -- Future due_date tasks next
                     ELSE 2                                                 -- Tasks with NULL due_date last
                   END ASC,
                   due_date ASC,  -- Sort future tasks by due_date ascending
                   created DESC    -- Sort by created date as secondary sorting criterion
                ',
                [
                    ':staff_id' => $data['staff_id'],
                ]
            );
            
        } else {
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
        }
        
        

        return $tasks;
    }
}
