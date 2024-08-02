<?php

namespace NDISmate\Services\ParticipantService;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class GetParticipantService
{
    function __invoke($data)
    {
        $where = '';
        $params = [];

        if (empty($data['participant_id']) && empty($data['id'])) {
            throw new \Exception('Participant ID is required');
        }

        if (!empty($data['participant_id']) && !empty($data['code'])) {
            $where = ' clientplanservices.participant_id = :participant_id AND services.code = :code';
            $params = [':participant_id' => $data['participant_id'], ':code' => $data['code']];
        }

        if (!empty($data['participant_id']) && !empty($data['service_id'])) {
            $where = ' clientplanservices.participant_id = :participant_id AND clientplanservices.service_id = :service_id ';
            $params = [':participant_id' => $data['participant_id'], ':service_id' => $data['service_id']];
        }

        if (!empty($data['id'])) {
            $where = ' clientplanservices.id = :clientplanservice_id ';
            $params = [':clientplanservice_id' => $data['id']];
        }

        try {
            $query =
                "SELECT
                    clientplanservices.id,
                    clientplanservices.plan_id,
                    ANY_VALUE(clientplanservices.service_id) AS service_id,
                    ANY_VALUE(clientplanservices.plan_manager_id) AS plan_manager_id,
                    ANY_VALUE(planmanagers.name) AS plan_manager_name,
                    ANY_VALUE(services.code) AS code,
                    ANY_VALUE(clientplanservices.include_travel) AS include_travel,
                    ANY_VALUE(clientplanservices.budget) AS budget,
                    ANY_VALUE(services.budget_display) AS budget_display,
                    ANY_VALUE(services.billing_code) AS billing_code,
                    ANY_VALUE(services.billing_unit) AS billing_unit,
                    COALESCE(SUM(timetrackings.session_duration), 0 ) AS session_duration,
                    ANY_VALUE(services.rate) AS rate,
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
                    ANY_VALUE(clientplanservices.is_active) AS is_active
                FROM clientplanservices
                LEFT JOIN timetrackings ON timetrackings.participant_service_id = clientplanservices.id
                JOIN services ON services.id = clientplanservices.service_id
                JOIN planmanagers ON planmanagers.id = clientplanservices.plan_manager_id 
                WHERE "
                . $where
                . ' 
                GROUP BY
                    clientplanservices.plan_id,
                    clientplanservices.id
                ORDER BY
                    clientplanservices.plan_id,
                    clientplanservices.id
                ';

            if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
                $query = str_replace('ANY_VALUE', '', $query);
            }

            $bean = R::getRow(
                $query,
                $params
            );

            $converter = new ConvertFieldsToBoolean();
            $bean = $converter($bean, ['is_active']);

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
