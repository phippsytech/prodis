<?php
namespace NDISmate\Services\TripService\Utilities;

use RedBeanPHP\R as R;

class CalculateClaimable
{
    function __invoke($kms, $trip_duration, $participant_service_id)
    {
        $maxClaimableTime = R::getCell(
            'SELECT 
                max_claimable_travel_duration
            FROM clientplanservices 
            WHERE id = :participant_service_id',
            [':participant_service_id' => $participant_service_id]
        );

        // If trip_duration is not set or 0, return 0 for both claimableKms and claimableTime
        if (empty($trip_duration)) {
            return ['kms' => $kms, 'duration' => 0];
        }

        $claimableKms = $kms;
        $claimableTime = $trip_duration;  // Use numeric value for time

        if ($trip_duration > $maxClaimableTime) {
            $claimablePercentage = $maxClaimableTime / $trip_duration;
            $claimableKms = round($kms * $claimablePercentage, 2);  // Rounded to 2 decimal places
            $claimableTime = $maxClaimableTime;  // Use numeric value for max time
        }

        return ['kms' => $claimableKms, 'duration' => $claimableTime];
    }
}
