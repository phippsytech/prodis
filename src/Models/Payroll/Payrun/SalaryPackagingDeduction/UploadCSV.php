<?php
namespace NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction;

use \RedBeanPHP\R as R;

class UploadCSV
{
    public function __invoke($obj, $file)
    {
        $deduction_csv_name = $file['name'];
        $payrun_id = $file['payrun_id'];

        if (empty($deduction_csv_name)) {
            return ['http_code' => 400, 'error_message' => 'No file name provided'];
        }

        $beans = R::find('salarypackagingdeductions', 'deduction_csv_name=:deduction_csv_name', [':deduction_csv_name' => $deduction_csv_name]);

        if (count($beans) != 0) {
            return ['http_code' => 400, 'error_message' => 'This deduction advice has already been uploaded'];
        }

        $binary = $this->getBinary($file['data']);

        // Read the csv string and convert it into an array
        $csv = array_map('str_getcsv', explode("\n", $binary));
        $header = array_shift($csv);

        $expected_order = ['NCB', 'FullName', 'TotalAmount', 'AccountType', 'Status', 'PID'];

        if (!$this->array_contains_keys_in_order($header, $expected_order)) {
            return ['http_code' => 400, 'error_message' => 'CSV file does not match expected format', 'header' => $header, 'expected_order' => $expected_order];
        }

        foreach ($csv as $row) {
            if (count($row) == 1 && $row[0] == '') {  // skip blank lines
                continue;
            }
            unset($data);
            $data = array_combine($header, $row);

            // Otherwise, create a record for this payment request.
            $result = $obj->CRUD->create([
                'ncb' => $data['NCB'],
                'full_name' => $data['FullName'],
                'total_amount' => $data['TotalAmount'],
                'adjusted_amount' => $data['TotalAmount'],
                'account_type' => $data['AccountType'],
                'status' => $data['Status'],
                'pid' => $data['PID'] === '' ? NULL : $data['PID'],
                'payrun_ref' => $payrun_id,
                'deduction_csv_name' => $deduction_csv_name,
            ]);
        }

        return ['http_code' => 201];
    }

    function array_contains_keys_in_order($array, $expected_order)
    {
        $is_in_order = true;
        foreach ($array as $i => $value) {
            if ($expected_order[$i] != $value) {
                $is_in_order = false;
                break;
            }
        }
        return $is_in_order;
    }

    function getBinary($data)
    {
        list($mime_type_array, $base64_array) = explode(';', $data);
        $mime_type_array = explode(':', $mime_type_array);
        $mime_type = $mime_type_array[1];
        $base64_array = explode(',', $base64_array);
        $base64_file = $base64_array[1];
        $binary = base64_decode($base64_file);
        return $binary;
    }
}
