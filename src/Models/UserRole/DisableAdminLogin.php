<?php
namespace NDISmate\Models\UserRole;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

final class DisableAdminLogin
{
    public function __invoke()
    {

        // TODO: this needs to be a user id, not email
        $email = 'michael@michaelphipps.com';

        $userRole = R::findOne('userroles', ' email=:email ', [':email' => $email]);

        if (empty($userRole)) {
            $userRole = R::dispense('userroles');
            $userRole->email = $email;
        }

        $userRole->roles = null;
        R::store($userRole);

        return ['http_code' => 200, 'message' => 'Admin user has been disabled.'];
    }
}
