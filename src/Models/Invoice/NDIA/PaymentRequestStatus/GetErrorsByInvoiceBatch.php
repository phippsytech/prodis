<?php

namespace NDISmate\Models\Invoice\NDIA\PaymentRequestStatus;

use \RedBeanPHP\R as R;

class GetErrorsByInvoiceBatch
{
    public function __invoke($data)
    {
        // Query to get all errors for a specific invoice batch
        $query = "claim_reference LIKE :claim_reference AND payment_request_status = 'ERROR'";
        $params = [':claim_reference' => '%-' . $data['invoice_batch']];

        // If NDIS number is provided, add it to the query
        if (isset($data['ndis_number'])) {
            $query .= ' AND ndis_number = :ndis_number';
            $params[':ndis_number'] = $data['ndis_number'];
        }

        $beans = R::find('ndiapaymentrequeststatuss', $query, $params);

        // Return the errors if found, otherwise return an error message
        return !empty($beans)
            ? ['http_code' => 200, 'result' => array_values($beans)]
            : ['http_code' => 404, 'error_message' => 'No errors found for this invoice batch.'];
    }
}
