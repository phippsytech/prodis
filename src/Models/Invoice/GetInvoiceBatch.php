<?php

namespace NDISmate\Models\Invoice;

use RedBeanPHP\R as R;

class GetInvoiceBatch
{
    /**
     * Invoke method to get invoice batch.
     *
     * @param array $data Input data.
     * @return array Result array with HTTP code and result set.
     */
    public function __invoke(array $data): array
    {
        // $result = (new ListGroupedInvoiceLineItems)([
        //     'ndis_number' => '431411369',
        //     'invoice_batch' => 230,
        //     'planmanager_id' => 105
        // ]);

        // return ['http_code' => 200, 'result' => $result];

        // Check if the invoice batch is set and is numeric.
        if (!isset($data['invoice_batch']) || !is_numeric($data['invoice_batch'])) {
            return ['http_code' => 400, 'error_message' => 'Invalid invoice batch.'];
        }

        // Define the query with proper alignment and spacing as per PSR-12.
        $query = "
            SELECT
                TRIM(REPLACE(clients.ndis_number, ' ', '')) AS NDISNumber,
                clients.name AS ClientName,
                timetrackings.planmanager_id AS PlanManagerId
            FROM timetrackings
            JOIN clients ON timetrackings.client_id = clients.id
            JOIN planmanagers ON timetrackings.planmanager_id = planmanagers.id
            WHERE timetrackings.invoice_batch = :invoice_batch
            AND (timetrackings.session_duration > 0 AND timetrackings.session_duration IS NOT NULL)
            GROUP BY clients.ndis_number, clients.name, timetrackings.planmanager_id
            ORDER BY clients.name, timetrackings.planmanager_id
        ";

        // Execute the query.
        $invoiceBatches = R::getAll($query, [':invoice_batch' => $data['invoice_batch']]);

        // Initialize the array to hold the batch information.
        $invoiceBatch = [];

        foreach ($invoiceBatches as $invoiceBatchItem) {
            $lineItems = (new ListGroupedInvoiceLineItems)([
                'ndis_number' => $invoiceBatchItem['NDISNumber'],
                'invoice_batch' => $data['invoice_batch'],
                'planmanager_id' => $invoiceBatchItem['PlanManagerId']
            ])['result'];

            $invoiceNumber = $invoiceBatchItem['NDISNumber'] . '-' . $invoiceBatchItem['PlanManagerId'] . '-' . $data['invoice_batch'];

            $invoiceTotal = 0;
            foreach ($lineItems as $lineItem) {
                $invoiceTotal += $lineItem['Quantity'] * $lineItem['UnitPrice'];
            }

            // Append to the batch array with proper key naming and calculation.
            $invoiceBatch[] = [
                'ClientName' => $lineItems[0]['ClientName'],
                'ClaimReference' => $invoiceNumber,
                'PlanManagerId' => $invoiceBatchItem['PlanManagerId'],
                'PlanManagerName' => $lineItems[0]['ClientBillingName'],
                'InvoiceTotal' => round($invoiceTotal, 2)
            ];
        }

        return ['http_code' => 200, 'result' => $invoiceBatch];
    }
}
