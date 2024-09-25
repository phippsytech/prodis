<?php
namespace NDISmate\Models\Participant\ServiceBooking;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use \RedBeanPHP\R as R;

class GetServiceBooking
{
    public function __invoke($data)
    {
        $bean = R::load('servicebookings', $data['id']);

        // $converter = new ConvertFieldsToBoolean();
        // $bean = $converter($bean, ['is_active', 'adjust_weekly_time','include_travel']);

        return $bean;
    }
}
