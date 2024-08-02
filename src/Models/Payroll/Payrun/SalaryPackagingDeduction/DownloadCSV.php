<?php
namespace NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction;

use \RedBeanPHP\R as R;

class DownloadCSV
{
    public function __invoke($data)
    {

        $payrun_ref = $data['payrun_id'];
        
        $query = 'SELECT * FROM salarypackagingdeductions
            WHERE payrun_ref=:payrun_ref';
        $params = [
            ':payrun_ref' => $payrun_ref
        ];

        $bean = R::getAll($query, $params);

        // $bean = $result['result'];

        // header('Content-Type: text/csv');
        // header('Content-Disposition: attachment; filename="file.csv"');

        $fp = fopen('php://memory', 'w');

        // NOTE: I am using fwrite() instead of fputcsv() because fputcsv() did not allow me to wrap YYYY-MM-DD in inverted commas which resulted in the date format being mangled if opened and saved in excel.

        fwrite($fp, "NCB,FullName,TotalAmount,AccountType,Status,PID\n\n");

        foreach ($bean as $fields) {
            fwrite($fp,
                $fields['ncb'] . ','
                    . $fields['full_name'] . ','
                    . $fields['adjusted_amount'] . ','
                    . $fields['account_type'] . ','
                    . $fields['status'] . ','
                    . $fields['pid'] . "\n\n");
            $filename = $fields['deduction_csv_name'];
        }

        rewind($fp);
        $csv = stream_get_contents($fp);

        fclose($fp);
        readfile($csv);
        return ['http_code' => 200, 'result' => $csv, 'filename' => $filename];
    }
}
