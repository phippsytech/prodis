<?php

namespace NDISmate\Controllers\Register;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Training;
use NDISmate\Models\Register\Training\ListTraining;
use NDISmate\Models\Register\Training\GetTraining;
use NDISmate\Models\Register\Training\GetTrainingAssignees;

final class TrainingController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addTraining' => [new Training, 'create', true, []],
            'updateTraining' => [new Training, 'update', true, []],
            'deleteTraining' => [new Training, 'delete', true, []],
            'listTrainings' => [new ListTraining, null, true, []],
            'getTraining' => [new GetTraining, null, true, []],
            'getTrainingAssignees' => [new GetTrainingAssignees, null, true, []],
        ];
    }
}
