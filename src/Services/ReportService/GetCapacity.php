<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetCapacity
{
    public function __invoke($data)
    {
        $bean = R::getAll(
            "SELECT 
            t1.staff_id,
            t1.staff_name,
            t1.billable_hours_kpi,
            sum(t1.hours_per_week) AS total_sbis_hours_per_week
            FROM(
            SELECT 
                clientstaffassignments.id, 
                clientstaffassignments.staff_id,
                staffs.name AS staff_name,
                staffs.billable_hours_kpi,
                clientstaffassignments.client_id, 
                clients.name AS client_name,
                (clientplanservices.budget / clientplanservices.rate)/(DATEDIFF(clientplans.service_agreement_end_date, clientplans.service_agreement_signed_date)/7) AS hours_per_week,
                services.code,
                clientplanservices.rate
            FROM clientstaffassignments 
            JOIN clients ON (clientstaffassignments.client_id = clients.id)
            JOIN clientplans ON (clientstaffassignments.client_id = clientplans.client_id)
            JOIN clientplanservices ON (clientplans.id=clientplanservices.plan_id)
            JOIN services ON (clientplanservices.service_id=services.id)
            JOIN staffs ON (clientstaffassignments.staff_id=staffs.id)
            WHERE services.code LIKE '%SBIS%'
            AND clientstaffassignments.is_primary='1'
            AND clientplans.service_agreement_end_date > CURRENT_DATE
            AND (clients.archived != 1 AND clients.archived != 0 OR clients.archived IS NULL)
            AND (staffs.archived != 1 AND staffs.archived != 0 OR staffs.archived IS NULL)
            ) AS t1
            GROUP BY staff_id"
        );

        return $bean;
    }
}
