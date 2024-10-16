<?php
namespace NDISmate\Services\ParticipantServiceBooking;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class GetAvailableSessionDuration
{
    public function __invoke($data)
    {
        $exclude_unbilled = $data['exclude_unbilled'] ?? false;

        // validate that the service_booking_id is set
        if (!isset($data['service_booking_id']) || !is_numeric($data['service_booking_id'])) {
            throw new \Exception('service_booking_id is required');
        }

        // Fetch service booking details
        $servicebookings_bean = R::getRow(
            'SELECT 
                serviceagreements.service_agreement_signed_date,
                serviceagreements.service_agreement_end_date,
                servicebookings.id as id,
                servicebookings.participant_id as client_id, 
                servicebookings.budget_start_date as budget_start_date, 
                servicebookings.service_id as service_id,
                services.code as service_code,
                services.name as services_name,
                servicebookings.rate as service_rate,
                services.record_travelled_kilometers as record_travelled_kilometers,
                servicebookings.budget as budget,
                servicebookings.plan_manager_id as plan_manager_id,
                servicebookings.kilometer_budget as kilometer_budget,
                servicebookings.include_travel as include_travel
            FROM servicebookings 
            JOIN services ON (services.id = servicebookings.service_id) 
            JOIN serviceagreements ON (serviceagreements.id = servicebookings.plan_id)
            WHERE servicebookings.id=:service_booking_id',
            [
                ':service_booking_id' => $data['service_booking_id']
            ]
        );

        if (!isset($servicebookings_bean['service_agreement_signed_date'])) {
            throw new \Exception('Service Agreement Signed Date Not Found');
        }

        // Get the amount spent on the service excluding distance travelled
        $query =
            "SELECT 
                ROUND(
                    SUM(
                        CASE
                            WHEN services.billing_unit = 'hour' THEN COALESCE(timetrackings.session_duration, 0) / 60 * timetrackings.rate
                            WHEN services.billing_unit = 'day' THEN COALESCE(timetrackings.session_duration, 0) / 24 * timetrackings.rate
                            ELSE COALESCE(timetrackings.session_duration, 0) * timetrackings.rate
                        END
                    ), 2
                ) AS spent
            FROM timetrackings 
            JOIN services ON services.id = timetrackings.service_id
            WHERE service_booking_id = :service_booking_id
            AND session_date >= :budget_start_date
            AND unit_type IS NULL";


        $params = [
            ':service_booking_id' => $data['service_booking_id'],
            ':budget_start_date' => $servicebookings_bean['budget_start_date'],
        ];

        if ($exclude_unbilled) {
            $query .= ' AND timetrackings.invoice_batch IS NOT NULL';
        }

        $timetrackings_bean = R::getRow($query, $params);
        
        $spent = $timetrackings_bean['spent'] ?? 0;

        return [
            'available_session_duration' => $this->calculateBudget($servicebookings_bean, $spent),
            'plan_manager_id' => $servicebookings_bean['plan_manager_id'],
            'record_travelled_kilometers' => $servicebookings_bean['record_travelled_kilometers'],
        ];
    }

    // Function to adjust the budget by deducting kilometers
    private function calculateBudget($servicebooking, $spent)
    {
        $adjusted_budget = $servicebooking['budget'];

        // remove km budget on the budget
        if (
            $servicebooking['include_travel'] ||
            $servicebooking['kilometer_budget'] != null ||
            $servicebooking['kilometer_budget'] > 0
          ){
            $adjusted_budget = $servicebooking['budget'] - $servicebooking['kilometer_budget'];
          }

        // check if the adjusted budget is less than the spent
        if ($adjusted_budget < $spent) {
            return 0;
        }

        $adjusted_budget = $adjusted_budget - $spent;

        // Calculate available session duration in minutes
        return round(($adjusted_budget / $servicebooking['service_rate']) * 60);
    }
}
