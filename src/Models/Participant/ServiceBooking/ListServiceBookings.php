<?php

namespace NDISmate\Models\Participant\ServiceBooking;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;
use NDISmate\Utilities\ConvertFieldsToBoolean;

class ListServiceBookings
{
    public function __invoke($data)
    {
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
                    ANY_VALUE(servicebookings.kilometer_budget) AS kilometer_budget,
                    SUM(timetrackings.session_duration) AS session_duration,
                    MAX(timetrackings.session_date) AS last_session_date,
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
                    servicebookings.is_active AS is_active,
                    servicebookings.adjust_weekly_time AS adjust_weekly_time,
                    servicebookings.allocated_funding as allocated_funding
                FROM servicebookings
                LEFT JOIN timetrackings ON timetrackings.service_booking_id = servicebookings.id
                    AND timetrackings.session_date >= servicebookings.budget_start_date
                    AND (timetrackings.session_duration > 0 AND timetrackings.session_duration is not null)
                JOIN services ON services.id = servicebookings.service_id
                JOIN serviceagreements ON serviceagreements.id = servicebookings.plan_id
                JOIN planmanagers ON planmanagers.id = servicebookings.plan_manager_id 
                WHERE (servicebookings.participant_id = :participant_id OR servicebookings.plan_id = :service_agreement_id)
                GROUP BY
                    servicebookings.id
                ORDER BY
                    servicebookings.id";

            $beans = R::getAll(
                $query,
                [
                    ':participant_id' => $data['participant_id'],
                    ':service_agreement_id' => $data['service_agreement_id'] ?? null
                ]
            );

            $converter = new ConvertFieldsToBoolean();
            $beans = $converter($beans, ['include_travel', 'is_active', 'adjust_weekly_time']);

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
