<?php

namespace NDISmate\Models\Payroll\Payrun;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \NDISmate\CORE\KeyValue;
use \NDISmate\Xero\Session;
use \RedBeanPHP\R as R;
use \XeroAPI\XeroPHP\AccountingObjectSerializer;  // Use this class to deserialize error caught

final class Controller
{
    var $session;
    var $payroll_tenant_id;
    var $payrollAuApi;

    function __construct()
    {
        $this->session = new Session();

        $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken((string) $this->session->getSession()['token']);

        $this->payrollAuApi = new \XeroAPI\XeroPHP\Api\PayrollAuApi(
            new \GuzzleHttp\Client(),
            $config
        );

        $this->payroll_tenant_id = (string) KeyValue::get('xero_payroll_tenant_id');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        switch ($body['action']) {
            case 'createPayrun':
                try {
                    // $guard->protect(["payroll"]);
                    $payrun = (new \NDISmate\Models\Payroll\CreatePayrun)($this);
                    return JsonResponse::response($payrun);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getPayruns':
                try {
                    // $guard->protect(["payroll"]);
                    $payruns = (new \NDISmate\Models\Payroll\GetPayruns)($this);
                    return JsonResponse::response($payruns);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getPayrollCalendars':
                try {
                    $payroll_calendars = (new \NDISmate\Models\Payroll\GetPayrollCalendars)($this);
                    return JsonResponse::response($payroll_calendars);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getPayrun':
                try {
                    // $guard->protect(["payroll"]);
                    $payrun = (new \NDISmate\Models\Payroll\Payrun\GetPayrun)($this, $data);
                    return JsonResponse::response($payrun);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'listAdjustments':
                try {
                    // $guard->protect(["payroll"]);
                    $result = (new \NDISmate\Models\Payroll\Payrun\Adjustment\ListAdjustments)($this);
                    return JsonResponse::response($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getEarningsItems':
                try {
                    // $guard->protect(["payroll"]);
                    $earnings = (new \NDISmate\Models\Payroll\GetEarningsItems)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $earnings]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getEmployee':
                try {
                    // $guard->protect(["payroll"]);
                    $employee = (new \NDISmate\Models\Payroll\GetEmployee)($this, $data);
                    return JsonResponse::response($employee);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getEmployeeByStaffId':
                try {
                    // $guard->protect(["payroll"]);
                    $employee = (new \NDISmate\Models\Payroll\GetEmployeeByStaffId)($this, $data);
                    return JsonResponse::response($employee);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getStaffIdByXeroEmployeeRef':
                try {
                    // $guard->protect(["payroll"]);
                    $employee = (new \NDISmate\Models\Payroll\Payrun\GetStaffIdByXeroEmployeeRef)($data);
                    return JsonResponse::response($employee);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getLeaveApplications':
                try {
                    // $guard->protect(["payroll"]);
                    $leave_applications = (new \NDISmate\Models\Payroll\Leave\GetLeaveApplications)($this, $data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $leave_applications]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getLeaveBalances':
                try {
                    // $guard->protect(["payroll"]);
                    $leave_balances = (new \NDISmate\Models\Payroll\Leave\GetLeaveBalances)($this, $data);
                    return JsonResponse::response($leave_balances);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getPayslip':
                try {
                    // $guard->protect(["payroll"]);
                    $earnings = (new \NDISmate\Models\Payroll\Payrun\Payslip\GetPayslip)($this, $data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $earnings]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getKMsByStaffId':
                try {
                    // $guard->protect(["payroll"]);
                    $result = (new \NDISmate\Models\Payroll\Payrun\GetKMsByStaffId)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $result]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'doPayrun':
                try {
                    // $guard->protect(["payroll"]);
                    $result = (new \NDISmate\Models\Payroll\Payrun\DoPayrun)($this, $data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $result]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'createTimesheetForStaffId':
                try {
                    $result = (new \NDISmate\Models\Payroll\Payrun\Timesheet\CreateTimesheetForStaffId)($this, $data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $result]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getPayslipIds':
                try {
                    // $guard->protect(["payroll"]);
                    $payslip_ids = (new \NDISmate\Models\Payroll\Payrun\GetPayslipIds)($this);
                    return JsonResponse::response(['http_code' => 200, 'result' => $payslip_ids]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'createEmployeeEarnings':
                try {
                    // $guard->protect(["payroll"]);
                    $earnings = (new \NDISmate\Models\Payroll\Payrun\CreateEmployeeEarnings)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $earnings]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'listShiftsByStaffId':
                try {
                    // $guard->protect(["payroll"]);
                    $earnings = (new \NDISmate\Models\Payroll\Payrun\ListShiftsByStaffId)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $earnings]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getSILEarnings':
                try {
                    // $guard->protect(["payroll"]);
                    $sil_earnings = (new \NDISmate\Models\Payroll\Payrun\GetEarnings)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $sil_earnings]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
