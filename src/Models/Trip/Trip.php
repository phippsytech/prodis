<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Trip extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('trips'));
        $this->fields = [
            'client_id' => [v::optional(v::numericVal())],
            'staff_id' => [v::numericVal()],
            'planmanager_id' => [v::optional(v::numericVal())],
            'service_id' => [v::optional(v::numericVal())],
            'service_booking_id' => [v::optional(v::numericVal())],
            'trip_date' => [v::stringType()],
            'vehicle_type' => [v::optional(v::stringType())],
            'do_not_bill' => [v::boolVal()],
            'kms' => [v::numericVal()],
            'trip_duration' => [v::numericVal()],
            'trip_purpose' => [v::optional(v::stringType())],
            'support_item_number' => [v::optional(v::stringType())],
            'trip_support_item_number' => [v::optional(v::stringType())],
            'status' => [v::optional(v::stringType())],
        ];
    }
}
