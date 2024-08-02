<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetLastSeen
{
    public function __invoke($data)
    {
        // MAX(supportitems.registration_group_number) AS registration_group_number,
        //     MAX(supportitems.registration_group_name) AS registration_group_name,
        //     MAX(supportitems.support_category_name) AS support_category_name,

        $sql =
            "SELECT 
                staffs.name AS staff_name, 
                staffs.id AS staff_id,
                clients.name AS client_name, 
                clients.id AS client_id,
                MAX(timetrackings.session_date) AS last_session_date,
                DATEDIFF(DATE(MAX(timetrackings.session_date)), NOW()) AS days_ago
            FROM 
                clientstaffassignments 
            JOIN 
                staffs ON staffs.id = clientstaffassignments.staff_id
            JOIN 
                clients ON clients.id = clientstaffassignments.client_id
            LEFT JOIN 
                timetrackings ON timetrackings.staff_id = clientstaffassignments.staff_id 
                    AND timetrackings.client_id = clientstaffassignments.client_id
                    AND (timetrackings.claim_type IS NULL OR timetrackings.claim_type = '')
            JOIN 
                services ON services.id = timetrackings.service_id
            JOIN 
                supportitems ON services.billing_code=supportitems.support_item_number                
            WHERE 
                (clients.archived != 1 OR clients.archived IS NULL)
                AND (staffs.archived != 1 OR staffs.archived IS NULL)
                AND supportitems.registration_group_number IN ('0110','0128')
                AND support_item_number != '11_023_0110_7_3'
            GROUP BY 
                clientstaffassignments.staff_id, clientstaffassignments.client_id
                ORDER BY
                clientstaffassignments.staff_id, clientstaffassignments.client_id
                ";

        $beans = R::getAll($sql);

        return $beans;
    }
}
