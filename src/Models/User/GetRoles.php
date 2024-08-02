<?php
namespace NDISmate\Models\User;

use \RedBeanPHP\R as R;

class GetRoles
{
    public function __invoke($data, $field, $guard)
    {
        // $guard->roles;  // This is the roles of the currently logged in user

        $user = R::findOne('users', ' id=:user_id ', [':user_id' => $data['user_id']]);

        if ($user !== null) {
            // should this be 'roles' => $guard->roles or 'roles' => $user->roles
            return json_decode($user->roles, true);
        } else {
            // Handle the case when $user is null
            return [];  // or any other appropriate default value
        }
    }
}
