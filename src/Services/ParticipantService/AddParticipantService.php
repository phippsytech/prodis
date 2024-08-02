<?php
namespace NDISmate\Services\ParticipantService;

use NDISmate\Models\Participant\Service as ParticipantService;
use NDISmate\Models\Service\GetService;
use RedBeanPHP\R as R;

/**
 * Class AddParticipantService
 *
 * This class is responsible for adding a participant service.
 * It retrieves the service details using the provided service ID,
 * sets the rate from the retrieved service, and then creates a new
 * participant service with the provided data.
 */
class AddParticipantService
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
            $service = (new GetService)(['id' => $data['service_id']]);
            $data['rate'] = $service['rate'];
            $participant_service = (new ParticipantService)->create($data);
            return $participant_service;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
