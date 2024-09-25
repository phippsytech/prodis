<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Training;

final class TrainingController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addTraining' => [new Training, 'create', true, []],
            'updateTraining' => [new Training, 'update', true, []],
        ];
    }
}