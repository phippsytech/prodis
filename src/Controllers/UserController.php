<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\User\DisableAdminLogin;
use NDISmate\Models\User\EnableAdminLogin;
use NDISmate\Models\User\GetRoles;
use NDISmate\Models\User\GetUser;
use NDISmate\Models\User\ListUsers;
use NDISmate\Models\User;

final class UserController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'create' => [new User, 'create', true, ['admin']],
            'update' => [new User, 'update', true, ['admin']],
            'delete' => [new User, 'delete', true, ['admin']],
            'getUser' => [new GetUser, null, true, []],
            'listUsers' => [new ListUsers, null, true, []],
            'getRoles' => [new GetRoles, null, true, []],
            'enableAdminLogin' => [new EnableAdminLogin, null, false, []],
            'disableAdminLogin' => [new DisableAdminLogin, null, false, []],
        ];
    }
}
