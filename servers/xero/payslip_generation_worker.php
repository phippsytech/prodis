<?php
require '/var/www/prodis/init.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use \NDISmate\CORE\KeyValue;
use \NDISmate\Xero\Session;

/**
 * Did you know?
 * It is is possible to access http headers for xero requests.
 * This contains the limit remaining data.
 * It could be possible to use this to manage throttling of requests to xero.
 * https://github.com/XeroAPI/xero-php-oauth2#accessing-http-headers
 */
$connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASSWORD, RABBITMQ_VHOST);
$channel = $connection->channel();

$channel->queue_declare('ndismate_xero_payslip', false, true, false, false);

$channel->basic_consume('ndismate_xero_payslip', '', false, false, false, false, function ($message) use ($channel) {
    // TODO: I am doing this early to prevent an endless loop if the worker crashes.
    // I'm sure there is a better way to approach this

    // Acknowledge the message to remove it from the queue
    $channel->basic_ack($message->delivery_info['delivery_tag']);

    $unserializedData = unserialize($message->body);

    $data = $unserializedData['data'];
    $employee = $unserializedData['employee'];
    $payrun_id = $unserializedData['payrun_id'];
    $payslip_id = $unserializedData['payslip_id'];

    $payslip_data = [];

    $data['staff_id'] = $employee['staff_id'];
    $data['payrun_id'] = $payrun_id;
    $payslip_data['staff_id'] = $employee['staff_id'];
    $payslip_data['xero_employee_id'] = $employee['xero_employee_ref'];
    $payslip_data['payslip_id'] = $payslip_id;

    $generator = new \NDISmate\Models\Payroll\Payrun\Payslip\Generator();

    try {
        $xero = new \stdClass();
        $xero->session = new Session();
        $xero->payrollAuApi = new \XeroAPI\XeroPHP\Api\PayrollAuApi(
            new \GuzzleHttp\Client(),
            \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken((string) $xero->session->getSession()['token'])
        );
        $xero->payroll_tenant_id = (string) KeyValue::get('xero_payroll_tenant_id');

        $existing_payslip = (new \NDISmate\Models\Payroll\Payrun\Payslip\GetPayslip)($xero, $payslip_data);

        $employee = (new \NDISmate\Models\Payroll\GetEmployeeV2)($xero, $payslip_data);

        $payslip_data['leave'] = (new \NDISmate\Models\Payroll\Payrun\GetLeaveFromPayslip)($existing_payslip);

        // TODO:  ListShiftsByStaffId is slow.  See if speed can be improved
        $payslip_data['shifts'] = (new \NDISmate\Models\Payroll\Payrun\ListShiftsByStaffId)($data);

        // TODO: ListAdjustmentsByStaffId is slow.  See if speed can be improved
        $payslip_data['adjustments'] = (new \NDISmate\Models\Payroll\Payrun\ListAdjustmentsByStaffId)($data);

        $payslip_data['ppt'] = (new \NDISmate\Models\Payroll\Payrun\ListPPTByStaffId)($data, $employee, $payslip_data);

        $payslip_data['kms'] = (new \NDISmate\Models\Payroll\Payrun\ListKMsByStaffId)($data);

        // $payslip_data['salary_packaging'] = (new \NDISmate\Models\Payroll\Payrun\ListSalaryPackagingByStaffId)($data);

        $results = (new \NDISmate\Models\Payroll\Payrun\Payslip\MakeEmployeePayslip)($xero, $payslip_data, $payrun_id);

        // increase the processed count
        $generator->increaseProcessed();
    } catch (\Exception $e) {
        // TODO: This should flag an error

        // need to increase the processed count, even if it failed.
        $generator->increaseProcessed();
    }

    sleep(1);  // throttle to 1 message per second
});

// echo "Waiting for messages...\n";

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
