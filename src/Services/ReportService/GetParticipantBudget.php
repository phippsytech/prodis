<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetParticipantBudget
{
    public function __invoke($data)
    {
        // TODO: This query should return service_agreement_end_date instead of ndis_plan_end_date
        // but that will need to be modified in the client code as well.


$query="SELECT 
    clients.id,
    timetrackings.service_booking_id,
    clients.name,
    services.code,
    servicebookings.rate,
    servicebookings.budget,
    ANY_VALUE(serviceagreements.service_agreement_end_date) as ndis_plan_end_date,
    
    ROUND(
        SUM(        CASE
                WHEN services.billing_unit = 'hour' AND timetrackings.service_booking_id = servicebookings.id THEN COALESCE(timetrackings.session_duration, 0) / 60 * timetrackings.rate
                WHEN services.billing_unit = 'day' AND timetrackings.service_booking_id = servicebookings.id THEN COALESCE(timetrackings.session_duration, 0) / 24 * timetrackings.rate
                WHEN timetrackings.service_booking_id = servicebookings.id THEN COALESCE(timetrackings.session_duration, 0) * timetrackings.rate
                ELSE 0
            END
        ), 2
        )  AS total_billed,
        (ANY_VALUE(servicebookings.budget) - 
        ROUND(
            SUM(
                CASE
                    WHEN services.billing_unit = 'hour' AND timetrackings.service_booking_id = servicebookings.id THEN COALESCE(timetrackings.session_duration, 0) / 60 * timetrackings.rate
                    WHEN services.billing_unit = 'day' AND timetrackings.service_booking_id = servicebookings.id THEN COALESCE(timetrackings.session_duration, 0) / 24 * timetrackings.rate
                    WHEN timetrackings.service_booking_id = servicebookings.id THEN COALESCE(timetrackings.session_duration, 0) * timetrackings.rate
                    ELSE 0
                END
            ), 2)
    ) AS remaining,
    (DATEDIFF(ANY_VALUE(serviceagreements.service_agreement_end_date), 
                CURDATE())/7) as weeks_remaining
FROM timetrackings
JOIN clients ON (timetrackings.client_id = clients.id)
JOIN serviceagreements ON (serviceagreements.client_id = timetrackings.client_id)
JOIN servicebookings ON (servicebookings.id = timetrackings.service_booking_id)
JOIN services ON (servicebookings.service_id = services.id)
LEFT JOIN (
    SELECT 
        support_item_number, 
        MIN(support_category_number) AS support_category_number 
    FROM supportitems
    GROUP BY support_item_number
) supportitems ON (services.billing_code = supportitems.support_item_number)
WHERE  (clients.archived != 1 OR clients.archived IS NULL)
    AND serviceagreements.is_active = 1
    AND servicebookings.is_active = 1
    AND servicebookings.budget IS NOT NULL
    AND supportitems.support_category_number NOT IN (1, 4)
    AND timetrackings.session_duration IS NOT NULL
    AND timetrackings.session_date >= servicebookings.budget_start_date
    AND timetrackings.session_date <= serviceagreements.service_agreement_end_date
    GROUP BY clients.id, timetrackings.service_booking_id
ORDER BY ANY_VALUE(serviceagreements.service_agreement_end_date), clients.name, services.code";


        // AND serviceagreements.service_agreement_end_date >= CURDATE()

        if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
            $query = str_replace('ANY_VALUE', '', $query);
        }

        $bean = R::getAll($query);

        return $bean;
    }
}
