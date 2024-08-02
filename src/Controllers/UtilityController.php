<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Utilities\GetUserType;

final class UtilityController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'getUserType' => [new GetUserType, null, true, []],
        ];
    }
}
