<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Service\GetService;
use NDISmate\Models\Service\ListServices;
use NDISmate\Models\Service;

final class ServiceController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addService' => [new Service, 'create', false, ['admin']],
            'updateService' => [new Service, 'update', false, ['admin']],
            'listServices' => [new ListServices, null, false, []],
            'getService' => [new GetService, null, false, []],
        ];
    }
}
