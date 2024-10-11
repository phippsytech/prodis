<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\GetConflictOfInterests;

final class ConflictOfInterestsController extends BaseController {

    protected function defineController() {

        $this->controller = [
            'getConflictOfInterestsList' => [new GetConflictOfInterests, null, true, [] ]
        ];
    }

}