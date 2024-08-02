<?php

namespace NDISmate\Xero;

use \RedBeanPHP\R as R;


class MakeBatchPayment {

    function __invoke($xero){
        
        ## READY!
        $payment_date = new \DateTime($data['payment_date']);
        $payments = [];
        $arr_batch_payments = [];
        $arr_invoices = []; 
        $arr_line_items=[];

        $xero_contact_id="81ceac77-025a-4a34-a8bf-b885474ce569"; // NDIA Contact in Demo Company
        $xero_bank_account_id="13918178-849a-4823-9a31-57b7eac713d7"; // "Business Bank Account" in Demo Company

        ## SET!
        $obj_contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $obj_contact->setContactId($xero_contact_id);


        $payment_date= new \DateTime();
        $amount=number_format(mt_rand(5000, 99999) / 100, 2);
        $reference = "Article-".rand(1000,9999);
        $invoice_number = "TEST-".microtime(true);
        
        
        // Setup the Line Item
        $obj_line_item = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
        $obj_line_item->setDescription("Test Item");
        $obj_line_item->setQuantity(1);
        $obj_line_item->setUnitAmount($amount);
        $obj_line_item->setAccountCode("200");
        // $obj_line_item->setItemCode($line_item['item_code']);
        $obj_line_item->setTaxType(\XeroAPI\XeroPHP\Models\Accounting\TaxType::EXEMPTOUTPUT);
        $obj_line_item->setTaxAmount(0.00); // I think this line is the ultimate solution to the rounding issue.
        array_push($arr_line_items, $obj_line_item);
        
        
        // add invoice
        $obj_invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $obj_invoice->setDateAsDate($payment_date);
        $obj_invoice->setDueDateAsDate($payment_date);
        $obj_invoice->setContact($obj_contact);
        $obj_invoice->setLineItems($arr_line_items);
        $obj_invoice->setInvoiceNumber($invoice_number);
        $obj_invoice->setReference($reference);
        $obj_invoice->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED);
        $obj_invoice->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC);

        // don't set this value to NO_TAX because line items will be set to BASEXCLUDED and we don't want that.
        $obj_invoice->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE); // excludes GST

        array_push($arr_invoices, $obj_invoice);
   
        // // Create the Invoices
        $obj_invoices = new \XeroAPI\XeroPHP\Models\Accounting\Invoices;
        $obj_invoices->setInvoices($arr_invoices);
        $apiResponse = $obj->accountingApi->createInvoices(
            xero_tenant_id: $obj->tenant_id, 
            invoices: $obj_invoices, 
            summarize_errors: false, 
            unitdp: 4,
            // idempotency_key:null, 
        );

        // turn invoices into payments
        foreach ($apiResponse as $payment_invoice){
            $payment = new \XeroAPI\XeroPHP\Models\Accounting\Payment;
            $payment->setDate($payment_date);
            $payment->setAmount($payment_invoice->getTotal()); // another possible option could be getAmountDue()
            $payment->setInvoice($payment_invoice);
            array_push($payments, $payment);
        }

        // Bank
        $bankAccount = new \XeroAPI\XeroPHP\Models\Accounting\Account;
        $bankAccount->setAccountID($xero_bank_account_id);

        // Batch Payment
        $batchPayment = new \XeroAPI\XeroPHP\Models\Accounting\BatchPayment;
        $batchPayment->setAccount($bankAccount);
        $batchPayment->setReference('NDIA Batch Payment');
        $batchPayment->setDate($payment_date);
        $batchPayment->setPayments($payments);

        // Batch Payments
        $batchPayments = new \XeroAPI\XeroPHP\Models\Accounting\BatchPayments;
        array_push($arr_batch_payments, $batchPayment);
        $batchPayments->setBatchPayments($arr_batch_payments);

        ## GO!
        try {
            $summarizeErrors = true;
            $result = $xero->accountingApi->createBatchPayment($xero->tenant_id, $batchPayments, $summarizeErrors);
            return ["http_code"=>200,"result"=>$result];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }

}