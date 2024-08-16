<?php

namespace NDISmate\Models\Staff;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Credential extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('staffcredentials'));
        $this->fields = [
            'staff_id' => [v::numericVal()],
            'credential_id' => [v::numericVal()],
            'details' => [v::stringType()],
            'vultr_storage_ref' => [v::stringType()],
            'credential_date' => [v::stringType()]
        ];
    }
}
