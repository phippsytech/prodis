<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetParticipantBudget
{
    public function __invoke($data)
    {
        // TODO: This query should return service_agreement_end_date instead of ndis_plan_end_date
        // but that will need to be modified in the client code as well.

        $query = 'SELECT 
            clients.id,
            clients.name,
            services.code,
            ANY_VALUE(timetrackings.rate) as rate,
            ANY_VALUE(clientplanservices.budget) as budget,
            ANY_VALUE(clientplans.service_agreement_end_date) as ndis_plan_end_date,
            ROUND(SUM((timetrackings.session_duration/60) * ANY_VALUE(timetrackings.rate) ),2) as total_billed,
            ((ANY_VALUE(clientplanservices.budget))-ROUND(SUM((timetrackings.session_duration/60) * ANY_VALUE(timetrackings.rate) ),2)) as remaining,
            (DATEDIFF(ANY_VALUE(clientplans.service_agreement_end_date), 
            CURDATE())/7) as weeks_remaining
        FROM timetrackings 
        JOIN clients ON (timetrackings.client_id=clients.id)
        JOIN services ON (timetrackings.service_id=services.id)
        JOIN clientplans ON (clientplans.client_id=timetrackings.client_id)
        JOIN clientplanservices ON (clientplanservices.plan_id=clientplans.id and clientplanservices.service_id=services.id)
        JOIN supportitems ON (services.billing_code=supportitems.support_item_number)
        WHERE 
            (clients.archived != 1 or clients.archived is null)
            AND clientplanservices.budget is not null
            AND supportitems.support_category_number NOT IN (1,4)
            AND timetrackings.session_duration is not null
            
            AND timetrackings.session_date >= clientplans.service_agreement_signed_date
        GROUP BY timetrackings.client_id, services.code
        ORDER BY ANY_VALUE(clientplans.service_agreement_end_date), clients.name, services.code';

        // AND clientplans.service_agreement_end_date >= CURDATE()

        if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
            $query = str_replace('ANY_VALUE', '', $query);
        }

        $bean = R::getAll($query);

        return $bean;
    }
}
