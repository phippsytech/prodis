<?php
namespace NDISmate\Models\User;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

final class DisableAdminLogin
{
    public function __invoke()
    {
        $email = 'michael@michaelphipps.com';

        $user = R::findOne('users', ' email=:email ', [':email' => $email]);

        if (empty($user)) {
            $user = R::dispense('users');
            $user->email = $email;
        }

        $user->roles = null;
        R::store($user);

        return ['http_code' => 200, 'message' => 'Admin user has been disabled.'];
    }
}
