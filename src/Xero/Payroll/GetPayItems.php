<?php
namespace NDISmate\Xero\Payroll;

use \RedBeanPHP\R as R;


class GetPayItems {

    function __invoke($xero){

        try {
            $pay_items = $xero->payrollAuApi->getPayItems($xero->payroll_tenant_id);

            $earnings_rates= $pay_items->getPayItems()->getEarningsRates();
            foreach($earnings_rates as $earnings_rate){
                /*
                    "name": "Ordinary Hours",
                    "account_code": "41015",
                    "type_of_units": "Hours",
                    "is_exempt_from_tax": false,
                    "is_exempt_from_super": false,
                    "is_reportable_as_w1": true,
                    "allowance_contributes_to_annual_leave_rate": null,
                    "allowance_contributes_to_overtime_rate": null,
                    "earnings_type": "ORDINARYTIMEEARNINGS",
                    "earnings_rate_id": "fea6f6d7-f11c-4ad3-9c89-921733914298",
                    "rate_type": "RATEPERUNIT",
                    "rate_per_unit": null,
                    "multiplier": null,
                    "accrue_leave": null,
                    "amount": null,
                    "employment_termination_payment_type": null,
                    "updated_date_utc": "/Date(1682997232000+0000)/",
                    "current_record": true,
                    "allowance_type": null,
                    "allowance_category": null
                 */
                if($earnings_rate->getCurrentRecord() == true){
                    $apiResponse['earnings_rates'][] = (Array) $earnings_rate;
                } 
            }

            $deduction_types= $pay_items->getPayItems()->getDeductionTypes();
            foreach($deduction_types as $deduction_type){
                /*
                    "name": "Union Fees/Subscriptions",
                    "account_code": "850",
                    "reduces_tax": false,
                    "reduces_super": false,
                    "is_exempt_from_w1": false,
                    "deduction_type_id": "e2df841c-9033-4f56-83af-349e60e2ea2f",
                    "updated_date_utc": "/Date(1665370389000+0000)/",
                    "deduction_category": "UNIONFEES",
                    "current_record": true
                */
                if($deduction_type->getCurrentRecord() == true) $apiResponse['deduction_types'][] = (Array) $deduction_type;
            }

            $leave_types= $pay_items->getPayItems()->getLeaveTypes();
            foreach($leave_types as $leave_type){
                /*
                    "name": "Annual Leave",
                    "type_of_units": "Hours",
                    "leave_type_id": "25598515-634b-493b-8b4e-d59ae7677c02",
                    "normal_entitlement": 152,
                    "leave_loading_rate": null,
                    "updated_date_utc": "/Date(1665370389000+0000)/",
                    "is_paid_leave": true,
                    "show_on_payslip": true,
                    "current_record": true,
                    "leave_category_code": "ANNUALLEAVE",
                    "sgc_exempt": false
                */
                if($leave_type->getCurrentRecord() == true) $apiResponse['leave_types'][] = (Array) $leave_type;
            }

            $reimbursement_types= $pay_items->getPayItems()->getReimbursementTypes();
            foreach($reimbursement_types as $reimbursement_type){
                /*
                    "name": "Travel Costs",
                    "account_code": "850",
                    "reimbursement_type_id": "e7a2bc58-6105-46a8-bb08-43af65533f44",
                    "updated_date_utc": "/Date(1665370389000+0000)/",
                    "current_record": true
                */
                if($reimbursement_type->getCurrentRecord() == true) $apiResponse['reimbursement_types'][] = (Array) $reimbursement_type;
            }


            return ["http_code"=>200,"result"=>$apiResponse];    

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }
}
        