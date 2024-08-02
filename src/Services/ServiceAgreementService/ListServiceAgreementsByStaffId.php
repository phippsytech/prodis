<?php
namespace NDISmate\Services\ServiceAgreementService;

use RedBeanPHP\R as R;

class ListServiceAgreementsByStaffId
{
    public function __invoke($data)
    {
        try {
            $query =
                'WITH ActiveClientPlans AS (
                    SELECT 
                        client_id, 
                        MAX(service_agreement_end_date) as max_end_date
                    FROM clientplans
                    WHERE is_active = 1
                    GROUP BY client_id
                ),
                ClientStaffAssignments AS (
                    SELECT 
                        csa.client_id, 
                        clients.name as client_name,
                        clients.on_hold,
                        acp.max_end_date as service_agreement_end_date,
                        ROW_NUMBER() OVER (PARTITION BY csa.client_id ORDER BY csa.client_id) as row_num
                    FROM clientstaffassignments csa
                    JOIN clients 
                        ON clients.id = csa.client_id
                    JOIN staffs 
                        ON staffs.id = csa.staff_id
                    LEFT JOIN ActiveClientPlans acp
                        ON clients.id = acp.client_id
                    WHERE 
                        (clients.archived IS NULL OR clients.archived != 1)
                        AND staffs.id = :staff_id
                )
                SELECT 
                    client_id, 
                    client_name,
                    on_hold,
                    service_agreement_end_date,
                    DATEDIFF(DATE(service_agreement_end_date), NOW()) as days_ago
                FROM ClientStaffAssignments
                WHERE row_num = 1
                ORDER BY service_agreement_end_date';

            if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
                $query = str_replace('ANY_VALUE', '', $query);
            }

            $beans = R::getAll(
                $query,
                [':staff_id' => $data['staff_id']]
            );

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
