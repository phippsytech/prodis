<?php

/**
 * NOTE: STOP! THIS WAY MADNESS LIES...
 *
 * The problem with the Timesheet approach is that it is not possible to update a timesheet once it has been approved.
 * There is a topic in user voice about this: https://xero.uservoice.com/forums/250567-payroll-api/suggestions/4267147-be-able-to-overwrite-timesheets-via-the-api
 * It was originally raised in 2013 and is still not implemented.  I don't think it will ever be implemented because Xero is invested in XeroME.
 */

namespace NDISmate\Models\Payroll\Payrun\Timesheet;

use \RedBeanPHP\R as R;

class CreateTimesheetForStaffId
{
    function __invoke($xero, $data)
    {
        $earnings = (new \NDISmate\Models\Payroll\Payrun\GetEarnings)($data);

        $earnings = $this->reformatEarningsData($earnings, $data['start_date']);

        $pay_items = json_decode(file_get_contents(BASE_PATH . '/src/Models/SIL/Payrun/earning_items.json'), true);

        try {
            foreach ($earnings as $staffId => $shift_rates) {
                $staffer = R::getRow('SELECT xero_employee_ref, paygrade_id, employment_basis FROM staffs WHERE id=:staff_id', [':staff_id' => $data['staff_id']]);

                // if xero_employee_ref is empty or null skip this staff member
                if (empty($staffer['xero_employee_ref']))
                    continue;

                $payrun_timesheet = R::getRow('SELECT * FROM payruntimesheets WHERE staff_id=:staff_id AND payrun_xero_ref=:payrun_id', [
                    ':staff_id' => $data['staff_id'],
                    ':payrun_id' => $data['payrun_id']
                ]);

                // if a payrun_timesheet exists and it has a timesheet_xero_ref skip this staff member
                if ($payrun_timesheet)
                    continue;

                // if($payrun_timesheet){
                //     $timesheet = $xero->payrollAuApi->getTimesheet(
                //         xero_tenant_id: $xero->payroll_tenant_id,
                //         timesheet_id: $payrun_timesheet['timesheet_xero_ref'],
                //     );

                //     $timesheet= $timesheet->getTimesheet();
                //     $timesheet->setStatus("DRAFT"); // DRAFT, APPROVED, PROCESSED
                //     $timesheet->setTimeSheetLines(null);

                //     $timesheets[] = $timesheet;

                //     $apiResponse = $xero->payrollAuApi->updateTimesheet(
                //         xero_tenant_id: $xero->payroll_tenant_id,
                //         timesheet_id: $payrun_timesheet['timesheet_xero_ref'],
                //         timesheet: $timesheets,
                //     );

                //     return ["http_code"=>200, "result"=>$apiResponse];

                // }else{
                $timesheet = new \XeroAPI\XeroPHP\Models\PayrollAu\Timesheet();
                $timesheet->setEmployeeID($staffer['xero_employee_ref']);
                $timesheet->setStartDateAsDate(new \DateTime($data['start_date']));
                $timesheet->setEndDateAsDate(new \DateTime($data['end_date']));
                $timesheet->setStatus('APPROVED');  // DRAFT, APPROVED, PROCESSED
                // }

                // print_r($timesheet);

                foreach ($shift_rates as $shift_type => $numberOfUnits) {
                    // if shift type is not SLEEP and employment_basis is SALARY skip this shift
                    if ($shift_type != 'SLEEP' && $staffer['employment_basis'] == 'SALARY')
                        continue;

                    if ($shift_type == 'SLEEP') {
                        foreach ($numberOfUnits as $key => $value) {
                            if ($value != 0) {
                                $numberOfUnits[$key] = 1;
                            }
                        }
                    }

                    $timesheetLine = new \XeroAPI\XeroPHP\Models\PayrollAu\TimesheetLine();
                    $timesheetLine->setEarningsRateId($this->getEarningsRateId($staffer['paygrade_id'], $shift_type));
                    $timesheetLine->setNumberOfUnits($numberOfUnits);
                    $timesheetLines[] = $timesheetLine;
                }

                $timesheet->setTimeSheetLines($timesheetLines);
                unset($timesheetLines);

                $timesheets[] = $timesheet;
            }

            if ($payrun_timesheet)
                return ['http_code' => 400, 'error_message' => 'Timesheet exists'];

            // finally create the timesheet

            $apiResponse = $xero->payrollAuApi->createTimesheet(
                xero_tenant_id: $xero->payroll_tenant_id,
                timesheet: $timesheets,
            );

            // store reference to timesheet in database so it can be updated in future
            $payrun_timesheet = R::dispense('payruntimesheets');
            $payrun_timesheet->staff_id = $data['staff_id'];

            $payrun_timesheet->timesheet_xero_ref = $apiResponse[0]->getTimesheetId();
            $payrun_timesheet->payrun_xero_ref = $data['payrun_id'];
            R::store($payrun_timesheet);

            // }

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
