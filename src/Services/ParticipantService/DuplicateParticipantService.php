<?php
namespace NDISmate\Services\ParticipantService;

use RedBeanPHP\R as R;

class DuplicateParticipantService
{
    public function __invoke($data)
    {
        try {
            $participant_service_id = $data['participant_service_id'];

            $plan_id = $data['service_agreement_id'];

            $originalBean = R::load('clientplanservices', $participant_service_id);

            // ## set is_active to false for the original participant_service_id ##
            $originalBean->is_active = false;
            R::store($originalBean);

            // Unbox the original bean to get its properties
            $properties = $originalBean->export();

            // ## DUPLICATE THE SERVICE ##

            // Create a new bean with the same properties
            $newBean = R::dispense('clientplanservices');
            $newBean->import($properties);

            unset($newBean->id);  // Remove the ID to avoid conflicts
            unset($newBean->updated);  // Remove the updated timestamp
            unset($newBean->archived);  // Remove the archived flag
            $newBean->created = date('Y-m-d H:i:s');

            // ## SET THE NEW RATE ##
            // $newBean->rate = $data['new_rate'];
            $newBean->plan_id = $plan_id;
            $newBean->is_active = true;  // set is_active to true

            // ## UPDATE ANY UNBILLED ITEMS ##
            // from and including the amendment_start_date
            // set to the new rate
            // set to the new participant_service_id

            $sql = 'UPDATE timetrackings SET rate=:new_rate, participant_service_id=:new_participant_service_id WHERE participant_service_id=:participant_service_id AND session_date >= :amendment_start_date AND invoice_batch IS NULL';
            R::exec($sql, [':new_rate' => $data['new_rate'], ':new_participant_service_id' => $newBean->id, ':participant_service_id' => $participant_service_id, ':amendment_start_date' => $data['amendment_start_date']]);

            // ## UPDATE THE BUDGET
            // set the budget for the `new participant_service_id` based on the rate and available hours (using the available budget from the original service agreement)

            $originalServiceAvailability = (new GetAvailableSessionDuration)([
                'participant_service_id' => $participant_service_id,
                'exclude_unbilled' => true
            ]);

            $newBean->rate = ($originalServiceAvailability['result']['available_session_duration'] / 60) * $data['new_rate'];

            // Store the new bean
            $newBeanId = R::store($newBean);

            return $newBean;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
