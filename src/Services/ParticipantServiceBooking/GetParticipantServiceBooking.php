<?php

namespace NDISmate\Services\ParticipantServiceBooking;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use RedBeanPHP\R as R;
use RedBeanPHP\RedException as RedException;

class GetParticipantServiceBooking
{
    function __invoke($data)
    {
        $where = '';
        $params = [];

        if (empty($data['participant_id']) && empty($data['id'])) {
            throw new \Exception('Participant ID is required');
        }

        if (!empty($data['participant_id']) && !empty($data['code'])) {
            $where = ' servicebookings.participant_id = :participant_id AND services.code = :code';
            $params = [':participant_id' => $data['participant_id'], ':code' => $data['code']];
        }

        if (!empty($data['participant_id']) && !empty($data['service_id'])) {
            $where = ' servicebookings.participant_id = :participant_id AND servicebookings.service_id = :service_id ';
            $params = [':participant_id' => $data['participant_id'], ':service_id' => $data['service_id']];
        }

        if (!empty($data['id'])) {
            $where = ' servicebookings.id = :servicebooking_id ';
            $params = [':servicebooking_id' => $data['id']];
        }


        // remember servicebookings.plan_id = serviceagreements.id

        try {
            $query =
                "SELECT
                    servicebookings.id,
                    servicebookings.plan_id,
                    ANY_VALUE(serviceagreements.service_agreement_signed_date) AS service_agreement_signed_date,
                    ANY_VALUE(serviceagreements.service_agreement_end_date) AS service_agreement_end_date,
                    ANY_VALUE(servicebookings.service_id) AS service_id,
                    ANY_VALUE(servicebookings.plan_manager_id) AS plan_manager_id,
                    ANY_VALUE(planmanagers.name) AS plan_manager_name,
                    ANY_VALUE(services.code) AS code,
                    ANY_VALUE(servicebookings.include_travel) AS include_travel,
                    ANY_VALUE(servicebookings.budget) AS budget,
                    ANY_VALUE(servicebookings.budget_start_date) AS budget_start_date,
                    ANY_VALUE(services.budget_display) AS budget_display,
                    ANY_VALUE(services.billing_code) AS billing_code,
                    ANY_VALUE(services.billing_unit) AS billing_unit,
                    MAX(timetrackings.session_date) AS last_session_date,
                    COALESCE(SUM(timetrackings.session_duration), 0 ) AS session_duration,
                    ANY_VALUE(servicebookings.rate) AS rate,
                    SUM(
                         CASE
                            WHEN services.billing_unit = 'hour' 
                                THEN COALESCE(timetrackings.session_duration, 0) / 60 * timetrackings.rate
                            WHEN services.billing_unit = 'each' 
                                THEN COALESCE(timetrackings.session_duration, 0) * timetrackings.rate
                            WHEN services.billing_unit = 'kms' 
                                THEN COALESCE(timetrackings.session_duration, 0) * timetrackings.rate
                            WHEN services.billing_unit = 'day' 
                                THEN COALESCE(timetrackings.session_duration, 0) / 24 * timetrackings.rate
                            ELSE COALESCE(timetrackings.session_duration, 0) * timetrackings.rate
                        END
                    ) AS spent,
                    ANY_VALUE(servicebookings.is_active) AS is_active,
                    ANY_VALUE(servicebookings.adjust_weekly_time) AS adjust_weekly_time
                FROM servicebookings
                LEFT JOIN timetrackings ON timetrackings.service_booking_id = servicebookings.id
                    AND timetrackings.session_date >= servicebookings.budget_start_date
                    AND (timetrackings.session_duration > 0 AND timetrackings.session_duration is not null)
                JOIN services ON services.id = servicebookings.service_id
                JOIN serviceagreements on serviceagreements.id = servicebookings.plan_id
                JOIN planmanagers ON planmanagers.id = servicebookings.plan_manager_id 
                WHERE "
                . $where
                . '
                GROUP BY
                    servicebookings.plan_id,
                    servicebookings.id
                ORDER BY
                    servicebookings.plan_id,
                    servicebookings.id
                ';

            $bean = R::getRow(
                $query,
                $params
            );


            $converter = new ConvertFieldsToBoolean();
            $bean = $converter([$bean], ['is_active', 'adjust_weekly_time', 'include_travel'])[0];



            return $bean;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
