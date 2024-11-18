<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Services\ServiceAgreementService\ListServiceAgreementsByStatus;


final class ServiceAgreementController extends BaseController  // Final prevents inheritance
{
    protected function defineController()
    {
        $this->controller = [
            'listServiceAgreementsByStatus' => [new ListServiceAgreementsByStatus, null, true, []],
        ];
    }
}
