<?php

namespace NDISmate\Models\Invoice\NDIA\PaymentRequestStatus;

use RedBeanPHP\R;

class ProcessErrors
{
    private \NDISmate\Models\Invoice\NDIA\Remittance\Xero $xero;

    public function __construct()
    {
        $this->xero = new \NDISmate\Models\Invoice\NDIA\Remittance\Xero();
    }

    public function __invoke()
    {
        return ['http_code' => 400, 'error_message' => 'This is currently disabled on purpose.'];

        $invoices = R::getAll(
            "SELECT claim_reference
             FROM ndiapaymentrequeststatuss
             WHERE payment_request_status = 'ERROR'
             AND claim_reference LIKE '%-310'
             GROUP BY claim_reference"
        );

        // SELECT claim_reference
        //      FROM ndiapaymentrequeststatuss
        //      WHERE claim_reference = '430688725-280';

        $invoiceNumbers = $this->createXeroInvoiceNumberArray($invoices);

        $result = $this->xero->accountingApi->getInvoices(
            xero_tenant_id: $this->xero->tenant_id,
            invoice_numbers: join(',', $invoiceNumbers)
        );

        $xeroInvoices = $result->getInvoices() ?? [];

        $stuff = \XeroAPI\XeroPHP\AccountingObjectSerializer::sanitizeForSerialization($xeroInvoices);

        // return ['http_code' => 200, 'result' => $stuff];

        $filteredInvoiceNumbers = $this->getFilteredInvoiceNumbers($stuff, $invoiceNumbers);

        foreach ($filteredInvoiceNumbers as $invoiceNumber) {
            $ndismateInvoiceLineItems = (new \NDISmate\Models\Invoice\GetInvoice)(['invoice_number' => $invoiceNumber]);

            if (empty($ndismateInvoiceLineItems)) {
                continue;
            }

            if (empty($ndismateInvoiceLineItems['result'])) {
                continue;
            }
            $ndismateInvoiceLineItems = $ndismateInvoiceLineItems['result'];

            $xeroInvoice = $this->getXeroInvoiceByInvoiceNumber($xeroInvoices, $invoiceNumber);

            $rejectedLineItems = [];
            $billableIds = [];
            foreach ($ndismateInvoiceLineItems as $lineItem) {
                if (count($lineItem['errors']) > 0) {
                    $rejectedLineItems[] = $lineItem;
                    $billableIds = array_unique(array_merge($billableIds, $lineItem['SessionIds']));
                }
            }

            $allItemsHaveErrors = count($ndismateInvoiceLineItems) === count($rejectedLineItems);

            if (!empty($xeroInvoice)) {
                if ($xeroInvoice->getAmountPaid() > 0) {
                    // Invoice has a payment; cannot reverse it.
                } else {
                    if ($allItemsHaveErrors) {
                        $xeroInvoice->setInvoiceNumber($xeroInvoice->getInvoiceNumber() . 'VOID');
                        $xeroInvoice->setStatus('VOIDED');
                    } else {
                        [$xeroInvoice, $billableIds] = (new \NDISmate\Models\Invoice\RemoveXeroInvoiceLineItems)($xeroInvoice, $rejectedLineItems);
                    }
                    try {
                        $this->updateXeroInvoice($xeroInvoice);
                    } catch (\XeroAPI\XeroPHP\ApiException $e) {
                        // Handle exception or log error
                        return ['http_code' => 400, $e];
                    }
                }
            }

            $this->storeErrors($rejectedLineItems);
            $this->reverseBillables($billableIds);
        }

        return ['http_code' => 201, [$rejectedLineItems, $billableIds]];
    }

    /**
     * Store errors for line items.
     *
     * @param array $lineItems
     * @return void
     */
    private function storeErrors(array $lineItems): void
    {
        foreach ($lineItems as $lineItem) {
            foreach ($lineItem['SessionIds'] as $sessionId) {
                $bean = R::findOrCreate(
                    'billablendiaerrors',
                    [
                        'timetracking_id' => $sessionId,
                        'payment_request_id' => $lineItem['error']
                    ]
                );

                R::store($bean);
            }
        }
    }

    /**
     * Reverse billables based on given IDs.
     *
     * @param array $ids
     * @return void
     */
    private function reverseBillables(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE timetrackings SET invoice_batch=null WHERE id IN ($placeholders)";
        R::exec($sql, $ids);
    }

    /**
     * Find a Xero invoice by its invoice number.
     *
     * @param array $xeroInvoices
     * @param string $invoiceNumber
     * @param bool $triedReplacing
     * @return mixed
     */
    private function getXeroInvoiceByInvoiceNumber(array $xeroInvoices, string $invoiceNumber, bool $triedReplacing = false)
    {
        foreach ($xeroInvoices as $invoice) {
            if ($invoice->getInvoiceNumber() === $invoiceNumber) {
                return $invoice;
            }
        }

        if (!$triedReplacing) {
            $modifiedInvoiceNumber = strpos($invoiceNumber, '-45-') !== false
                ? str_replace('-45-', '-', $invoiceNumber)
                : str_replace('-', '-45-', $invoiceNumber);

            return $this->getXeroInvoiceByInvoiceNumber($xeroInvoices, $modifiedInvoiceNumber, true);
        }

        return null;
    }

    /**
     * Updates a Xero invoice.
     *
     * @param mixed $invoice
     * @return void
     */
    private function updateXeroInvoice($invoice)
    {
        try {
            $this->xero->accountingApi->updateInvoice(
                xero_tenant_id: $this->xero->tenant_id,
                invoice_id: $invoice->getInvoiceID(),
                invoices: $invoice
            );
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            // Handle exception or log error
        }
    }

    /**
     * Create an array of Xero invoice numbers.
     *
     * Generates alternative invoice numbers for the old style numbering where
     * we skipped -45- for NDIA invoices
     *
     * @param array $invoices
     * @return array
     */
    private function createXeroInvoiceNumberArray(array $invoices): array
    {
        $processedInvoiceNumbers = [];

        foreach ($invoices as $invoice) {
            $invoiceNumber = $invoice['claim_reference'];
            $processedInvoiceNumbers[] = $invoiceNumber;
            $processedInvoiceNumbers[] = strpos($invoiceNumber, '-45-') !== false
                ? str_replace('-45-', '-', $invoiceNumber)
                : str_replace('-', '-45-', $invoiceNumber);
        }

        return array_unique($processedInvoiceNumbers);
    }

    /**
     * Filters invoice numbers that appear in the invoices array.
     *
     * @param $invoices Array of invoices with 'InvoiceNumber' keys.
     * @param array $invoiceNumbers Array of invoice numbers to filter.
     * @return array Filtered list of invoice numbers that appear in the invoices array.
     */
    private function getFilteredInvoiceNumbers($invoices, array $invoiceNumbers): array
    {
        // Step 1: Extract the invoice numbers from the $invoices array
        $extractedInvoiceNumbers = array_map(function ($invoice) {
            return $invoice->InvoiceNumber;
        }, $invoices);

        // Step 2: Filter the $invoiceNumbers array
        $filteredInvoiceNumbers = array_filter($invoiceNumbers, function ($invoiceNumber) use ($extractedInvoiceNumbers) {
            return in_array($invoiceNumber, $extractedInvoiceNumbers);
        });

        // Reindex the array
        return array_values($filteredInvoiceNumbers);
    }
}
