<?php
namespace NDISmate\Models\Payroll\Payrun;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\MessageQueueService;
use RedBeanPHP\R as R;

class DoPayrun
{
    function __invoke($xero, $data)
    {
        $payslip_ids = (new \NDISmate\Models\Payroll\Payrun\GetPayslipIds)(xero: $xero, force: true);
        $xero_employee_refs = array_keys($payslip_ids);
        // $xero_employee_refs_string = implode(',', array_map('intval', $xero_employee_refs));
        if (!empty($xero_employee_refs)) {
            $xero_employee_refs_quoted = array_map(function ($ref) {
                return "'" . $ref . "'";
            }, $xero_employee_refs);

            $xero_employee_refs_string = implode(',', $xero_employee_refs_quoted);
        } else {
            $xero_employee_refs_string = '';  // or any other action you want to take when the array is empty
        }

        // if (isset($data['staff_id'])) {
        // $staff_id = $data['staff_id'];
        // }

        $query = 'SELECT 
            id as staff_id,
            xero_employee_ref        
            FROM staffs
            WHERE xero_employee_ref IS NOT NULL AND xero_employee_ref <> ""
            AND xero_employee_ref IN (' . $xero_employee_refs_string . ')
            AND (archived is null or archived!=1)
            ';

        $params = [];

        if (isset($data['staff_id'])) {
            $query .= ' AND id=:staff_id';
            $params = [':staff_id' => $data['staff_id']];
        }

        if (isset($data['xero_employee_ref'])) {
            $query .= ' AND xero_employee_ref=:xero_employee_ref';
            $params = [':xero_employee_ref' => $data['xero_employee_ref']];
        }

        $staff = R::getAll($query, $params);

        $websocket_message = [
            'action' => 'sendToChannel',
            'channel' => 'payslip_generator',
            'data' => [
                'action' => 'updateGeneratorStatus',
                'data' => [
                    'status' => 'idle',  // idle, processing, error
                    'processed' => 0,
                    'count' => 0,
                ]
            ]
        ];

        $generator = new \NDISmate\Models\Payroll\Payrun\Payslip\Generator();
        $websocket_message['data']['data'] = $generator->startGenerator(count($staff));

        $socket = stream_socket_client('unix:///tmp/ndismate.sock');
        if ($socket !== false) {
            $websocket_json = json_encode($websocket_message);
            fwrite($socket, $websocket_json);
            $response = fread($socket, 8192);
        }

        $employee_payslip_data = [];

        foreach ($staff as $employee) {
            // if xero_employee_ref is empty or null skip this staff member
            // if (!isset($payslip_ids[$employee['xero_employee_ref']]))
            //     continue;

            $results[] = MessageQueueService::sendToQueue('ndismate_xero_payslip', [
                'data' => $data,
                'payrun_id' => $data['payrun_id'],
                'employee' => $employee,
                'payslip_id' => $payslip_ids[$employee['xero_employee_ref']]
            ]);
        }

        return $results;
    }
}
