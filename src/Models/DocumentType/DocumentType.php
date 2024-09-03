<?php

namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class DocumentType extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('documenttypes'));
        $this->fields = [
            'name' => [v::stringType()],  
            'description' => [v::optional(v::stringType())], 
            'is_required' => [v::boolType()],
        ];
    }
}
