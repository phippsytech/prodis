<?php
namespace NDISmate\Models\User;

use \NDISmate\CORE\TransactionalDatabase;
use \NDISmate\CORE\Validation;
use \RedBeanPHP\R as R;

class GetUser
{
    public function __invoke($data, $fields, $guard)
    {
        if (!isset($data['user_id'])) {
            $user_id = $guard->user_id;
        } else {
            $user_id = $data['user_id'];
        }
        

        // Do get single user
        $user = R::load('users', $user_id);

        if (!$user) {
            throw new \Exception('User Not Found', 404);
        } else {
            $user = $user->export();

            // Get user roles
            // $user['roles'] = json_decode((new \NDISMate\Models\UserRole\GetRoles)(["user_id" => $user_id]), true);

            $user['roles'] = (new \NDISMate\Models\UserRole\GetRoles)(["user_id" => $user_id]);

            return $user;
        }
    }
}
