<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class CheckInvoiceNumbers
{
    function __invoke($data)
    {
        $xero = new \NDISmate\Models\Invoice\NDIA\Remittance\Xero();

        $ifModifiedSince = null;
        $where = null;
        $order = null;
        $iDs = null;
        $invoiceNumbers = $this->getInvoiceNumbers($data['page']);
        $contactIDs = null;
        $statuses = null;
        $page = null;
        $includeArchived = true;
        $createdByMyApp = null;
        $unitdp = 4;
        $summaryOnly = true;

        try {
            $invoices = $xero->accountingApi->getInvoices($xero->tenant_id, $ifModifiedSince, $where, $order, $iDs, $invoiceNumbers, $contactIDs, $statuses, $page, $includeArchived, $createdByMyApp, $unitdp, $summaryOnly);

            $lookup = (new \NDISmate\Models\Invoice\NDIA\Remittance\CreateInvoiceNumberLookup)($invoices);

            return ['http_code' => 200, 'result' => $result, 'chunk' => $invoiceNumbers];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            print_r($error);
            return ['http_code' => 400, 'error' => $error];
        }
    }

    function getInvoiceNumbers($page = 0)
    {
        $invoice_numbers = R::getAll(
            'SELECT invoice_number
            FROM ndiapaymentremittances 
            GROUP BY invoice_number'
        );

        $resultArray = array_map(function ($item) {
            return $item['invoice_number'];
        }, $invoice_numbers);

        $chunks = array_chunk($resultArray, 100);

        $items = $chunks[$page];

        return $items;
    }
}
