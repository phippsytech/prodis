<?php
namespace NDISmate\Models\SIL\Payrun;

use \RedBeanPHP\R as R;

// Payroll tracking in Xero
// https://central.xero.com/s/article/Payroll-tracking-in-Xero

/*
 * The above link talks about how to assign a tracking category to a timesheet line item.
 *
 * I think to be truly useful, we need to generate the earnings items rather than even use timesheets.
 */

class CreateTimesheet
{
    function __invoke($xero, $data)
    {
        $earnings = (new \NDISmate\Models\SIL\Payrun\GetEarnings)($data);

        // return ["http_code"=>200, "result"=>$earnings];

        $earnings = $this->reformatEarningsData($earnings, $data['start_date']);

        // return ["http_code"=>200, "result"=>$earnings];

        $pay_items = json_decode(file_get_contents(BASE_PATH . '/src/Models/SIL/Payrun/earning_items.json'), true);

        try {
            foreach ($earnings as $staffId => $shift_rates) {
                $staffer = R::getRow('SELECT staffs.xero_employee_ref, staffs.level, staffs.paygrade_id, clientstaffs.house_lead FROM staffs join clientstaffs on (staffs.id=clientstaffs.staff_id) WHERE staffs.id=:staff_id', [':staff_id' => $staffId]);

                // if xero_employee_ref is empty or null skip this staff member
                if (empty($staffer['xero_employee_ref']))
                    continue;

                // Skip Melvin Diggs
                // if ($staffer['xero_employee_ref']!="3bfd3755-6543-49bc-8483-cc4be1669c41") continue;

                // print ($staffer['xero_employee_ref']).PHP_EOL;

                // // if employment_type is contractor skip this staff member
                // if($staffer['employment_type']=="contractor") continue;

                $timesheet = new \XeroAPI\XeroPHP\Models\PayrollAu\Timesheet();
                $timesheet->setEmployeeID($staffer['xero_employee_ref']);

                $timesheet->setStartDateAsDate(new \DateTime($data['start_date']));
                $timesheet->setEndDateAsDate(new \DateTime($data['end_date']));
                $timesheet->setStatus('APPROVED');  // DRAFT, APPROVED, PROCESSED

                foreach ($shift_rates as $shift_type => $numberOfUnits) {
                    // I don't think I need this
                    // if ($shift_type == 'TRAVEL') continue;

                    // This house lead check is flawed
                    if ($staffer['house_lead'] == 1 and $shift_type != 'SLEEP')
                        continue;

                    if ($shift_type == 'SLEEP') {
                        foreach ($numberOfUnits as $key => $value) {
                            if ($value != 0) {
                                $numberOfUnits[$key] = 1;
                            }
                        }
                    }

                    // $earningsLine=[
                    //     "StaffID"=>$staffId,
                    //     "EmployeeID"=>$staffer['xero_employee_ref'],
                    //     "shift_type"=>$pay_items[$shift_type][$staffer['level']],

                    //     "StartDateAsDate"=>(new \DateTime($data['start_date'])),
                    //     "EndDateAsDate"=>(new \DateTime($data['end_date'])),
                    //     "earningsRateID" => $pay_items[$shift_type][$staffer['level']],
                    //     "numberOfUnits" => $numberOfUnits
                    // ];

                    // print "JSONRATE:".$shift_type.":".$pay_items[$shift_type][$staffer['level']].PHP_EOL;
                    // print "MYSQLRATE:".$shift_type.":".$this->getEarningsRateId($staffer['paygrade_id'], $shift_type).PHP_EOL.PHP_EOL;
                    $timesheetLine = new \XeroAPI\XeroPHP\Models\PayrollAu\TimesheetLine();
                    // $timesheetLine->setEarningsRateId($pay_items[$shift_type][$staffer['level']]);
                    $timesheetLine->setEarningsRateId($this->getEarningsRateId($staffer['paygrade_id'], $shift_type));
                    $timesheetLine->setNumberOfUnits($numberOfUnits);
                    $timesheetLines[] = $timesheetLine;

                    $earningsLines[] = $earningsLine;
                }

                $timesheet->setTimeSheetLines($timesheetLines);
                unset($timesheetLines);

                $timesheets[] = $timesheet;

                // $stuff[]=$earningsLines;
            }

            // return ["http_code"=>200, "stuff"=>$stuff];
            // return ["http_code"=>200, "result"=>$timesheets];

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

    function reformatEarningsData($earnings, $period_start)
    {
        $result = [];

        $startDate = new \DateTime($period_start);

        foreach ($earnings as $row) {
            $staffId = $row['staff_id'];
            $shift_type = $row['shift_type'];
            // $startDate = date('N', strtotime($row['start_date'])) - 1;
            // $startDate = (date('N', strtotime($row['start_date'])) - 1) % 14;
            $shift_date = new \DateTime($row['start_date']);
            $interval = $startDate->diff($shift_date);
            $pos = $interval->days;
            $duration = $row['duration'] / 60;

            if (!isset($result[$staffId])) {
                $result[$staffId] = [];
            }

            if (!isset($result[$staffId][$shift_type])) {
                $result[$staffId][$shift_type] = [];

                // Initialize the array with 14 rows
                for ($i = 0; $i < 14; $i++) {
                    $result[$staffId][$shift_type][$i] += ($i === $pos) ? $duration : 0;
                }
            } else {
                // Update the existing array row with the duration
                if ($pos < 14) {
                    $result[$staffId][$shift_type][$pos] += $duration;
                }
            }
        }
        return $result;
    }
}
