<?php

namespace NDISmate\Services\ServiceAgreementService;

use NDISmate\Services\ParticipantServiceBooking\DeleteServiceBookingsByAgreementId;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

/**
 * Class AddParticipantService
 *
 * This class is responsible for adding a participant service.
 * It retrieves the service details using the provided service ID,
 * sets the rate from the retrieved service, and then creates a new
 * participant service with the provided data.
 */
class DeleteDraftServiceAgreement
{
    /**
     * Invoke method to add a participant service.
     *
     * @param array $data The data required to create a participant service.
     * @return mixed The created participant service.
     * @throws \Exception If there is an error during the process.
     */
    public function __invoke($data)

    {
        try {
            $service_agreement_id = $data['id'];

            (new DeleteServiceBookingsByAgreementId)(['service_agreement_id' => $service_agreement_id]);
        } catch (\Exception $e) {
        }

        try {

            // Combine the DELETE and the check for is_draft in one query
            $sql = 'DELETE FROM serviceagreements 
                WHERE id = :service_agreement_id 
                AND is_draft = true
            ';

            // Execute the query and get the number of affected rows
            $affectedRows = R::exec($sql, [':service_agreement_id' => $service_agreement_id]);

            if ($affectedRows === 0) {
                throw new \Exception('Deletion failed: Service agreement is not a draft.');
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
