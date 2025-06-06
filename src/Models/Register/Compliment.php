<?php

namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Compliment extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('compliments'));
        $this->fields = [
            'date' => [v::dateTime('Y-m-d')],
            'complimenter' => [v::stringType()],
            'description' => [v::stringType()],
            'staffs_id' => [v::optional(v::numericVal())],
            'action_taken' => [v::optional(v::stringType())],
            'acknowledgement_date' => [v::optional(v::dateTime('Y-m-d'))],
            'status' => [v::stringType()],
            'user_id' => [v::numericVal()] // log user id on create
        ];
    }

    public function afterDispense()
    {
        // Set indexes after dispensing the bean
        R::exec("ALTER TABLE compliments ADD INDEX (staff_id)");
    }
}