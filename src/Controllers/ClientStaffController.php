<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Client\Staff\CheckAccess;
use NDISmate\Models\Client\Staff\ListClientStaffByClientId;
use NDISmate\Models\Client\Staff\ListStaffClientsByStaffId;
use NDISmate\Models\Client\Staff as ClientStaffAssignment;

final class ClientStaffController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addClientStaffer' => [new ClientStaffAssignment, 'create', true, ['admin']],
            'deleteClientStaffer' => [new ClientStaffAssignment, 'delete', true, ['admin']],
            'makePrimary' => [new ClientStaffAssignment, 'makePrimary', true, ['admin']],
            'removePrimary' => [new ClientStaffAssignment, 'removePrimary', true, ['admin']],
            'listClientStaffByClientId' => [new ListClientStaffByClientId, null, false, []],
            'listStaffClientsByStaffId' => [new ListStaffClientsByStaffId, null, false, []],
            'checkAccess' => [new CheckAccess, null, true, []],
        ];
    }
}
