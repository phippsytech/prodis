<?php
namespace NDISmate\Services\TripService\Utilities;

use RedBeanPHP\R as R;

class GetProviderTravelServiceId
{
    function __invoke($service_id)
    {
        $providerTravelServiceId = R::getCell("SELECT 
                services.id
            FROM services
            WHERE services.billing_code = (
                SELECT 
                CONCAT(
                    SUBSTRING_INDEX(services.billing_code, '_', 1),
                    '_799_',
                    SUBSTRING_INDEX(SUBSTRING_INDEX(services.billing_code, '_', -3), '_', 1),
                    '_',
                    SUBSTRING_INDEX(SUBSTRING_INDEX(services.billing_code, '_', -2), '_', 1),
                    '_',
                    SUBSTRING_INDEX(services.billing_code, '_', -1)
                ) AS travel_billing_code
                FROM services
                WHERE services.id = :service_id
            )",
            [':service_id' => $service_id]);

        return $providerTravelServiceId;
    }
}
