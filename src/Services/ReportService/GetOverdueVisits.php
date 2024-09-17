<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetOverdueVisits
{
    public function __invoke($data)
    {
        $sql = "SELECT 

        final.session_id,
            final.client_id,
            final.staff_id,
            final.service_id,
            final.last_session_date,
            final.days_ago,
            final.client_name,
            final.client_on_hold,
            final.therapist,
            final.service_rate,
            final.total_session_minutes,
            final.budget

        FROM
        (SELECT
            timetrackings.id as session_id,
            timetrackings.client_id,
            timetrackings.staff_id,
            timetrackings.service_id,
            MAX(timetrackings.session_date) AS last_session_date,
            DATEDIFF(DATE(MAX(timetrackings.session_date)), NOW()) AS days_ago,
            clients.name as client_name,
            clients.on_hold as client_on_hold,
            staffs.name as therapist,
            timetrackings.rate as service_rate,
            (SELECT 
            SUM( 
                session_duration +
                IFNULL((CASE WHEN actual_travel_time > 30 THEN 30 ELSE actual_travel_time END),0) 
            ) 
            FROM timetrackings t2
            WHERE client_id=timetrackings.client_id 
            AND service_id=timetrackings.service_id
            AND session_date >= serviceagreements.service_agreement_signed_date
        ) as total_session_minutes,
        ((servicebookings.budget/timetrackings.rate)*60) as budget
        FROM timetrackings 
        JOIN clients on (clients.id = timetrackings.client_id)
        JOIN services on (services.id = timetrackings.service_id)
        JOIN staffs on (staffs.id = timetrackings.staff_id)
        JOIN serviceagreements on (serviceagreements.client_id = timetrackings.client_id)
        JOIN servicebookings on (servicebookings.plan_id = serviceagreements.id and servicebookings.service_id = timetrackings.service_id)
        LEFT JOIN clientstaffassignments on (clientstaffassignments.client_id = timetrackings.client_id and clientstaffassignments.staff_id = timetrackings.staff_id)
        WHERE (
        clients.archived != 1 
        OR clients.archived is null
        )
        AND (
        staffs.archived != 1 
        OR staffs.archived is null
        ) 
        AND clientstaffassignments.id is not null
        AND clientstaffassignments.is_primary=1 
        AND clientstaffassignements.staff_role in ('bsp','ot','sp')
        
        AND (
        serviceagreements.ndis_plan_end_date is not null
        AND DATEDIFF(DATE(serviceagreements.ndis_plan_end_date), NOW()) > 0
        )
        AND (
        timetrackings.service_id NOT IN (1,4,20)
        AND timetrackings.service_id IN (select service_id from servicebookings where plan_id=serviceagreements.id)
        )
        ";

        if (isset($data['staff_id'])) {
            $sql .= ' AND timetrackings.staff_id=:staff_id ';
        }

        $sql .= '
        GROUP BY timetrackings.client_id, timetrackings.staff_id
        HAVING days_ago <= -14
        ) final
        HAVING budget > total_session_minutes
        ORDER BY days_ago
        ';

        if (isset($data['staff_id'])) {
            $bean = R::getAll($sql, [':staff_id' => $data['staff_id']]);
        } else {
            $bean = R::getAll($sql);
        }

        return $bean;
    }

    /*
     * I am leaving this here for a reminder about using HAVING in SQL.
     * It might give me some better results in the due report list.
     *
     * SELECT clients.id, clients.name
     * FROM clientstaffassignments
     * join clients on (clients.id = clientstaffassignments.client_id)
     * WHERE (clients.archived != 1 or clients.archived is null)
     * GROUP BY client_id
     * HAVING
     * MAX(primary_therapist) IS NULL
     * AND MAX(primary_occupational_therapist) IS NULL
     * AND MAX(primary_speech_therapist) IS NULL;
     */
}

// SELECT
// clients.name,
// staffs.name,
// timetrackings.staff_id,
// timetrackings.client_id,
// MAX(timetrackings.session_date) AS last_session_date,
// DATEDIFF(DATE(MAX(timetrackings.session_date)), NOW()) AS days_ago
// FROM timetrackings
// JOIN clients ON (clients.id = timetrackings.client_id)
// JOIN staffs ON (staffs.id = timetrackings.staff_id)
// WHERE clients.archived !=1 OR clients.archived is null
// AND staffs.archived !=1 OR staffs.archived is null
// GROUP BY timetrackings.client_id, timetrackings.staff_id
// ORDER BY timetrackings.client_id, timetrackings.staff_id, timetrackings.session_date DESC;
