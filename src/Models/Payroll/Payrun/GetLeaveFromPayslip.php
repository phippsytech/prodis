<?php
namespace NDISmate\Models\Payroll\Payrun;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetLeaveFromPayslip
{
    function __invoke($data)
    {
        // right now, hardcode any rate less than  20 is the leave loading, and it should not be counted.

        // if there are no leave lines return 0
        if (!isset($data->LeaveEarningsLines))
            return 0;

        // otherwise get the leave lines
        $leave_earnings_lines = $data->LeaveEarningsLines;

        $leave = 0;

        foreach ($leave_earnings_lines as $leave_earnings_line) {
            if ($leave_earnings_line->RatePerUnit > 0 && $leave_earnings_line->RatePerUnit < 20)
                continue;
            $leave += $leave_earnings_line->NumberOfUnits;
        }

        return $leave;
    }
}
