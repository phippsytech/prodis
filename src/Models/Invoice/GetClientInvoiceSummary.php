<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class GetClientInvoiceSummary
{
    public function __invoke($data)
    {
        return ['http_code' => 400, 'error_message' => 'endpoint disabled'];

        $bean = R::getAll(
            "SELECT 
            billings.code, 
            billings.billing_code, 
            SUM(ROUND(billings.rate * billings.quantity, 2)) AS total
        FROM (
            SELECT 
                timetrackings.service_id AS service_id, 
                timetrackings.rate AS rate,
                services.code,
                services.billing_code,
                CASE 
                    WHEN unit = 'E' AND rate != 1 THEN 1 
                    WHEN unit = 'E' AND rate = 1 THEN timetrackings.session_duration
                    ELSE timetrackings.session_duration / 60 
                END AS quantity
            FROM 
                timetrackings
                JOIN services ON (timetrackings.service_id = services.id)
                JOIN supportitems ON (supportitems.support_item_number = services.billing_code)
            WHERE 
                invoice_batch IS NOT NULL 
                AND client_id=:client_id
        ) AS billings
        GROUP BY 
            billings.code
        ",
            [':client_id' => $data['client_id']]
        );

        return ['http_code' => 200, 'result' => $bean];
    }
}

// SELECT
//     billings.code,
//     billings.billing_code,
//     SUM(ROUND(billings.rate * billings.quantity, 2)) AS total
// FROM (
//     SELECT
//         timetrackings.service_id AS service_id,
//         timetrackings.rate AS rate,
//         services.code,
//         services.billing_code,
//         CASE
//             WHEN timetrackings.unit_type = 'E' AND timetrackings.rate != 1 THEN 1
//             WHEN timetrackings.unit_type = 'E' AND timetrackings.rate = 1 THEN timetrackings.session_duration
//             ELSE timetrackings.session_duration / 60
//         END AS quantity
//     FROM
//         timetrackings
//         JOIN services ON (timetrackings.service_id = services.id)
//         JOIN supportitems ON (supportitems.support_item_number = services.billing_code)
//     WHERE
//         timetrackings.invoice_batch IS NOT NULL
//         AND timetrackings.client_id = 332
// ) AS billings
// GROUP BY
//     billings.code,
//     billings.billing_code;
