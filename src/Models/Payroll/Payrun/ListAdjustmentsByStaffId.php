<?php
namespace NDISmate\Models\Payroll\Payrun;

use \RedBeanPHP\R as R;

class ListAdjustmentsByStaffId
{
    var $pay_items;

    function __construct()
    {
        $pay_items = (new \NDISmate\Models\Payroll\ListPayItems)();

        $this->pay_items = [];

        foreach ($pay_items as $key => $pay_items) {
            foreach ($pay_items as $pay_item) {
                switch ($key) {
                    case 'EarningsRates':
                        $this->pay_items[$pay_item['EarningsRateID']] = [
                            'name' => $pay_item['Name'],
                            'rate' => $pay_item['RatePerUnit'],
                        ];
                        break;
                    case 'DeductionTypes':
                        $this->pay_items[$pay_item['DeductionTypeID']] = [
                            'name' => $pay_item['Name'],
                            'rate' => $pay_item['RatePerUnit'],
                        ];
                        break;
                    case 'LeaveTypes':
                        $this->pay_items[$pay_item['LeaveTypeID']] = [
                            'name' => $pay_item['Name'],
                            'rate' => $pay_item['RatePerUnit'],
                        ];
                        break;
                    case 'ReimbursementTypes':
                        $this->pay_items[$pay_item['ReimbursementTypeID']] = [
                            'name' => $pay_item['Name'],
                            'rate' => $pay_item['RatePerUnit'],
                        ];
                        break;
                }
            }
        }
    }

    function __invoke($data)
    {
        $adjustments = R::getAll('SELECT payrunadjustments.*, staffs.ordinary_hours_rate
        FROM payrunadjustments 
        JOIN staffs on staffs.id = payrunadjustments.staff_id
        WHERE staff_id = :staff_id
        ORDER BY payrunadjustments.id desc', [':staff_id' => $data['staff_id']]);

        if (!count($adjustments)) {
            return [];
        } else {
            foreach ($adjustments as $key => $adjustment) {
                // if ($adjustment['pay_item_xero_ref'] == '1c08b7a9-839a-4df5-b135-ae4d47139d34') {
                //     $earningsLine = [
                //         'earningsRateID' => $adjustment['pay_item_xero_ref'],
                //         'numberOfUnits' => $adjustment['quantity'],
                //         'ratePerUnit' => $adjustment['ordinary_hours_rate'],
                //     ];
                // } else {
                $earningsLine = [
                    'earningsRateID' => $adjustment['pay_item_xero_ref'],
                    'numberOfUnits' => $adjustment['quantity'],
                    'ratePerUnit' => $adjustment['rate'],
                ];
                // }

                $earningsLines[] = $earningsLine;
            }

            return $earningsLines;
        }
    }
}
