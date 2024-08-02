<?php
namespace NDISmate\Xero\Payroll\Migrate;

use \RedBeanPHP\R as R;

class MigratePayItems
{
    function __invoke($xero)
    {
        // try {

        // SOURCE
        $source_pay_items = $xero->payrollAuApi->getPayItems($xero->source_payroll_tenant_id);

        $source_earnings_rates = $source_pay_items->getPayItems()->getEarningsRates();

        $target_earnings_rates = [];

        $accepted_earnings_types = [
            'ORDINARYTIMEEARNINGS',
            'OVERTIMEEARNINGS',
            'BONUSESANDCOMMISSIONS',
            'ALLOWANCE'
        ];

        foreach ($source_earnings_rates as $source_earnings_rate) {
            var_dump($source_earnings_rate);
            if (
                $source_earnings_rate->getCurrentRecord() == true &&
                in_array($source_earnings_rate->getEarningsType(), $accepted_earnings_types)
            ) {
                $source_earnings_rate->setEarningsRateID(null);
                $source_earnings_rate->setAccountCode('477');
                $source_earnings_rate->setUpdatedDateUtc(null);
                $source_earnings_rate->setCurrentRecord(null);

                array_push($target_earnings_rates, $source_earnings_rate);
            }
        }

        return ['http_code' => 200];

        $target_pay_item = new \XeroAPI\XeroPHP\Models\PayrollAu\PayItem;
        $target_pay_item->setEarningsRates($target_earnings_rates);
        try {
            $xero->payrollAuApi->createPayItem($xero->target_payroll_tenant_id, $target_pay_item);
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errors[] = json_decode($e->getResponseBody(), true);
        }

        sleep(1);  // avoid spamming the api

        return ['http_code' => 200, 'result' => $result, 'errors' => $errors];
        // return ["http_code"=>200,"result"=>$result];

        // } catch (\XeroAPI\XeroPHP\ApiException $e) {
        //     $error = json_decode($e->getResponseBody(), true);
        //     return ["http_code"=>400, "error"=>$error];
        // }
    }
}
