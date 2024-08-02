<?php
namespace NDISmate\Models\User;

use \RedBeanPHP\R as R;

class CheckAdmin
{
    public function __invoke($data)
    {
        $user = R::findOne('users', ' id=:user_id ', [':user_id' => $data['user_id']]);
        return (count(array_intersect(['admin'], json_decode($user->roles, true))) > 0) ? true : false;
    }
}
