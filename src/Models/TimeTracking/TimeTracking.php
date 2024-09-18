<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class TimeTracking extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('timetrackings'));
        $this->fields = [
            'staff_id' => [v::optional(v::numericVal())],
            'client_id' => [v::optional(v::numericVal())],
            'service_id' => [v::optional(v::numericVal())],
            'service_booking_id' => [v::optional(v::numericVal())],
            'planmanager_id' => [v::numericVal()],
            'session_date' => [v::date('Y-m-d')],
            'claim_type' => [v::stringType()],
            'cancellation_reason' => [v::optional(v::stringType())],
            'session_duration' => [v::optional(v::numericVal())],  // in mins
            'start_time' => [v::optional(v::stringType())],
            'invoice_batch' => [v::optional(v::numericVal())],
            'trip_id' => [v::optional(v::numericVal())],  // if claim_type is TRAN this will contain the ID of the trip
            'unit_type' => [v::optional(v::stringType())],  // this could be 'min', 'km', 'day', etc
            'rate' => [v::optional(v::monetaryAmount())],  // unit rate for provision of service
            // 'actual_travel_time' => [v::optional(v::numericVal())],  // actual minutes travelled (not capped)
            // 'kilometers_travelled' => [v::optional(v::numericVal())],  //
        ];
    }
}
