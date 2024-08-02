<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class ListClientInvoices
{
    public function __invoke($data)
    {
        $query =
            "WITH Grouped AS (
            SELECT 
                tt.invoice_batch AS InvoiceBatch,
                tt.planmanager_id AS PlanManagerId,
                ib.generation_date AS GenerationDate,
                tt.session_date AS SessionDate, 
                tt.claim_type AS ClaimType,
                s.billing_unit AS UnitType,
                CASE 
                    WHEN s.billing_unit = 'each' THEN 1 
                    WHEN s.billing_unit = 'kms' THEN tt.session_duration
                    ELSE tt.session_duration / 60 
                END AS Quantity,
                tt.rate AS Rate,
                CASE 
                    WHEN s.billing_unit = 'each' THEN 1 * tt.rate
                    WHEN s.billing_unit = 'kms' THEN tt.session_duration * tt.rate
                    ELSE (tt.session_duration / 60) * tt.rate
                END AS Total
            FROM 
                timetrackings tt
            JOIN services s ON tt.service_id = s.id
            JOIN invoicebatchs ib ON ib.id = tt.invoice_batch
            WHERE 
                tt.client_id = :client_id
                AND tt.invoice_batch IS NOT NULL
            ORDER BY 
                tt.session_date
        ),
        Aggregated AS (
            SELECT 
                ANY_VALUE(g.InvoiceBatch) AS InvoiceBatch,
                ANY_VALUE(g.GenerationDate) AS GenerationDate,
                ANY_VALUE(g.PlanManagerId) AS PlanManagerId,
                ANY_VALUE(pm.name) AS PlanManagerName,
                ROUND(SUM(g.Total),2) AS Total
            FROM 
                Grouped g
            JOIN planmanagers pm ON pm.id = g.PlanManagerId
            GROUP BY 
                g.InvoiceBatch,    
                g.PlanManagerId
        )
        SELECT 
            a.InvoiceBatch, 
            ANY_VALUE(a.GenerationDate) AS GenerationDate, 
            ANY_VALUE(a.PlanManagerName) AS PlanManagerName, 
            a.PlanManagerId, 
            SUM(a.Total) AS GrandTotal 
        FROM 
            Aggregated a
        GROUP BY 
            a.InvoiceBatch, 
            a.PlanManagerId
        ORDER BY 
            GenerationDate DESC";

        if (!defined('DB_TYPE') || DB_TYPE == 'mariadb') {
            $query = str_replace('ANY_VALUE', '', $query);
        }

        $bean = R::getAll($query, [':client_id' => $data['client_id']]);

        return ['http_code' => 200, 'result' => $bean];
    }
}
