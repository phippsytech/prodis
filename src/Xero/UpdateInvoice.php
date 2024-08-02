<?php

namespace NDISmate\Xero;


use \RedBeanPHP\R as R;


class UpdateInvoice {

    function __invoke($obj){
        
        // $xeroTenantId = "YOUR_XERO_TENANT_ID";
        // $ifModifiedSince = new DateTime("2020-02-06T12:17:43.202-08:00");
        // $where = "Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE . '"";
        // $order = "Name ASC";
    
        $invoiceID = "00000000-0000-0000-0000-000000000000";
        $unitdp = 4;

        $invoice = new XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $invoice->setReference('I am Iron man');

        $invoices = new XeroAPI\XeroPHP\Models\Accounting\Invoices;
        $arr_invoices = [];
        array_push($arr_invoices, $invoice);
        $invoices->setInvoices($arr_invoices);

        try {
            $apiResponse = $obj->accountingApi->updateInvoice($obj->tenant_id, $invoiceID, $invoices, $unitdp);
            
        } catch (Exception $e) {
            echo 'Exception when calling AccountingApi->updateInvoice: ', $e->getMessage(), PHP_EOL;
        }

    }
}