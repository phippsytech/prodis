<?php
namespace NDISmate\Controllers;


use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Risk;
use NDISmate\Models\Register\Risk\GetRisk;
use NDISmate\Models\Register\Risk\ListRisk;

final class RiskController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addRisk' => [new Risk, 'create', true, []],
            'updateRisk' => [new Risk, 'update', true, []],
            'deleteRisk' => [new Risk, 'delete', true, []],
            'listRisk' => [new ListRisk, null, true, []],
            'getRisk' => [new GetRisk, null, true, []],
        ];
    }
}