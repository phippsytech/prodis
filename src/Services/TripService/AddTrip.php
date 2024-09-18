<?php
namespace NDISmate\Services\TripService;

use NDISmate\Models\Trip;
use NDISmate\Services\TripService\Billing as TripBilling;
use NDISmate\Services\TripService\Utilities;

class AddTrip
{
    function __invoke($data)
    {
        // Create the trip
        $trip = (new Trip)->create($data);

        // we don't need to bill this trip, so exit here
        if ($data['do_not_bill']) {
            return;  // 201
        }

        $data = Utilities::getAdditionalData($data);

        // Now work out the data for billing
        $client_id = $data['client_id'];
        $service_id = $data['service_id'];
        $service_booking_id = $data['service_booking_id'];
        $planmanager_id = $data['planmanager_id'];

        $claimable = Utilities::calculateClaimable(
            kms: $data['kms'],
            trip_duration: $data['trip_duration'],
            service_booking_id: $service_booking_id
        );

        if (isset($data['kms']) && $data['kms'] > 0) {
            $provider_travel_service_id = Utilities::getProviderTravelServiceId($service_id);
            if (!empty($provider_travel_service_id)) {
                // create the travel distance entry, billed against the provider travel support item
                (new TripBilling)->addDistance([
                    'trip_id' => $trip['id'],
                    'staff_id' => $data['staff_id'],
                    'client_id' => $client_id,
                    'service_id' => $provider_travel_service_id,
                    'service_booking_id' => $service_booking_id,
                    'planmanager_id' => $planmanager_id,
                    'session_date' => $data['trip_date'],
                    'session_duration' => $claimable['kms'],
                ]);
            }
        }

        if (isset($data['trip_duration']) && $data['trip_duration'] > 0) {
            // create the travel duration entry billed against the service support item
            (new TripBilling)->addDuration([
                'trip_id' => $trip['id'],
                'staff_id' => $data['staff_id'],
                'client_id' => $client_id,
                'service_id' => $service_id,
                'service_booking_id' => $service_booking_id,
                'planmanager_id' => $planmanager_id,
                'session_date' => $data['trip_date'],
                'session_duration' => $data['trip_duration'],
            ]);
        }

        return;  // 201
    }
}
