<?php
namespace NDISmate\Services\ParticipantServiceBooking;

use \RedBeanPHP\R as R;

class ListProviderTravelByClientId
{
    public function __invoke($data)
    {
        try {
            // TODO: There is a hardcoded value here that needs to be cleaned up.

            $beans = R::getAll(
                'SELECT 
            s.id as service_id,
            s.billing_code,
            s.code,
            cps.id as service_booking_id,
            cps.plan_manager_id,
            cps.max_claimable_travel_duration
        FROM services s
        join servicebookings cps on cps.service_id = s.id
        JOIN serviceagreements cp ON cp.id = cps.plan_id
        WHERE 
        (cps.include_travel = 1 or cps.service_id=49)
        AND cp.is_active = 1
            AND cp.client_id = :client_id',
                [':client_id' => $data['client_id']]
            );

            return $beans;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
