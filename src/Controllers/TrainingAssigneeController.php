<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Training\GetTrainingAssignees;

final class TrainingController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'getTrainingAssignees' => [new GetTrainingAssignees, null, true, []],
        ];
    }
}