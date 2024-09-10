<?php
namespace NDISmate\Services\ServiceAgreementService;

use NDISmate\Utilities\ConvertFieldsToBoolean;
use RedBeanPHP\R as R;

class ListServicesByServiceAgreementId
{
    public function __invoke($data)
    {

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
                    ANY_VALUE(clientplanservices.budget_start_date) AS budget_start_date,
                    ANY_VALUE(services.budget_display) AS budget_display,
                    ANY_VALUE(services.billing_code) AS billing_code,
                    ANY_VALUE(services.billing_unit) AS billing_unit,
                    SUM(timetrackings.session_duration) AS session_duration,
                    MAX(timetrackings.session_date) AS last_session_date,
                    ANY_VALUE(clientplanservices.rate) AS rate,
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
                    clientplanservices.is_active AS is_active,
                    clientplanservices.adjust_weekly_time AS adjust_weekly_time,
                    clientplanservices.allocated_funding as allocated_funding
                FROM clientplanservices
                LEFT JOIN timetrackings ON timetrackings.participant_service_id = clientplanservices.id
                    AND timetrackings.session_date >= clientplanservices.budget_start_date
                    AND (timetrackings.session_duration > 0 AND timetrackings.session_duration is not null)
                JOIN services ON services.id = clientplanservices.service_id
                JOIN planmanagers ON planmanagers.id = clientplanservices.plan_manager_id 
                WHERE clientplanservices.plan_id = :service_agreement_id
                    
                GROUP BY
                    clientplanservices.plan_id,
                    clientplanservices.id
                ORDER BY
                    clientplanservices.plan_id,
                    clientplanservices.id";

            if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
                $query = str_replace('ANY_VALUE', '', $query);
            }

            $beans = R::getAll(
                $query,
                [':service_agreement_id' => $data['service_agreement_id']]
            );

            // THERE IS AN ERROR HERE
            $converter = new ConvertFieldsToBoolean();
            $beans = $converter($beans, ['include_travel', 'is_active', 'adjust_weekly_time']);

            // $beans = (new ConvertFieldsToBoolean)($beans, ['include_travel']);

            return $beans;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}
