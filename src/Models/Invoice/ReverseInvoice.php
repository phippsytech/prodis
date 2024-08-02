<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;
// use NDISmate\Xero\Helpers as XeroHelpers;

class ReverseInvoice
{
    public function __invoke($data)
    {
        // break up the invoice id into its parts
        $parts = explode('-', $data['invoice_number']);

        switch (count($parts)) {
            case 3:
                $ndis_number = $parts[0];
                $planmanager_id = $parts[1];
                $invoice_batch = $parts[2];
                break;

            case 2:
                $ndis_number = $parts[0];
                $invoice_batch = $parts[1];
                $planmanager_id = 45;  // this is the ndis plan manager id.
                break;

            default:
                return ['http_code' => 400, 'error_message' => 'Invalid invoice number'];
        }

        // Reverse the invoice in Xero:
        $xero = new \NDISmate\Models\Invoice\NDIA\Remittance\Xero();

        try {
            // get the invoice by invoice_number
            $result = $xero->accountingApi->getInvoices($xero->tenant_id, null, null, null, null, $data['invoice_number']);
            $invoice = $result->getInvoices()[0];

            if ($invoice) {
                // Check if the invoice has any payment.  If so, we can't reverse the invoice or billing items.
                if ($invoice->getAmountPaid() > 0) {
                    return ['http_code' => 400, 'error_message' => 'Invoice has a payment.  Cannot reverse.'];
                }

                // Update the invoice status to VOIDED
                $invoice->setInvoiceNumber($invoice->getInvoiceNumber() . 'VOID');
                $invoice->setStatus('VOIDED');
                $result = $xero->accountingApi->updateInvoice($xero->tenant_id, $invoice->getInvoiceID(), $invoice);
            }
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            // if there's an error, most likely the invoice number doesn't exist.
            // we can ignore the error and continue.
        }

        // reverse the invoice in the timetrackings table
        $bean = R::exec(
            'UPDATE timetrackings
            LEFT JOIN clients on (clients.id=timetrackings.client_id)
            LEFT JOIN planmanagers on (planmanagers.id=timetrackings.planmanager_id)
            SET timetrackings.invoice_batch=null
            WHERE clients.ndis_number=:ndis_number
                AND timetrackings.invoice_batch=:invoice_batch
                AND timetrackings.planmanager_id=:planmanager_id',
            [
                ':ndis_number' => [$ndis_number, \PDO::PARAM_STR],  // force redbean to treat ndis_number as a string
                ':invoice_batch' => $invoice_batch,
                ':planmanager_id' => $planmanager_id
            ]
        );

        return ['http_code' => 200];
    }
}
