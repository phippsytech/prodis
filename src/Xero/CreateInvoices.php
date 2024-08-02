<?php

namespace NDISmate\Xero;

use \RedBeanPHP\R as R;

class CreateInvoices
{
    function __invoke($obj, $invoices)
    {
        try {
            // Setup the Invoice
            $arr_invoices = [];

            // Generate the Invoices
            foreach ($invoices as $invoice) {
                // Setup the Contact
                $obj_contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
                $obj_contact->setContactId($invoice['contact_id']);

                // Setup the Line Items
                $arr_line_items = [];
                foreach ($invoice['line_items'] as $line_item) {
                    // Setup the Line Item
                    $obj_line_item = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
                    $obj_line_item->setDescription($line_item['description']);
                    $obj_line_item->setQuantity($line_item['quantity']);
                    $obj_line_item->setUnitAmount(number_format($line_item['unit_amount'], 4, '.', ''));
                    $obj_line_item->setAccountCode($line_item['account_code']);
                    $obj_line_item->setItemCode($line_item['item_code']);
                    $obj_line_item->setTaxType(\XeroAPI\XeroPHP\Models\Accounting\TaxType::EXEMPTOUTPUT);
                    $obj_line_item->setTaxAmount(0.0);  // I think this line is the ultimate solution to the rounding issue.
                    array_push($arr_line_items, $obj_line_item);
                }

                // add invoice
                $obj_invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
                if (isset($invoice['date']))
                    $obj_invoice->setDateAsDate(new \DateTime($invoice['date']));
                if (isset($invoice['due_date']))
                    $obj_invoice->setDueDateAsDate(new \DateTime($invoice['due_date']));
                $obj_invoice->setContact($obj_contact);
                $obj_invoice->setLineItems($arr_line_items);
                if (isset($invoice['invoice_number']))
                    $obj_invoice->setInvoiceNumber($invoice['invoice_number']);
                $obj_invoice->setReference($invoice['reference']);
                $obj_invoice->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_AUTHORISED);
                $obj_invoice->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC);

                // Marks the invoice as sent when we create it in Xero.
                if (isset($invoice['sent_to_contact']) && $invoice['sent_to_contact'] === true) {
                    $obj_invoice->setSentToContact(true);
                }

                // don't set this value to NO_TAX because line items will be set to BASEXCLUDED and we don't want that.
                $obj_invoice->setLineAmountTypes(\XeroAPI\XeroPHP\Models\Accounting\LineAmountTypes::EXCLUSIVE);  // excludes GST

                // if we've specified a branding theme for the invoice, use it.
                if (isset($invoice['branding_theme_id']))
                    $obj_invoice->setBrandingThemeId($invoice['branding_theme_id']);
                array_push($arr_invoices, $obj_invoice);
            }

            // Create the Invoices
            $obj_invoices = new \XeroAPI\XeroPHP\Models\Accounting\Invoices;
            $obj_invoices->setInvoices($arr_invoices);
            $apiResponse = $obj->accountingApi->createInvoices(
                xero_tenant_id: $obj->tenant_id,
                invoices: $obj_invoices,
                summarize_errors: false,
                unitdp: 4,
                // idempotency_key:null,
            );

            // suggest code to email the invoice to the client using AWS SES
            // $obj->emailInvoice($apiResponse->getInvoices()[0]->getInvoiceID(), $apiResponse->getInvoices()[0]->getContact()->getEmailAddress());

            return ['http_code' => 200, 'result' => $apiResponse];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);

            return ['http_code' => 400, 'error' => $error];
        }
    }
}
