<?php

namespace NDISmate\Services\ParticipantServiceBooking;

use NDISmate\Models\Participant\ServiceBooking as ParticipantServiceBooking;
// use NDISmate\Services\ParticipantService\CheckUniqueServiceBooking as ParticipantServiceCheckUniqueServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\GetParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\CheckUniqueServiceBooking;
use NDISmate\Models\Participant\ServiceAgreement\GetServiceAgreement;

/**
 * Class AddParticipantService
 *
 * This class is responsible for adding a participant service.
 * It retrieves the service details using the provided service ID,
 * sets the rate from the retrieved service, and then creates a new
 * participant service with the provided data.
 */
class AddParticipantServiceBooking
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

            $serviceAgreement = (new GetServiceAgreement)(["id" => (int)$data['plan_id']]);

            $isDraftServiceBooking = isset($serviceAgreement['is_draft']) ? $serviceAgreement['is_draft'] : false;

            $data['is_draft'] = $isDraftServiceBooking;

            //Check that there are no other active servicebookings for the same service for this participant.
            $isUniqueServiceBooking = (new CheckUniqueServiceBooking)($data);

            if ($isDraftServiceBooking === false && $isUniqueServiceBooking === false) {
                throw new \Exception('An active service of this type already exists for this participant.');
            }

            if ($isDraftServiceBooking && $isUniqueServiceBooking === false) {
                throw new \Exception('A draft service of this type already exists for this participant.');
            }

            $result = (new ParticipantServiceBooking)->create($data);
            $serviceBooking = (new GetParticipantServiceBooking)(['id' => $result['id']]);
            return $serviceBooking;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
