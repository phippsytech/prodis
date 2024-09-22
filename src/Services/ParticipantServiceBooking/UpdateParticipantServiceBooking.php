<?php

namespace NDISmate\Services\ParticipantServiceBooking;

use NDISmate\Models\Participant\ServiceBooking as ParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\GetParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\CheckUniqueServiceBooking;

/**
 * Class UpdateParticipantService
 *
 * This class is responsible for updating a participant service.
 * It retrieves the service details using the provided service ID,
 * sets the rate from the retrieved service, and then updates a
 * participant service with the provided data.
 */
class UpdateParticipantServiceBooking
{
    /**
     * Invoke method to add a participant service.
     *
     * @param array $data The data required to update a participant service.
     * @return mixed The updated participant service.
     * @throws \Exception If there is an error during the process.
     */
    public function __invoke($data)
    {
        try {


            $isUniqueServiceBooking = (new CheckUniqueServiceBooking)($data);

            // return $isUniqueServiceBooking;

            if ($isUniqueServiceBooking === false) {
                throw new \Exception('An active service of this type already exists for this participant.');
            }


            $result = (new ParticipantServiceBooking)->update($data);

            $participant_service = (new GetParticipantServiceBooking)(['id' => $result['id']]);

            return $participant_service;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
