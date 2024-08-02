<?php
namespace NDISmate\Models\User;

use \RedBeanPHP\R as R;

class ListUsers
{
    public function __invoke($data, $admin = false)
    {
        $lastSeen = R::getAll('SELECT 
                user_id,
                MAX(created) AS last_created_date
            FROM 
                apilogs
            GROUP BY 
                user_id');

        $users = R::getAll('SELECT users.*  FROM users');

        foreach ($users as $key => $user) {
            $users[$key]['last_seen'] = null;
            foreach ($lastSeen as $seen) {
                if ($seen['user_id'] == $user['id']) {
                    $users[$key]['last_seen'] = $seen['last_created_date'];
                    break;
                }
            }
        }

        if (!count($users)) {
            throw new \Exception('No User Records Found', 404);
        }
        return $users;
    }
}
