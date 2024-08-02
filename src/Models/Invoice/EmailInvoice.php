<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

/**
 * Don't bother sending the invoice using Xero's API.
 * Just download the invoice as a PDF and send it as an attachment via SES
 *
 * It costs 1 API call regardless, so the Xero cost is no different.
 * The advantage is we have more flexibility over who the invoice is sent to
 * without resorting to manual handling.
 *
 * My only concern is deliverability of invoices when using SES.
 */

// Get the invoice as a pdf:
// https://xeroapi.github.io/xero-php-oauth2/docs/v2/accounting/index.html#api-Accounting-getInvoiceAsPdf

// You'll want an online invoice link to send with the attachment.
// https://xeroapi.github.io/xero-php-oauth2/docs/v2/accounting/index.html#api-Accounting-getOnlineInvoice

/**
 * NOTE:
 *
 * There is a daily limit per Xero organisation for sending invoices of 1000 per day for paying organisations.
 *
 * src: https://developer.xero.com/documentation/api/accounting/invoices#emailing-an-invoice
 */
class EmailInvoice
{
    public function __invoke($data)
    {
        $xero = new \NDISmate\Models\Invoice\NDIA\Remittance\Xero();
        $requestEmpty = new XeroAPI\XeroPHP\Models\Accounting\RequestEmpty;
        try {
            $result = $xero->accountingApi->emailInvoice(
                xero_tenant_id: $xero->tenant_id,
                invoice_id: $data['invoice_id'],
                request_empty: $requestEmpty,
            );
            return ['http_code' => 200];  // Note, a successful response is actually 204.  I'm returning 200 for now.
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
