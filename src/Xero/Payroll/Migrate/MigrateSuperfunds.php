<?php
namespace NDISmate\Xero\Payroll\Migrate;

use \RedBeanPHP\R as R;

class MigrateSuperfunds
{
    function __invoke($xero)
    {
        // get the list of superfunds already in the target.
        $target_superfund_usis = [];
        $target_superfunds = $xero->payrollAuApi->getSuperfunds($xero->target_payroll_tenant_id);
        foreach ($target_superfunds as $target_superfund) {
            // if(!empty($target_superfund->getUsi())){
            $target_superfund_usis[$target_superfund->getName()] = $target_superfund->getSuperFundID();
            // }
        }

        // return ["http_code"=>200,"result"=>$target_superfund_usis];

        $source_superfunds = $xero->payrollAuApi->getSuperfunds($xero->source_payroll_tenant_id);

        $result = [];
        $errors = [];
        $the_superfunds = [];
        foreach ($source_superfunds as $source_superfund) {
            $source_superfund_name = $source_superfund->getName();
            $source_superfund_usi = $source_superfund->getUsi();
            $source_superfund_id = $source_superfund->getSuperFundID();

            echo $source_superfund_usi . "\n";

            try {
                // If the superfund does not exist, add it to the target.
                if (empty($target_superfund_usis[$source_superfund_name])) {
                    $the_superfund = $xero->payrollAuApi->getSuperfund($xero->source_payroll_tenant_id, $source_superfund_id);
                    $the_superfund[0]->setSuperFundID(null);
                    $the_superfund[0]->setUpdatedDateUtc(null);

                    $apiresult = $xero->payrollAuApi->createSuperfund($xero->target_payroll_tenant_id, [$the_superfund[0]]);
                    $result[] = $apiresult;
                    $target_superfund_id = $apiresult[0]->getSuperFundID();
                } else {
                    $target_superfund_id = $target_superfund_usis[$source_superfund_name];
                }

                $existingSuperfund = R::findOne('migratedsuperfunds', 'source_superfund_ref = ?', [$source_superfund_id]);
                if (!$existingSuperfund) {
                    // Create a new bean
                    $migratedSuperfund = R::dispense('migratedsuperfunds');
                    $migratedSuperfund->source_superfund_ref = $source_superfund_id;
                    $migratedSuperfund->target_superfund_ref = $target_superfund_id;
                    R::store($migratedSuperfund);
                }
            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                $errors[] = json_decode($e->getResponseBody(), true);
            }

            sleep(1);
        }

        return ['http_code' => 200, 'result' => $result, 'errors' => $errors];
    }
}
