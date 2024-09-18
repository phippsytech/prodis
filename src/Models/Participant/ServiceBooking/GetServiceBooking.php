<?php
namespace NDISmate\Models\Participant\ServiceBooking;

use \RedBeanPHP\R as R;

class GetServiceBooking
{
    public function __invoke($data)
    {
        $bean = R::load('servicebookings', $data['id']);
        return $bean;
    }
}
