<?php

namespace NDISmate\Services\ParticipantServiceBooking;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class DuplicateParticipantServiceBooking
{
    public function __invoke($data)
    {
        try {
            $service_booking_id = $data['service_booking_id'];

            $plan_id = $data['service_agreement_id'];

            $originalBean = R::load('servicebookings', $service_booking_id);

            // ## set is_active to false for the original service_booking_id ##
            $originalBean->is_active = false;
            R::store($originalBean);

            // Unbox the original bean to get its properties
            $properties = $originalBean->export();

            // ## DUPLICATE THE SERVICE ##

            // Create a new bean with the same properties
            $newBean = R::dispense('servicebookings');
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
            // set to the new service_booking_id

            $sql = 'UPDATE timetrackings SET rate=:new_rate, service_booking_id=:new_service_booking_id WHERE service_booking_id=:service_booking_id AND session_date >= :amendment_start_date AND invoice_batch IS NULL';
            R::exec($sql, [':new_rate' => $data['new_rate'], ':new_service_booking_id' => $newBean->id, ':service_booking_id' => $service_booking_id, ':amendment_start_date' => $data['amendment_start_date']]);

            // ## UPDATE THE BUDGET
            // set the budget for the `new service_booking_id` based on the rate and available hours (using the available budget from the original service agreement)

            $originalServiceAvailability = (new GetAvailableSessionDuration)([
                'service_booking_id' => $service_booking_id,
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
