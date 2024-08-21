<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Service extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('services'));
        $this->fields = [
            'name' => [v::stringType()],  // eg Behaviour Management Plan
            'code' => [v::stringType()],  // eg BMP
            'billing_code' => [v::stringType()],  // eg 11_023_0110_7_3
            'billing_unit' => [v::stringType()],  // eg hour, day, week, each
            'rate' => [v::monetaryAmount()],  // unit rate for provision of service
            'location' => [v::stringType()],  // support item location (affects rate selection)
            // 'travel_allowance' => [v::stringType()],  // Max allowable travel for service (between 0 and 30 minutes)
            // 'record_travelled_kilometers' => [v::boolVal()],
            'budget_display' => [v::optional(v::stringType())],  // total, weekly
            'status' => [v::optional(v::stringType())] // current, expired
        ];
    }
}
