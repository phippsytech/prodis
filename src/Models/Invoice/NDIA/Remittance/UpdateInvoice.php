<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \NDISmate\Xero\Session;
use \RedBeanPHP\R as R;

class UpdateInvoice
{
    // var $xero_contact_id = "bc7042bd-5a23-4c17-8f7e-4efe2dc84f09"; // NDIA Contact in Demo Company
    var $xero_contact_id = '87cf3341-2a14-49a6-9f9e-2d623eba0b76';  // NDIA Contact in Crystel Care

    function __invoke($data)
    {
        $invoice_number = $data['invoice_number'];
        $claim_date = $data['claim_date'];

        $xero = new \NDISmate\Models\Invoice\NDIA\Remittance\Xero();

        $xero_invoice_number_lookup = R::findOne(
            'xeroinvoicenumberlookup',
            'invoice_number=:invoice_number',
            [':invoice_number' => [$invoice_number, \PDO::PARAM_STR]]
        );

        $invoice_id = $xero_invoice_number_lookup->invoice_ref;

        $invoice_date = (new \DateTime(date('Y-m-d', strtotime($claim_date))));
        $invoice_due_date = (new \DateTime(date('Y-m-d', strtotime($claim_date . '+7 days'))));

        $ndis_number = $this->getNDISNumberFromInvoiceNumber($invoice_number);

        $obj_contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $obj_contact->setContactId($this->xero_contact_id);

        $obj_invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
        $obj_invoice->setDateAsDate($invoice_date);
        $obj_invoice->setDueDateAsDate($invoice_due_date);
        $obj_invoice->setContact($obj_contact);
        $obj_invoice->setInvoiceId($invoice_id);
        $obj_invoice->setInvoiceNumber($invoice_number);
        $obj_invoice->setReference($this->getClientName($ndis_number) . ' - ' . $ndis_number);
        $obj_invoice->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED);
        $obj_invoice->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC);
        // don't set this value to NO_TAX because line items will be set to BASEXCLUDED and we don't want that.
        $obj_invoice->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);  // excludes GST

        // Setup the Line Items
        $arr_line_items = [];
        $line_items = $this->getInvoiceLineItems($invoice_number);

        foreach ($line_items as $line_item) {
            $service_code = $this->getServiceCode($line_item['billing_code']);
            $service_name = $this->getSupportItemName($line_item['billing_code']);

            if (!empty($line_item['claim_type'])) {
                $claim_type = ' - Claim Type: ' . $line_item['claim_type'];
                if ($line_item['claim_type'] == 'Cancellation Charges') {
                    $claim_type .= ' - ' . $line_item['cancel_reason'];
                }
            } else {
                $claim_type = '';
            }

            // Setup the Line Item
            $obj_line_item = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
            $obj_line_item->setDescription($line_item['billing_code'] . ' - ' . $service_name . ' - ' . date('d/m/Y', strtotime($line_item['supports_delivered_from'])) . $claim_type);
            $obj_line_item->setQuantity($line_item['quantity']);
            $obj_line_item->setUnitAmount(number_format($line_item['unit_price'], 4, '.', ''));
            $obj_line_item->setAccountCode($this->getXeroAccountCode($ndis_number, $line_item['billing_code']));

            // if the item code doesn't exist, the invoice fails silently
            $obj_line_item->setItemCode($service_code);

            $obj_line_item->setTaxType(\XeroAPI\XeroPHP\Models\Accounting\TaxType::EXEMPTOUTPUT);
            $obj_line_item->setTaxAmount(0.0);  // I think this line is the ultimate solution to the rounding issue.
            array_push($arr_line_items, $obj_line_item);
        }

        $obj_invoice->setLineItems($arr_line_items);

        $invoices = new \XeroAPI\XeroPHP\Models\Accounting\Invoices;
        $arr_invoices = [];
        array_push($arr_invoices, $obj_invoice);
        $invoices->setInvoices($arr_invoices);

        try {
            $apiResponse = $xero->accountingApi->updateInvoice($xero->tenant_id, $invoice_id, $invoices, null, 4);
            return $apiResponse;  // return the array of invoices
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            print_r($error);
            return ['http_code' => 400, 'error' => $error];
        }
    }

    function getNDISNumberFromInvoiceNumber($invoice_number)
    {
        $parts = explode('-', $invoice_number);
        $ndis_number = $parts[0];
        return $ndis_number;
    }

    function getClientName($ndis_number)
    {
        $client_name = R::getCell(
            "SELECT name 
            FROM clients 
            WHERE REPLACE(ndis_number, ' ', '')=:ndis_number",
            [
                ':ndis_number' => [$ndis_number, \PDO::PARAM_STR],  // force redbean to treat as string
            ]
        );
        return $client_name;
    }

    function getSupportItemName($support_item_number)
    {
        $support_item_name = R::getCell(
            'SELECT support_item_name 
            FROM supportitems 
            WHERE support_item_number=:support_item_number',
            [
                ':support_item_number' => $support_item_number
            ]
        );
        return $support_item_name;
    }

    function getServiceCode($billing_code)
    {
        $service_code = R::getCell(
            'SELECT code 
            FROM services 
            WHERE billing_code=:billing_code',
            [
                ':billing_code' => $billing_code
            ]
        );
        return $service_code;
    }

    function getXeroAccountCode($ndis_number, $billing_code)
    {
        $xero_account_code = R::getCell(
            'SELECT xero_account_code 
            FROM clientplanservices
            JOIN clientplans ON (clientplans.id = clientplanservices.plan_id)
            JOIN clients ON (clients.id = clientplans.client_id)
            JOIN services ON (services.id = clientplanservices.service_id)
            WHERE clients.ndis_number=:ndis_number 
            AND services.billing_code=:billing_code',
            [
                ':ndis_number' => [$ndis_number, \PDO::PARAM_STR],  // force redbean to treat ndis_number as a string
                ':billing_code' => $billing_code
            ]
        );

        // return a default account code if no account code is found
        if (!$xero_account_code)
            return '200';

        return $xero_account_code;
    }

    function getInvoiceLineItems($invoice_number)
    {
        $line_items = R::getAll(
            'SELECT * 
            FROM ndiapaymentremittances 
            WHERE invoice_number=:invoice_number',
            [
                ':invoice_number' => $invoice_number
            ]
        );
        return $line_items;
    }
}
