<?php
namespace NDISmate\Models\UserRole;

use \RedBeanPHP\R as R;

class CheckRoles
{
    public function __invoke($data)
    {
        $user = R::findOne('userroles', ' user_id=:user_id ', [':user_id' => $data['user_id']]);
        return (count(array_intersect($data['roles'], json_decode($user->roles, true))) > 0) ? true : false;
    }
}
