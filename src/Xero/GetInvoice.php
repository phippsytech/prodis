<?php

namespace NDISmate\Xero;

use \RedBeanPHP\R as R;


class GetInvoice {

    function __invoke($xero, $data){

        $ifModifiedSince=null;
        $where=null;
        $order=null;
        $iDs=[];
        $invoiceNumbers=[$data['invoice_number']];
        $contactIDs=[];
        $statuses=null;
        $page=1;
        $includeArchived=true;
        $createdByMyApp=false;
        $unitdp=4;
        $summaryOnly=false;

        try {
            $apiResponse = $xero->accountingApi->getInvoices($xero->tenant_id, $ifModifiedSince, $where, $order, $iDs, $invoiceNumbers, $contactIDs, $statuses, $page, $includeArchived, $createdByMyApp, $unitdp, $summaryOnly);

            $invoices = $apiResponse->getInvoices();
            if (!empty($invoices)) {
                print_r($invoices);
                // $invoices contains data
            } else {
                // $invoices is empty
                print "No invoices found";
            }
            
            

            // $invoice = $invoices[0];
            
            // $invoice->getInvoiceId()
            return ["http_code"=>200,"result"=>$apiResponse];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            
            $error = json_decode($e->getResponseBody(), true);

            return ["http_code"=>400, "error"=>$error];

        }

    }
}