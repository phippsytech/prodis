<?php
namespace NDISmate\Xero\Payroll\Migrate;

use \RedBeanPHP\R as R;


class MigrateEmployees {

    function __invoke($xero){

        // get the list of superfunds already in the target.
        $target_superfund_usis=[];
        $target_superfunds = $xero->payrollAuApi->getSuperfunds($xero->target_payroll_tenant_id);
        foreach ($target_superfunds as $target_superfund){
            $target_superfund_usis[$target_superfund->getName()]=$target_superfund->getSuperFundID();
        }
        // return ["http_code"=>200, "results"=>$target_superfund_usis];


        $where = 'Status=="ACTIVE"';
        
        $apiResponse = $xero->payrollAuApi->getEmployees($xero->source_payroll_tenant_id, null, $where);

        foreach($apiResponse->getEmployees() as $employees){

            $employee = $xero->payrollAuApi->getEmployee($xero->source_payroll_tenant_id, $employees->getEmployeeId());

            foreach($employee as $ekey=>$evalue){

                $super_memberships=$employee[$ekey]->getSuperMemberships();

                foreach($super_memberships as $skey=>$svalue){
                    
                    $target_superfund_ref = R::getCell('SELECT target_superfund_ref FROM migratedsuperfunds WHERE source_superfund_ref = ?', [$super_memberships[$skey]->getSuperFundID()]);
                    
                    $super_memberships[$skey]->setSuperMembershipID(null);
                    $super_memberships[$skey]->setSuperFundID($target_superfund_ref); //
                }
                
            }


            $employee[$ekey]->setOrdinaryEarningsRateID(null); // I think I want to actually set this rather than leave it null



            $employee[$ekey]->setSuperMemberships($super_memberships);
            $employee[$ekey]->setEmployeeID(null);
            $employee[$ekey]->setUpdatedDateUtc(null);
            $employee[$ekey]->setIsAuthorisedToApproveTimesheets(false);
            $employee[$ekey]->setIsAuthorisedToApproveLeave(false);
            $employee[$ekey]->setPayrollCalendarID(null);
            
            $employee[$ekey]->setStartDate(null);


            $employee[$ekey]->setPayTemplate(null);
            $employee[$ekey]->setOpeningBalances(null);
            // $employee[$ekey]->setTaxDeclaration();
            // $employee[$ekey]->setIncomeType();
            // $employee[$ekey]->setEmploymentType();
            // $employee[$ekey]->setCountryOfResidence();
            $employee[$ekey]->setIsStp2Qualified(true);
            $employee[$ekey]->setLeaveBalances(null);
            $employee[$ekey]->setLeaveLines(null);
            // $employee[$ekey]->setSuperMemberships();
            // $employee[$ekey]->setStatus();
            // $employee[$ekey]->setUpdatedDateUtc();
            // $employee[$ekey]->setValidationError();
            
            // print_r($employee[$ekey]);

            try {
                $results[] = $xero->payrollAuApi->createEmployee($xero->target_payroll_tenant_id, [$employee[$ekey]]);
            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                $errors[] = json_decode($e->getResponseBody(), true);
            }
            sleep(1);
            // break;
        }
        
        return ["http_code"=>200, "results"=>$results, "errors"=>$errors];
        
    }

}
