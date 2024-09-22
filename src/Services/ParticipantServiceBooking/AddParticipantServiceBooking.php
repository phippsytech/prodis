<?php

namespace NDISmate\Services\ParticipantServiceBooking;

use NDISmate\Models\Participant\ServiceBooking as ParticipantServiceBooking;
use NDISmate\Services\ParticipantService\CheckUniqueServiceBooking as ParticipantServiceCheckUniqueServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\GetParticipantServiceBooking;
use NDISmate\Services\ParticipantServiceBooking\CheckUniqueServiceBooking;


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

            // This should be looked up in the form, allowing the user to override the rate.
            // $service = (new GetService)(['id' => $data['service_id']]);
            // $data['rate'] = $service['rate'];

            //I need to check that there are no other active servicebookings for the same service for this participant.
            $isUniqueServiceBooking = (new CheckUniqueServiceBooking)($data);

            if ($isUniqueServiceBooking === false) {
                throw new \Exception('An active service of this type already exists for this participant.');
            }

            $result = (new ParticipantServiceBooking)->create($data);
            $service_booking = (new GetParticipantServiceBooking)(['id' => $result['id']]);
            return $service_booking;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
