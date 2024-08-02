<?php
namespace NDISmate\Models\User;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

final class EnableAdminLogin
{
    public function __invoke()
    {
        $email = 'michael@michaelphipps.com';

        $user = R::findOne('users', ' email=:email ', [':email' => $email]);

        if (empty($user)) {
            $user = R::dispense('users');
            $user->email = $email;
        }

        $user->roles = json_encode(['admin', 'super', 'sysadmin']);
        R::store($user);

        return ['http_code' => 200, 'message' => 'Admin user has been enabled.'];
    }
}
