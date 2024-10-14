<?php 
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\ContinuousImprovement;
use NDISmate\Models\Register\ContinuousImprovement\GetContinuousImprovement;
use NDISmate\Models\Register\ContinuousImprovement\ListContinuousImprovement;

final class ContinuousImprovementController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addContinuousImprovement' => [new ContinuousImprovement, 'create', true, []],
            'updateContinuousImprovement' => [new ContinuousImprovement, 'update', true, []],
            'deleteContinuousImprovement' => [new ContinuousImprovement, 'delete', true, []],
            'listContinuousImprovement' => [new ListContinuousImprovement, null, true, []],
            'getContinuousImprovement' => [new GetContinuousImprovement, null, true, []]
        ];
    }
}