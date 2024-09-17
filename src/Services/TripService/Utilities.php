<?php
namespace NDISmate\Services\TripService;

use NDISmate\Services\TripService\Utilities\CalculateClaimable;
use NDISmate\Services\TripService\Utilities\GetAdditionalData;
use NDISmate\Services\TripService\Utilities\GetProviderTravelServiceId;

class Utilities
{
    public static function calculateClaimable($kms, $trip_duration, $service_booking_id)
    {
        return (new CalculateClaimable)(
            kms: $kms,
            trip_duration: $trip_duration,
            service_booking_id: $service_booking_id
        );
    }

    public static function getAdditionalData($data)
    {
        return (new GetAdditionalData)($data);
    }

    public static function getProviderTravelServiceId($service_id)
    {
        return (new GetProviderTravelServiceId)($service_id);
    }
}
