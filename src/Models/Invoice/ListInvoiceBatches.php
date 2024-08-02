<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class ListInvoiceBatches
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            "SELECT 
                invoicebatchs.id,
                invoicebatchs.generation_date,
                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM ndiapaymentrequeststatuss 
                        WHERE ndiapaymentrequeststatuss.claim_reference LIKE CONCAT('%-', invoicebatchs.id)
                        AND ndiapaymentrequeststatuss.payment_request_status = 'error'
                    ) THEN 'ERROR'
                    ELSE 'SUCCESS'
                END AS payment_request_status
            FROM invoicebatchs
            ORDER BY invoicebatchs.generation_date DESC, invoicebatchs.id DESC"
        );

        return ['http_code' => 200, 'result' => $beans];
    }
}
