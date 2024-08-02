<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class CreateInvoices
{
    function __invoke($xero, $invoice_list)
    {
        $arr_invoices = [];
        foreach ($invoice_list as $item) {
            $invoice = (new \NDISmate\Models\Invoice\NDIA\Remittance\CreateInvoice)($xero, $item['invoice_number'], $item['claim_date']);
            array_push($arr_invoices, $invoice);
            unset($invoice);
        }

        $invoices = new \XeroAPI\XeroPHP\Models\Accounting\Invoices;
        $invoices->setInvoices($arr_invoices);

        try {
            $apiResponse = $xero->accountingApi->createInvoices(
                xero_tenant_id: $xero->tenant_id,
                invoices: $invoices,
                summarize_errors: false,
                unitdp: 4,
                // idempotency_key:null,
            );
            return $apiResponse;  // return the array of invoices
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            print_r($error);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
