<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use NDISmate\Xero\Helpers as XeroHelpers;
use \NDISmate\CORE\KeyValue;
use \NDISmate\Xero\Session;
use \RedBeanPHP\R as R;

class GenerateInvoicesAndBatchPayments
{
    /*
     * NOTE:
     * // We are only processing NDIA remittance from 14th February 2023
     * We are only processing NDIA remittance from 25th March 2023
     * Prior to that it is a mess that will need to be sorted manually.
     *
     * delete from ndiapaymentremittances where claim_date < 20230324;
     */

    function __invoke()
    {
        $xeroHelpers = new XeroHelpers;

        // Check that the appropriate items are in Xero
        // https://go.xero.com/app/!Pqzj1/products-and-services
        //    $xeroHelpers->setupItems();

        $xero = new \NDISmate\Models\Invoice\NDIA\Remittance\Xero();

        // First Create Invoices
        $invoice_list = $this->getRemittanceInvoiceList();
        $invoices = (new \NDISmate\Models\Invoice\NDIA\Remittance\CreateInvoices)($xero, $invoice_list);

        // Create a lookup table for invoice numbers / invoice ids (invoice_ref)
        $lookup = (new \NDISmate\Models\Invoice\NDIA\Remittance\CreateInvoiceNumberLookup)($invoices);

        $batch_payment_list = $this->getRemittanceBatchPaymentList();
        $batch_payments = (new \NDISmate\Models\Invoice\NDIA\Remittance\CreateBatchPayments)($xero, $batch_payment_list);

        return ['http_code' => 200, 'result' => $batch_payments];
    }

    function getRemittanceInvoiceList()
    {
        // I am ignoring one remittance due to negative amounts.
        $remittance_invoice_list = R::getAll(
            // "SELECT invoice_number, MIN(claim_date) AS claim_date
            // FROM ndiapaymentremittances
            // WHERE remittance_csv_name != 'fa0d0afe-dc20-1eed-b29f-b2d9f3bc0658.csv'
            // GROUP BY invoice_number
            // ORDER BY claim_date"
            "SELECT payment.invoice_number, MIN(payment.claim_date) AS claim_date
            FROM ndiapaymentremittances payment
            LEFT JOIN ndiaprocessedremittances processed ON payment.remittance_csv_name = processed.remittance_csv_name
            WHERE processed.remittance_csv_name IS NULL
                AND payment.remittance_csv_name != 'fa0d0afe-dc20-1eed-b29f-b2d9f3bc0658.csv'
            GROUP BY payment.invoice_number
            ORDER BY claim_date"
        );
        return $remittance_invoice_list;
    }

    function getRemittanceBatchPaymentList()
    {
        $remittance_batch_payment_list = R::getAll(
            // "SELECT remittance_csv_name, MIN(claim_date) AS claim_date
            // FROM ndiapaymentremittances
            // WHERE remittance_csv_name != 'fa0d0afe-dc20-1eed-b29f-b2d9f3bc0658.csv'
            // GROUP BY remittance_csv_name
            // ORDER BY claim_date"
            "SELECT payment.remittance_csv_name, MIN(payment.claim_date) AS claim_date
            FROM ndiapaymentremittances payment 
            LEFT JOIN ndiaprocessedremittances processed ON payment.remittance_csv_name = processed.remittance_csv_name
            WHERE processed.remittance_csv_name IS NULL
                AND payment.remittance_csv_name != 'fa0d0afe-dc20-1eed-b29f-b2d9f3bc0658.csv'
            GROUP BY payment.remittance_csv_name
            ORDER BY claim_date"
        );
        return $remittance_batch_payment_list;
    }
}
