<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class GetPrototype
{
    function __invoke($data)
    {
        if (!isset($data['remittance_csv_name'])) {
            return ['http_code' => 400, 'error' => 'Missing remittance_csv_name'];
        }

        $query =
            "SELECT
            payment_details.ndis_number,
            payment_details.invoice_batch,
            invoice_details.price as invoice_total,
            payment_details.total as amount_paid
    FROM
        (SELECT 
            SUBSTRING_INDEX(invoice_number, '-', 1) AS ndis_number,
            SUBSTRING_INDEX(SUBSTRING_INDEX(invoice_number, '-', 2), '-', -1) AS invoice_batch,
            SUM(amount_paid) AS total
        FROM
            ndiapaymentremittances
        WHERE
            remittance_csv_name = :remittance_csv_name
        GROUP BY
        invoice_number, invoice_batch) AS payment_details
    LEFT JOIN
        (SELECT 
            TRIM(REPLACE(c.ndis_number, ' ', '')) AS ndis_number,
            t.invoice_batch,
            SUM(ROUND(ROUND(t.session_duration / 60, 2) * s.rate, 2)) AS price
        FROM
            timetrackings AS t
            JOIN services AS s ON s.id = t.service_id
            JOIN clients AS c ON c.id = t.client_id
        WHERE
            t.invoice_batch IS NOT NULL
            AND t.invoice_batch > 0
            AND t.planmanager_id = 45
        GROUP BY
            c.ndis_number, t.invoice_batch) AS invoice_details
    ON
        payment_details.ndis_number = invoice_details.ndis_number
        AND payment_details.invoice_batch = invoice_details.invoice_batch";

        $beans = R::getAll($query, [':remittance_csv_name' => $data['remittance_csv_name']]);

        return ['http_code' => 200, 'result' => array_values($beans)];  // Reset keys and prepare for output
    }
}
