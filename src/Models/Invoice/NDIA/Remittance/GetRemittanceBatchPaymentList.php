<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class GetRemittanceBatchPaymentList
{
    function __invoke()
    {
        $remittance_batch_payment_list = R::getAll(
            // "SELECT remittance_csv_name, MIN(claim_date) AS claim_date, SUM(amount_paid) as amount_paid
            // FROM ndiapaymentremittances
            // WHERE remittance_csv_name != 'fa0d0afe-dc20-1eed-b29f-b2d9f3bc0658.csv'
            // GROUP BY remittance_csv_name
            // ORDER BY claim_date"
            "SELECT payment.remittance_csv_name, MIN(payment.claim_date) AS claim_date, SUM(payment.amount_paid) as amount_paid
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
