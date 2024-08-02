<?php
namespace NDISmate;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class APILog extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('apilogs'));
        $this->fields = [
            'user_id' => [v::optional(v::numericVal())],
            'client_id' => [v::optional(v::numericVal())],
            'uri' => [v::optional(v::stringType())],
            'action' => [v::optional(v::stringType())],
            'method' => [v::optional(v::stringType())],
            'data' => [v::optional(v::arrayType())],
            'ip' => [v::optional(v::stringType())],
        ];
    }
}
