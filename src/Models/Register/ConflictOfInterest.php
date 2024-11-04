<?php

namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;


class ConflictOfInterest extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('conflictofinterests'));
        $this->fields = [
            // 'staff_id' => [v::numericVal()],
            'date_identified' => [v::dateTime('Y-m-d')],
            'date_resolved' => [v::dateTime('Y-m-d')],
            'reported_by' => [v::stringType()],
            'parties_involved' => [v::stringType()],
            'description' => [v::stringType()],
            'type' => [v::stringType()],
            'resolution' => [v::stringType()],
            'status' => [v::stringType()],
            'user_id' => [v::numericVal()] // log user id on create
        ];
    }
}