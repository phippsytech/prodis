<?php
namespace NDISmate\Models\Payroll\Leave\LeaveApplication;

use \RedBeanPHP\R as R;

class CreateLeaveApplication
{
    // https://xeroapi.github.io/xero-php-oauth2/docs/v2/payroll_au/index.html#api-PayrollAu-createLeaveApplication
    // https://developer.xero.com/documentation/api/payrollau/leaveapplications

    function __invoke($xero, $data)
    {
        $xero_employee_id = '7c29ee7d-2c5d-456d-b1e8-399b14659e9f';

        // I'm currently collecting "annual" or "personal".
        // I also have an "unpaid" flag

        // Because there are two types of annual leave, I need to work out which one to use.
        // If the staff member has the 5 weeks annual leave in their leave balance, that is the one I need should use.
        $xero_leave_type_id = '844bf41e-9efb-4dfd-88f9-cbe015104e8c';

        $start_date = '2023-12-04';
        $end_date = '2023-12-04';

        // $startDate = new \DateTime($start_date);
        // $endDate = new \DateTime($end_date);

        $leaveApplication = new \XeroAPI\XeroPHP\Models\PayrollAu\LeaveApplication;
        $leaveApplication->setEmployeeId($xero_employee_id);
        $leaveApplication->setLeaveTypeId($xero_leave_type_id);

        $leaveApplication->setTitle('Annual Leave - TEST');
        $leaveApplication->setStartDate(new \DateTime($start_date));
        $leaveApplication->setEndDate(new \DateTime($end_date));
        // $leaveApplication->setDescription();
        $leaveApplication->setPayOutType('DEFAULT');  // is there a xero constant for this?

        $leave_period = new \XeroAPI\XeroPHP\Models\PayrollAu\LeavePeriod;

        // $givenDate = '2023-12-02'; // Any date
        $fortnight = (new \NDISmate\Models\Payroll\GetPayFortnight)($start_date);
        $pay_period_start_date = new \DateTime($fortnight['start_date']);
        $pay_period_end_date = new \DateTime($fortnight['end_date']);
        $leave_period->setPayPeriodEndDate($pay_period_end_date);
        $leave_period->setPayPeriodStartDate($pay_period_start_date);

        $leave_period->setNumberOfUnits(4);
        $leave_period->setLeavePeriodStatus('SCHEDULED');

        $leave_periods[] = $leave_period;

        $leaveApplication->setLeavePeriods($leave_periods);

        $leave_applications[] = $leaveApplication;

        try {
            $apiResponse = $xero->payrollAuApi->createLeaveApplication(
                xero_tenant_id: $xero->payroll_tenant_id,
                leave_application: $leave_applications,
                // idempotency_key: null
            );

            return ['http_code' => 200, 'result' => $apiResponse];
        } catch (\Exception $e) {
            print_r($e);
            $error = $e->getMessage();
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}

// {
//     "result": {
//       "LeaveTypes": [
//         {
//           "Name": "Annual Leave",
//           "LeaveTypeID": "68c25d64-85ae-46b8-a522-07eb61f2c53e"
//         },
//         {
//           "Name": "Long Service Leave",
//           "LeaveTypeID": "7e7ff6be-d315-451d-8439-2dd4e0150524"
//         },
//         {
//           "Name": "Parental Leave (unpaid)",
//           "LeaveTypeID": "29772819-c355-4f6c-9f9e-e8bf9daf8c90"
//         },
//         {
//           "Name": "Personal/Carer's Leave",
//           "LeaveTypeID": "64c40e5d-523d-4401-8789-86e4e1d541dc"
//         },
//         {
//           "Name": "Personal/Carer's Leave (unpaid)",
//           "LeaveTypeID": "92c3bbc6-92c2-40b6-8a3d-f216a8295891"
//         },
//         {
//           "Name": "Compassionate Leave (paid)",
//           "LeaveTypeID": "9e89a441-a4de-49f7-a8f9-4b0c81d3fcc3"
//         },
//         {
//           "Name": "Compassionate Leave (unpaid)",
//           "LeaveTypeID": "2521e747-1a19-4305-ab92-ecc938fd314b"
//         },
//         {
//           "Name": "Community Service Leave",
//           "LeaveTypeID": "a7e6771e-d217-4e15-81d2-76a20193bd7d"
//         },
//         {
//           "Name": "Other Unpaid Leave",
//           "LeaveTypeID": "bf5a9d1c-fed8-42d1-a5b2-de280018cf0f"
//         },
//         {
//           "Name": "Annual Leave (unpaid)",
//           "LeaveTypeID": "844bf41e-9efb-4dfd-88f9-cbe015104e8c"
//         },
//         {
//           "Name": "Annual Leave 5 Weeks",
//           "LeaveTypeID": "15e8e825-7d19-426e-8d8f-3eea265daa29"
//         },
//         {
//           "Name": "Compassionate Leave (paid)",
//           "LeaveTypeID": "395d2c39-88a2-4bbb-8930-a5422c3d5894"
//         }
//       ]
//     }
//   }
