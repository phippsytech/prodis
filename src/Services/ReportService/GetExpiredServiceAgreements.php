<?php
namespace NDISmate\Services\ReportService;

use \RedBeanPHP\R as R;

class GetExpiredServiceAgreements
{
    public function __invoke($data)
    {
        try {
            $sql =
                'WITH LatestClientPlans AS (
                SELECT 
                    client_id, 
                    MAX(service_agreement_end_date) as max_end_date
                FROM clientplans
                GROUP BY client_id
            )
            SELECT 
                clients.id as client_id,
                clients.name as client_name,
                clients.on_hold,
                lcp.max_end_date as service_agreement_end_date,
                DATEDIFF(DATE(lcp.max_end_date), NOW()) as days_ago,
                cp.is_active
            FROM clients
            LEFT JOIN LatestClientPlans lcp
                ON clients.id = lcp.client_id
            LEFT JOIN clientplans cp
                ON lcp.client_id = cp.client_id 
                AND lcp.max_end_date = cp.service_agreement_end_date
            WHERE 
                (clients.archived IS NULL OR clients.archived != 1)
            ORDER BY lcp.max_end_date';

            $beans = R::getAll($sql);

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
