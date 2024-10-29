<?php

namespace NDISmate\Services\ParticipantServiceBooking;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class DeleteServiceBookingsByAgreementId
{
    public function __invoke($data)
    {
        try {
            $service_agreement_id = $data['service_agreement_id'];

            // Combine the DELETE and the check for is_draft in one query
            $sql = 'DELETE FROM servicebookings 
              WHERE plan_id = :service_agreement_id 
              AND EXISTS (
                  SELECT 1 FROM serviceagreements 
                  WHERE id = :service_agreement_id 
                  AND is_draft = true
              )';

            // Execute the query and get the number of affected rows
            $affectedRows = R::exec($sql, [':service_agreement_id' => $service_agreement_id]);

            if ($affectedRows === 0) {
                throw new \Exception('Deletion failed: Service agreement is not a draft or no service bookings found.');
            }

            return;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error deleting service bookings by agreement id: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
