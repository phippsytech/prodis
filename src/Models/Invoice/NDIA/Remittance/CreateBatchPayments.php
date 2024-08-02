<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class CreateBatchPayments
{
    function __invoke($xero, $batch_payment_list)
    {
        // Batch Payments
        $arr_batch_payments = [];
        foreach ($batch_payment_list as $item) {
            $batch_payment = (new \NDISmate\Models\Invoice\NDIA\Remittance\CreateBatchPayment)($xero, $item['remittance_csv_name'], $item['claim_date']);
            array_push($arr_batch_payments, $batch_payment);
            unset($invoice);
        }

        $batch_payments = new \XeroAPI\XeroPHP\Models\Accounting\BatchPayments;
        $batch_payments->setBatchPayments($arr_batch_payments);

        // # GO!
        try {
            $summarizeErrors = true;
            $apiResponse = $xero->accountingApi->createBatchPayment($xero->tenant_id, $batch_payments, $summarizeErrors);

            // once processed, store the remittance_csv_name in the ndiaprocessedremittances table
            // so we know which ones to skip in future.
            foreach ($batch_payment_list as $batch_payment) {
                $this->store_batch_payment($batch_payment);
            }

            return $apiResponse;
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            print_r($error);
            return ['http_code' => 400, 'error' => $error];
        }
    }

    function store_batch_payment($batch_payment)
    {
        $exists = R::findOne(
            'ndiaprocessedremittances',
            'remittance_csv_name=:remittance_csv_name',
            [':remittance_csv_name' => [$batch_payment['remittance_csv_name'], \PDO::PARAM_STR]]
        );
        if (!$exists) {
            $ndia_processed_remittance = R::dispense('ndiaprocessedremittances');
            $ndia_processed_remittance->remittance_csv_name = $batch_payment['remittance_csv_name'];
            R::store($ndia_processed_remittance);
        }
    }
}
