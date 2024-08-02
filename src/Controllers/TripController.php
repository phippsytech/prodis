<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Trip\GetTrip;
use NDISmate\Models\Trip\ListTrips;
use NDISmate\Models\Trip\ListTripsByDate;
use NDISmate\Services\TripService\AddTrip;
use NDISmate\Services\TripService\DeleteTrip;
use NDISmate\Services\TripService\UpdateTrip;

final class TripController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'getTrip' => [new GetTrip, null, true, []],
            'listTrips' => [new ListTrips, null, true, []],
            'listTripsByDate' => [new ListTripsByDate, null, false, []],
            'addTrip' => [new AddTrip, null, true, []],
            'updateTrip' => [new UpdateTrip, null, true, []],
            'deleteTrip' => [new DeleteTrip, null, true, []],
        ];
    }
}
