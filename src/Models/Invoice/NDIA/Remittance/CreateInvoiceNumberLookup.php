<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class CreateInvoiceNumberLookup
{
    function __invoke($invoices)
    {
        // Map the invoice_numbers against the invoice ids
        // This is a ~4MB file btw - curious to see what happens.
        foreach ($invoices as $invoice) {
            $this->storeInvoiceNumber($invoice);
        }
    }

    function storeInvoiceNumber($invoice)
    {
        $invoice_number = $invoice->getInvoiceNumber();
        $xero_invoice_number_lookup = R::findOne(
            'xeroinvoicenumberlookup',
            'invoice_number=:invoice_number',
            [':invoice_number' => [$invoice_number, \PDO::PARAM_STR]]
        );
        if ($xero_invoice_number_lookup) {
            $xero_invoice_number_lookup->invoice_ref = $invoice->getInvoiceID();
            R::store($xero_invoice_number_lookup);
        } else {
            $xero_invoice_number_lookup = R::dispense('xeroinvoicenumberlookup');
            $xero_invoice_number_lookup->invoice_number = $invoice_number;
            $xero_invoice_number_lookup->invoice_ref = $invoice->getInvoiceID();
            R::store($xero_invoice_number_lookup);
        }
    }
}
