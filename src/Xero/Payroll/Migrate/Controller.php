<?php
namespace NDISmate\Xero\Payroll\Migrate;

use NDISmate\CORE\JsonResponse;
use NDISmate\Xero\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\KeyValue;

class Controller
{
    var $session;
    var $payrollAuApi;
    var $source_payroll_tenant_id;
    var $target_payroll_tenant_id;

    function __construct()
    {
        $this->session = new Session();

        $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken((string) $this->session->getSession()['token']);

        $this->payrollAuApi = new \XeroAPI\XeroPHP\Api\PayrollAuApi(
            new \GuzzleHttp\Client(),
            $config
        );

        $this->accountingApi = new \XeroAPI\XeroPHP\Api\AccountingApi(
            new \GuzzleHttp\Client(),
            $config
        );

        /*
         * //    "id": "0f6d2c79-c6f7-4f8d-bfcd-347c230930c6",
         * //     "name": "Allied Health SA PTY LTD",
         * //     "type": "ORGANISATION"
         * {
         *
         *         "id": "4d6f0bda-d7fa-4454-a4e7-9c99ed4e0e4f",
         *         "name": "Australian Disability Sport",
         *         "type": "ORGANISATION"
         *     },
         *     {
         *         "id": "721320f7-8564-414b-9daa-f81351ccf3a2",
         *         "name": "Crystel Care Pty Ltd",
         *         "type": "ORGANISATION"
         *     },
         *     {
         *         "id": "930169c8-25b3-4689-a985-fc25cf8633da",
         *         "name": "Demo Company (AU)",
         *         "type": "ORGANISATION"
         *     }
         */

        $this->source_payroll_tenant_id = '721320f7-8564-414b-9daa-f81351ccf3a2';  // Crystel Care Pty Ltd
        $this->target_payroll_tenant_id = '0f6d2c79-c6f7-4f8d-bfcd-347c230930c6';  // Allied Health SA Pty Ltd

        // $this->source_payroll_tenant_id = '4d6f0bda-d7fa-4454-a4e7-9c99ed4e0e4f';  // Australian Disability Sport
        // $this->target_payroll_tenant_id = '721320f7-8564-414b-9daa-f81351ccf3a2';  // Crystel Care Pty Ltd

        // $this->target_payroll_tenant_id="930169c8-25b3-4689-a985-fc25cf8633da"; // Demo Company (AU)
        // $this->target_payroll_tenant_id="4d6f0bda-d7fa-4454-a4e7-9c99ed4e0e4f"; // Australian Disability Sport
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $method = $request->getMethod();

        switch ($method) {
            case 'POST':
                $body = json_decode($request->getBody(), true);
                $data = $body['data'];
                // $header = $request->getHeaderLine('Authorization');
                // $token = str_replace('Bearer ', '', $header);

                switch ($body['action']) {
                    case 'migratePayItems':
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\Migrate\MigratePayItems)($this));
                        break;

                    case 'migratePayRates':
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\Migrate\MigratePayRates)($this));
                        break;

                    case 'migrateAccounts':
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\Migrate\MigrateAccounts)($this));
                        break;

                    case 'migrateEmployees':
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\Migrate\MigrateEmployees)($this));
                        break;

                    case 'migrateSuperfunds':
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\Migrate\MigrateSuperfunds)($this));
                        break;
                }

                break;
        }

        return JsonResponse::notFound();
    }
}
