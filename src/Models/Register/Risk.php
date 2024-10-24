<?php
namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Risk extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('risks'));
        $this->fields = [
            'staff_id' => [v::numericVal()],
            'date_identified' => [v::dateTime('Y-m-d')],
            'date_resolved' => [v::optional(v::dateTime('Y-m-d'))],
            'type' => [v::stringType()],
            'description' => [v::stringType()],
            'resolution' => [v::stringType()],
            'status' => [v::stringType()], // open, closed
            'user_id' => [v::numericVal()] // log user id on create
        ];
    }

    public function afterDispense()
    {
        R::exec("ALTER TABLE risks ADD INDEX (staff_id)");
    }
}