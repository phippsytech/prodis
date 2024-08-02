<?php

namespace NDISmate\Models\SIL\Payrun;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \NDISmate\Services\AuthenticationService\Guard;
use \NDISmate\Xero\Session;
use \RedBeanPHP\R as R;
use \XeroAPI\XeroPHP\AccountingObjectSerializer;  // Use this class to deserialize error caught

final class Controller
{
    var $session;
    var $tenant_id;
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

        $this->tenant_id = (string) KeyValue::get('xero_tenant_id');
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
            case 'getPayItems':
                try {
                    // $guard->protect(["admin"]);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetPayItems)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getLeaveItems':
                try {
                    // $guard->protect(["admin"]);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetLeaveItems)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getDeductions':
                try {
                    // $guard->protect(["admin"]);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetDeductions)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'fetchPayItems':
                try {
                    // $guard->protect(["admin"]);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\FetchPayItems)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getPayrollCalendars':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetPayrollCalendars)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getEarnings':
                try {
                    $guard->protect(['admin']);
                    $result = (new \NDISmate\Models\SIL\Payrun\GetEarnings)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $result]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getBreakdown':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetBreakdown)($this, $data));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'createTimesheet':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\CreateTimesheet)($this, $data));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'createPayrun':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\CreatePayrun)($this, $data));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'addKmsToPayrun':
                try {
                    // $guard->protect(["admin"]);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\AddKmsToPayrun)($this, $data));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getDraftPayruns':
                try {
                    // $guard->protect(["admin"]);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetDraftPayruns)($this, $data));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;
            case 'getReimbursementTypes':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetReimbursementTypes)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getEmployees':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetEmployees)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getEmployee':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Payrun\GetEmployee)($this));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
