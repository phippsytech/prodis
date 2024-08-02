<?php
namespace NDISmate\Xero;

use NDISmate\CORE\JsonResponse;
use NDISmate\Xero\Session;
use NDISmate\Xero\Helpers as XeroHelpers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Controller {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $method = $request->getMethod();

        switch ($method){

            case "GET":
                $params = $request->getQueryParams();
                $result = (new Session)->authenticate($params);
                return JsonResponse::temporaryRedirect(DOMAIN.'/#/settings');
                break;

            case "POST":
                $body = json_decode($request->getBody(), true);
                $data = $body['data'];
                // $header = $request->getHeaderLine('Authorization');
                // $token = str_replace('Bearer ', '', $header);

                switch($body['action']){

                    case "connect":
                        $session = new Session;
                        return JsonResponse::response(["http_code"=>200, "result"=>$session->connect()]);
                    break;

                    case "disconnect":
                        $session = new Session;
                        return JsonResponse::response(["http_code"=>200, "result"=>$session->disconnect()]);
                    break;

                    

                    case "checkConnected":
                        $session = new Session;
                        return JsonResponse::response(["http_code"=>200, "result"=>$session->checkConnected()]);
                    break;

                    case "testConnection":
                        $session = new Session;
                        return JsonResponse::response($session->testConnection());
                    break;


                    case "setupItems":
                        // $session = new Session;
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->setupItems());
                    break;

                    // case "findOrCreateXeroContactByEmail":
                    //     $xeroHelpers = new XeroHelpers;
                    //     return JsonResponse::response($xeroHelpers->findOrCreateXeroContactByEmail($data));
                    // break;


                    case "listTenants":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response(["http_code"=>200, "result"=>$xeroHelpers->listTenants()]);
                    break;

                    case "getAccountingTenantId":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getAccountingTenantId());
                    break;

                    case "setAccountingTenantId":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->setAccountingTenantId($data));
                    break;

                    case "getPayrollTenantId":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getPayrollTenantId());
                    break;

                    case "setPayrollTenantId":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->setPayrollTenantId($data));
                    break;

                    case "getPayrollSettings":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getPayrollSettings());
                    break;



                    case "getContacts":
                        // $session = new Session;
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getContacts());
                    break;

                    case "getSuperannuationFunds":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getSuperannuationFunds());
    
                    case "getAccounts":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getAccounts());
                        break;
                    
                    case "getEmployees":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getEmployees($data));
                        break;

                    case "getBankTransactions":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getBankTransactions());
                        
                        break;
                    case "makeBatchPayment":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->makeBatchPayment());
                        
                        break;        

                    case "deleteBatchPayment":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->deleteBatchPayment());
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

                    case "updatePlanManagers":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->updatePlanManagersWithXeroContactId());
                        
                        break;

                    case "setNDIAContact":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->setNDIAContact($data['xero_contact_id']));
                    break;

                    case "getNDIAContact":
                        $xeroHelpers = new XeroHelpers;
                        return JsonResponse::response($xeroHelpers->getNDIAContact());
                    break;

                    case "getInvoice":
                        $xeroHelpers = new XeroHelpers;
                        
                        return JsonResponse::response($xeroHelpers->getInvoice($data));
                    break;

                    case "getInvoiceById":
                        $xeroHelpers = new XeroHelpers;
                        // b7b88123-6b9c-473d-a624-c40d9a39b964
                        // 94df5b3e-dc15-4452-8ec7-92dc0448483e
                        return JsonResponse::response($xeroHelpers->getInvoiceById("94df5b3e-dc15-4452-8ec7-92dc0448483e"));
                    break;

                    case "getInvoiceAsPdf":
                        $xeroHelpers = new XeroHelpers;
                        // b7b88123-6b9c-473d-a624-c40d9a39b964
                        // 94df5b3e-dc15-4452-8ec7-92dc0448483e
                        return JsonResponse::response($xeroHelpers->getInvoiceAsPdf("b7b88123-6b9c-473d-a624-c40d9a39b964"));
                    break;


       

                }
                break;
        }

        return JsonResponse::notFound();
    }

}
