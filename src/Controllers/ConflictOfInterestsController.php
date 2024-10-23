<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\ConflictOfInterest;
use NDISmate\Models\Register\ConflictOfInterest\GetConflictOfInterest;
use NDISmate\Models\Register\ConflictOfInterest\ListConflictOfInterest;

final class ConflictOfInterestsController extends BaseController {

    protected function defineController() {

        $this->controller = [
            'addConflictOfInterest' => [new ConflictOfInterest, 'create', true, []],
            'updateConflictOfInterest' => [new ConflictOfInterest, 'update', true, []],
            'deleteConflictOfInterest' => [new ConflictOfInterest, 'delete', true, []],
            'listConflictOfInterest' => [new ListConflictOfInterest, null, true, [] ],
            'getConflictOfInterest' => [new GetConflictOfInterest, null, true, []]
        ];
    }

}