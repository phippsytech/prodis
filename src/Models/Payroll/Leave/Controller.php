<?php

namespace NDISmate\Models\Payroll\Leave;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \NDISmate\CORE\KeyValue;
use \NDISmate\Xero\Session;
use \RedBeanPHP\R as R;

// use \XeroAPI\XeroPHP\AccountingObjectSerializer;  // Use this class to deserialize error caught

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
            case 'createLeaveApplication':
                try {
                    // $guard->protect(["payroll"]);
                    $result = (new \NDISmate\Models\Payroll\Leave\LeaveApplication\CreateLeaveApplication)($this, $data);
                    return JsonResponse::response($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }

                break;

            case 'listLeaveApplications':
                try {
                    // $guard->protect(["payroll"]);
                    $result = (new \NDISmate\Models\Payroll\Leave\LeaveApplication\ListLeaveApplications)($this, $data);
                    return JsonResponse::response($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }

                break;

            case 'listLeaveApplicationsByStaffId':
                try {
                    // $guard->protect(["payroll"]);
                    $result = (new \NDISmate\Models\Payroll\Leave\LeaveApplication\ListLeaveApplicationsByStaffId)($this, $data);
                    return JsonResponse::response($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }

                break;

            case 'getLeaveBalances':
                try {
                    // $guard->protect(["payroll"]);
                    $result = (new \NDISmate\Models\Payroll\Leave\GetLeaveBalances)($this, $data);
                    return JsonResponse::response($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }

                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
