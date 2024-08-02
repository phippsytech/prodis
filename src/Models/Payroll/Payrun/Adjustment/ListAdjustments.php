<?php
namespace NDISmate\Models\Payroll\Payrun\Adjustment;

use \RedBeanPHP\R as R;

class ListAdjustments
{
    var $pay_items;

    function __construct()
    {
        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems');

        // Parse the JSON data into a PHP array
        $pay_items = json_decode($jsonData, true);

        $this->pay_items = [];

        foreach ($pay_items as $key => $pay_items) {
            // print($key);
            foreach ($pay_items as $pay_item) {
                switch ($key) {
                    case 'EarningsRates':
                        $this->pay_items[$pay_item['EarningsRateID']] = $pay_item['Name'];
                        break;

                    case 'DeductionTypes':
                        $this->pay_items[$pay_item['DeductionTypeID']] = $pay_item['Name'];
                        break;

                    case 'LeaveTypes':
                        $this->pay_items[$pay_item['LeaveTypeID']] = $pay_item['Name'];
                        break;

                    case 'ReimbursementTypes':
                        $this->pay_items[$pay_item['ReimbursementTypeID']] = $pay_item['Name'];
                        break;

                        // $this->pay_items[$pay_item['PayItemID']] = $pay_item['Name'];
                }
            }
        }
    }

    function __invoke($data)
    {
        $adjustments = R::getAll('SELECT payrunadjustments.*, staffs.name as staff_name 
        FROM payrunadjustments 
        JOIN staffs ON staffs.id = payrunadjustments.staff_id 
        ORDER BY staffs.name, payrunadjustments.id desc');

        if (!count($adjustments)) {
            return ['http_code' => 404, 'error_message' => 'Not Found'];
        } else {
            foreach ($adjustments as $key => $adjustment) {
                $adjustments[$key]['pay_item_name'] = $this->pay_items[$adjustment['pay_item_xero_ref']];
            }

            return ['http_code' => 200, 'result' => $adjustments];  // R::exportAll($beans, TRUE)
        }
    }
}
