<?php
namespace NDISmate\Services\TripService;

use NDISmate\Models\Trip;
use NDISmate\Services\TripService\Billing as TripBilling;

class DeleteTrip
{
    function __invoke($data)
    {
        (new TripBilling)->delete($data);
        (new Trip)->delete($data);

        return;  // 204
    }
}
