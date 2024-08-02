<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class CreateBatchPayment
{
    // var $xero_bank_account_id="13918178-849a-4823-9a31-57b7eac713d7"; // "Business Bank Account" in Demo Company
    // var $xero_bank_account_id="09684963-a191-4bc5-b4e8-06a3b8eb75d0"; // "Operations Account" in Crystel Care
    var $xero_bank_account_id = '656240fe-0be4-4db1-b22c-c6281d00f91e';  // "ANZ Operations #9434" in Crystel Care (NDIS Goes here from 31 Aug 2023)

    function __invoke($xero, $remittance_csv_name, $claim_date)
    {
        $payment_date = (new \DateTime(date('Y-m-d', strtotime($claim_date . '+1 days'))));

        // Bank
        $bankAccount = new \XeroAPI\XeroPHP\Models\Accounting\Account;
        $bankAccount->setAccountID($this->xero_bank_account_id);

        // Batch Payment
        $batch_payment = new \XeroAPI\XeroPHP\Models\Accounting\BatchPayment;
        $batch_payment->setAccount($bankAccount);
        $batch_payment->setReference('NDIA Batch Payment');
        $batch_payment->setDate($payment_date);

        $invoice_payments = $this->getInvoicePayments($remittance_csv_name);

        $payments = [];
        foreach ($invoice_payments as $invoice_payment) {
            $invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
            $invoice->setInvoiceID($this->getInvoiceIdFromInvoiceNumber($invoice_payment['invoice_number']));
            $payment = new \XeroAPI\XeroPHP\Models\Accounting\Payment;
            $payment->setDate($payment_date);
            $payment->setAmount($invoice_payment['amount_paid']);
            $payment->setInvoice($invoice);
            array_push($payments, $payment);
        }

        $batch_payment->setPayments($payments);

        return $batch_payment;
    }

    function getInvoicePayments($remittance_csv_name)
    {
        $invoice_payments = R::getAll(
            'SELECT invoice_number, SUM(amount_paid) as amount_paid
            FROM ndiapaymentremittances 
            WHERE remittance_csv_name=:remittance_csv_name 
            GROUP BY invoice_number',
            [
                ':remittance_csv_name' => $remittance_csv_name,
            ]
        );
        return $invoice_payments;
    }

    function getInvoiceIdFromInvoiceNumber($invoice_number)
    {
        $xero_invoice_number_lookup = R::findOne(
            'xeroinvoicenumberlookup',
            'invoice_number=:invoice_number',
            [':invoice_number' => [$invoice_number, \PDO::PARAM_STR]]
        );
        return $xero_invoice_number_lookup->invoice_ref;
    }
}
