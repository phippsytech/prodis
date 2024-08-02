<?php
namespace NDISmate\Models\Payroll\Payrun;

use \RedBeanPHP\R as R;

class CreateEmployeeEarnings
{
    function __invoke($data)
    {
        $staff_id = $data['staff_id'];

        $earnings = (new \NDISmate\Models\Payroll\Payrun\GetEarnings)($data);

        // return ["http_code"=>200, "result"=>$earnings];

        $earnings = $this->reformatEarningsData($earnings);

        // return ["http_code"=>200, "result"=>$earnings];

        $staffer = R::getRow('SELECT xero_employee_ref, paygrade_id FROM staffs WHERE id=:staff_id', [':staff_id' => $staff_id]);

        // if xero_employee_ref is empty or null skip this staff member
        if (empty($staffer['xero_employee_ref']))
            return ['http_code' => 400, 'error' => 'No Xero Employee Ref'];

        $paygrade_id = $staffer['paygrade_id'];

        foreach ($earnings as $shift_type => $numberOfUnits) {
            $earningsLine = [
                'StaffID' => $staff_id,
                'EmployeeID' => $staffer['xero_employee_ref'],
                'shift_type' => $shift_type,
                'earningsRateID' => $this->getEarningsRateId($paygrade_id, $shift_type),
                'numberOfUnits' => $numberOfUnits
            ];

            $earningsLines[] = $earningsLine;
        }

        $stuff[] = $earningsLines;

        return ['http_code' => 200, 'stuff' => $stuff];
        // return ["http_code"=>200, "result"=>$timesheets];

        try {
            // finally create the timesheet
            $apiResponse = $xero->payrollAuApi->createTimesheet($xero->payroll_tenant_id, $timesheets);

            return ['http_code' => 200, 'result' => $apiResponse];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }

    function getEarningsRateId($paygrade_id, $shift_type)
    {
        $xeropaygrade = R::getRow('SELECT sleep_over, public_holiday, sunday, saturday, night, evening, `day` FROM xeropaygrades WHERE id=:paygrade_id', [':paygrade_id' => $paygrade_id]);

        switch ($shift_type) {
            case 'SLEEP':
                return $xeropaygrade['sleep_over'];
            case 'PUB':
                return $xeropaygrade['public_holiday'];
            case 'SUN':
                return $xeropaygrade['sunday'];
            case 'SAT':
                return $xeropaygrade['saturday'];
            case 'NIGHT':
                return $xeropaygrade['night'];
            case 'EVE':
                return $xeropaygrade['evening'];
            case 'DAY':
                return $xeropaygrade['day'];
        }
    }

    function reformatEarningsData($earnings)
    {
        $result = [];

        foreach ($earnings as $row) {
            $staffId = $row['staff_id'];
            $shift_type = $row['shift_type'];

            $duration = $row['duration'] / 60;

            // Passive duration is always 1 hour
            if ($shift_type == 'SLEEP') {
                $duration = 1;
            }

            $result[$shift_type] += $duration;
        }
        return $result;
    }
}
