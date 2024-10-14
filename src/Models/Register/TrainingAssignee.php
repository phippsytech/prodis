<?php
namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Training extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('trainingassignees'));
        $this->fields = [
            'training_id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
        ];
    }

    public function afterDispense()
    {
        // Set indexes after dispensing the bean
        R::exec("ALTER TABLE trainingassignees ADD INDEX (staff_id)");
    }
}