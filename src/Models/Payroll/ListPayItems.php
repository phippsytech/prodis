<?php
namespace NDISmate\Models\Payroll;

use \RedBeanPHP\R as R;

class ListPayItems
{
    private $payItems;

    function __invoke()
    {
        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems');

        // Parse the JSON data into a PHP array
        $payItemsData = json_decode($jsonData, true);

        $this->payItems = [];

        foreach ($payItemsData as $itemType => $payItems) {
            foreach ($payItems as $payItem) {
                switch ($itemType) {
                    case 'EarningsRates':
                        $this->payItems[] = [
                            'payItemName' => $payItem['Name'],
                            'payItemXeroRef' => $payItem['EarningsRateID'],
                            'payItemType' => 'earnings'
                        ];
                        break;

                    case 'DeductionTypes':
                        $this->payItems[] = [
                            'payItemName' => $payItem['Name'],
                            'payItemXeroRef' => $payItem['DeductionTypeID'],
                            'payItemType' => 'deductions'
                        ];
                        break;

                    case 'ReimbursementTypes':
                        $this->payItems[] = [
                            'payItemName' => $payItem['Name'],
                            'payItemXeroRef' => $payItem['ReimbursementTypeID'],
                            'payItemType' => 'reimbursement'
                        ];
                        break;
                }
            }
        }

        return $this->payItems;
    }
}
