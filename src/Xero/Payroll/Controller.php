<?php
namespace NDISmate\Xero\Payroll;

use NDISmate\CORE\JsonResponse;
use NDISmate\Xero\Session;
use \NDISmate\CORE\KeyValue;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Controller {


    var $session;
    var $payroll_tenant_id;  
    var $payrollAuApi;

    
    function __construct(){

        $this->session = new Session();    
        
        $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$this->session->getSession()['token'] );

        $this->payrollAuApi = new \XeroAPI\XeroPHP\Api\PayrollAuApi(
            new \GuzzleHttp\Client(),
            $config
        );

        $this->payroll_tenant_id = (string) KeyValue::get('xero_payroll_tenant_id');

    }


    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $method = $request->getMethod();

        switch ($method){

            case "POST":
                $body = json_decode($request->getBody(), true);
                $data = $body['data'];
                // $header = $request->getHeaderLine('Authorization');
                // $token = str_replace('Bearer ', '', $header);

                switch($body['action']){

                    case "getSettings":
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\GetSettings)($this));
                        break;

                    case "getPayItems":
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\GetPayItems)($this));
                        break;

                    case "getSuperannuationFunds":
                        return JsonResponse::response((new \NDISmate\Xero\Payroll\GetSuperannuationFunds)($this));
                        break;
                        
                    case "getEmployees":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getEmployees($data));
                        break;

                    case "getEmployee":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getEmployee($data));
                        break;

                    case "getPayRun":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getPayRun());
                        break;

                    case "getPayRuns":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getPayRuns());
                        break;

                }

                break;
        }

        return JsonResponse::notFound();

    }

}
