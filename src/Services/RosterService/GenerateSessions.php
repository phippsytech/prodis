<?php

namespace NDISmate\Services\RosterService;

use NDISmate\CORE\KeyValue;
use NDISmate\Services\ParticipantServiceBooking\GetParticipantServiceBooking;
use NDISmate\Services\TimeTrackingService\AddTimeTracking;
use RedBeanPHP\R as R;

class GenerateSessions
{
    function __invoke($data)
    {
        try {
            $billing = (new \NDISmate\Models\SIL\Billing\GetBilling)($data);

            $results = [];

            foreach ($billing as $item) {
                $item['client_id'] = $data['client_id'];

                $participant_service = (new GetParticipantServiceBooking)([
                    'code' => $item['service_code'],
                    'participant_id' => $item['client_id'],
                ]);

                $item['session_date'] = $item['start_date'];
                $item['service_booking_id'] = $participant_service['id'];

                $item['claim_type'] = '';
                // $item['cancellation_reason'] = null;

                $result = (new AddTimeTracking)($item);
                $results[] = $result;
            }

            // update billing_period_start_date
            KeyValue::set('sil_billing_period_start_date', date('Y-m-d', strtotime($data['end_date'] . ' +1 day')));

            return $results;
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
