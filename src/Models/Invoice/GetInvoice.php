<?php

/**
 * GetInvoice
 *
 * This class gets line items for a specific invoice
 * if it is an ndia invoice it gets any errors associated with the line items
 *
 * @package NDISmate\Models\Invoice
 */

namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class GetInvoice
{
    public function __invoke($data)
    {
        if (isset($data['invoice_number']) && !empty($data['invoice_number'])) {
            // break up the invoice id into its parts
            $parts = explode('-', $data['invoice_number']);

            switch (count($parts)) {
                case 3:
                    $data['ndis_number'] = $parts[0];
                    $data['planmanager_id'] = $parts[1];
                    $data['invoice_batch'] = $parts[2];
                    break;

                case 2:
                    $data['ndis_number'] = $parts[0];
                    $data['invoice_batch'] = $parts[1];
                    $data['planmanager_id'] = 45;  // TODO: Hard coded data. Replace with Plan Manager Id for NDIA
                    break;

                default:
            }
        }

        if (!isset($data['ndis_number']) || !isset($data['planmanager_id']) || !isset($data['invoice_batch'])) {
            return ['http_code' => 400, 'error_message' => 'Invalid invoice number'];
        }

        $line_items = (new \NDISmate\Models\Invoice\ListGroupedInvoiceLineItems)($data);
        if (!isset($line_items['result'])) {  // If there are no line items, skip the rest of the code
            return ['http_code' => 400, 'error_message' => "No line items found for invoice {$data['invoice_number']}"];
        }

        $line_items = $line_items['result'];

        // Initialize $errors as null or from a function that might return null
        $errors = null;

        $errors = (new \NDISmate\Models\Invoice\NDIA\PaymentRequestStatus\GetErrorsByInvoiceBatch)($data);

        // Ensure $errors is an array before using array_key_exists()
        $errors = is_array($errors) && array_key_exists('result', $errors) ? $errors['result'] : null;

        // Iterate through each result item
        foreach ($line_items as $key => $line_item) {
            // Initialize an empty errors array for each result item
            $line_items[$key]['errors'] = [];

            if ($errors !== null) {
                // Check each error against this result item
                foreach ($errors as $error) {
                    if ($line_item['NDISNumber'] == $error['ndis_number'] &&
                        $line_item['SupportsDeliveredFrom'] == $error['supports_delivered_from'] &&
                        $line_item['SupportsDeliveredTo'] == $error['supports_delivered_to'] &&
                        $line_item['SupportNumber'] == $error['support_number'] &&
                        $line_item['Quantity'] == $error['quantity'] &&
                        $line_item['UnitPrice'] == $error['unit_price'] &&
                        $line_item['ClaimType'] == $error['claim_type'] &&
                        ($line_item['CancellationReason'] == $error['cancellation_reason'] ||
                            (empty($error['cancellation_reason']) &&
                                empty($line_item['CancellationReason'])))) {
                        // If all conditions match, add the error message to the result item's errors array
                        $line_items[$key]['errors'][] = $error['id'];
                        $line_items[$key]['error'] = $error['id'];
                    }
                }
            }
        }

        return ['http_code' => 200, 'result' => $line_items];
    }
}
