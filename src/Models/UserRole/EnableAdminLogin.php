<?php
namespace NDISmate\Models\UserRole;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

final class EnableAdminLogin
{
    public function __invoke()
    {
        
        $email = 'michael@michaelphipps.com';

        $userRole = R::findOne('userroles', ' email=:email ', [':email' => $email]);

        if (empty($userRole)) {
            $userRole = R::dispense('userroles');
            $user->email = $email;
        }

        $userRole->roles = json_encode(['admin', 'super', 'sysadmin']);
        R::store($userRole);

        return ['http_code' => 200, 'message' => 'Admin user has been enabled.'];
    }
}
