<?php
namespace NDISmate\Services\ParticipantService;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class GetAvailableSessionDuration
{
    public function __invoke($data)
    {
        $exclude_unbilled = $data['exclude_unbilled'] ?? false;

        // get the budget for the service

        // validate that the participant_service_id are set
        if (!isset($data['participant_service_id']) || !is_numeric($data['participant_service_id'])) {
            throw new \Exception('participant_service_id is required');
        }

        $clientplanservices_bean = R::getRow(
            'SELECT 
                clientplans.service_agreement_signed_date,
                clientplans.service_agreement_end_date,
                clientplanservices.id as id,
                clientplanservices.participant_id as client_id, 
                clientplanservices.service_id as service_id,
                services.code as service_code,
                services.name as services_name,
                clientplanservices.rate as service_rate,
                services.record_travelled_kilometers as record_travelled_kilometers,
                clientplanservices.budget as budget,
                clientplanservices.plan_manager_id as plan_manager_id,
                clientplanservices.kilometer_budget as kilometer_budget
            FROM clientplanservices 
            JOIN services on (services.id = clientplanservices.service_id) 
            JOIN clientplans on (clientplans.id = clientplanservices.plan_id)
            WHERE clientplanservices.id=:participant_service_id',
            [
                ':participant_service_id' => $data['participant_service_id']
            ]
        );

        if (!isset($clientplanservices_bean['service_agreement_signed_date'])) {
            throw new \Exception('Service Agreement Signed Date Not Found');
        }

        // now get the amount spent on the service
        $query =
            'SELECT 
            ROUND(
            SUM( 
                ((timetrackings.session_duration) +
                IFNULL((CASE WHEN timetrackings.actual_travel_time > 30 THEN 30 ELSE timetrackings.actual_travel_time END),0))/60 
                 * timetrackings.rate
            ),2) AS Spent
            FROM timetrackings 
            WHERE participant_service_id = :participant_service_id
            AND session_date >= :service_agreement_signed_date';

        $params = [
            ':participant_service_id' => $data['participant_service_id'],
            ':service_agreement_signed_date' => $clientplanservices_bean['service_agreement_signed_date']
        ];

        if ($exclude_unbilled) {
            $query .= ' AND timetrackings.invoice_batch IS NOT NULL';
        }

        $timetrackings_bean = R::getRow($query, $params);

        // $timetrackings_bean = R::getRow(
        //     'SELECT
        //         ROUND(
        //         SUM(

        //         ((timetrackings.session_duration) +
        //         IFNULL((CASE WHEN timetrackings.actual_travel_time > 30 THEN 30 ELSE timetrackings.actual_travel_time END),0))/60
        //          * timetrackings.rate
        //     ),2) AS Spent
        //     FROM timetrackings
        //     WHERE participant_service_id=:participant_service_id
        //     AND session_date>=:service_agreement_signed_date',
        //     [
        //         ':participant_service_id' => $data['participant_service_id'],
        //         ':service_agreement_signed_date' => $clientplanservices_bean['service_agreement_signed_date']
        //     ]
        // );

        $spent = $timetrackings_bean['Spent'];  // * $clientplanservices_bean['service_rate'];

        if ($clientplanservices_bean['budget'] > 0) {
            $remaining_budget = $clientplanservices_bean['budget'] - $spent;
        } else {
            $remaining_budget = 0;
        }

        $available_session_duration = round(($remaining_budget / $clientplanservices_bean['service_rate']) * 60);

        return
            [
                'available_session_duration' => $available_session_duration,
                'plan_manager_id' => $clientplanservices_bean['plan_manager_id'],
                'record_travelled_kilometers' => $clientplanservices_bean['record_travelled_kilometers'],
            ];
    }
}
