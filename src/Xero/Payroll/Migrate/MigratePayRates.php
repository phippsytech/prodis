<?php
namespace NDISmate\Xero\Payroll\Migrate;

use \RedBeanPHP\R as R;

class MigratePayRates
{
    var $rate_names = [
        'DAY' => 'Weekday Day',
        'SAT' => 'Saturday',
        'SUN' => 'Sunday',
        'PUB' => 'Public Holiday',
        'EVE' => 'Weekday Evening',
        'NIGHT' => 'Weekday Night',
        'SLEEP' => 'Night-Time Sleepover'
    ];

    function __invoke($xero)
    {
        $target_earnings_rates = [];

        $data = file_get_contents(__DIR__ . '/Payrates/casual.json');
        $pay_rates = json_decode($data, true);
        $target_earnings_rates = $this->getPayRates($pay_rates, 'Casual', $target_earnings_rates);

        $data = file_get_contents(__DIR__ . '/Payrates/ppt.json');
        $pay_rates = json_decode($data, true);
        $target_earnings_rates = $this->getPayRates($pay_rates, 'Full/Part-time', $target_earnings_rates);

        $data = file_get_contents(__DIR__ . '/Payrates/management.json');
        $pay_rates = json_decode($data, true);
        $target_earnings_rates = $this->getPayRates($pay_rates, 'Management', $target_earnings_rates);

        // return ["http_code"=>200, "result"=>$target_earnings_rates];

        $target_pay_item = new \XeroAPI\XeroPHP\Models\PayrollAu\PayItem;
        $target_pay_item->setEarningsRates($target_earnings_rates);
        try {
            $xero->payrollAuApi->createPayItem($xero->target_payroll_tenant_id, $target_pay_item);
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errors[] = json_decode($e->getResponseBody(), true);
        }

        sleep(1);  // avoid spamming the api

        return ['http_code' => 200, 'result' => $result, 'errors' => $errors];
    }

    function getPayRates($pay_rates, $rate_type, $target_earnings_rates)
    {
        foreach ($pay_rates as $rate_name => $levels) {
            foreach ($levels as $level => $pay_rate) {
                $earnings_rate_name = 'SIL ' . $rate_type . ' L' . $level . ' ' . $this->rate_names[$rate_name];

                // array_push($target_earnings_rates, $earnings_rate_name);

                $earningsRate = new \XeroAPI\XeroPHP\Models\PayrollAu\EarningsRate;
                $earningsRate->setName($earnings_rate_name);
                $earningsRate->setAccountCode('477');  // 477 is the default for Default Company.  470 for ADS
                $earningsRate->setTypeOfUnits('Hours');
                $earningsRate->setEarningsType(\XeroAPI\XeroPHP\Models\PayrollAu\EarningsType::ORDINARYTIMEEARNINGS);
                $earningsRate->setRateType(\XeroAPI\XeroPHP\Models\PayrollAu\RateType::RATEPERUNIT);
                $earningsRate->setRatePerUnit($pay_rate);
                $earningsRate->setIsReportableAsW1(true);
                $earningsRate->setIsExemptFromTax(false);
                $earningsRate->setIsExemptFromSuper(false);

                array_push($target_earnings_rates, $earningsRate);
            }
        }
        return $target_earnings_rates;
    }
}
