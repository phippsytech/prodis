<?php
namespace NDISmate\Models\UserRole;

use \RedBeanPHP\R as R;

class GetRoles
{
    public function __invoke($data)
    {

        $user = R::findOne('userroles', ' user_id=:user_id ', [':user_id' => $data['user_id']]);

        if ($user !== null) {
            return json_decode($user->roles, true);
        } else {
            // Handle the case when $user is null
            return [];  // or any other appropriate default value
        }
    }
}
