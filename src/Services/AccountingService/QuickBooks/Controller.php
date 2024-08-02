<?php
namespace NDISmate\quickbooks;

use NDISmate\CORE\JsonResponse;
use NDISmate\QuickBooks\Session;
use NDISmate\QuickBooks\Helpers as QuickBooksHelpers;

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


                    // case "setupItems":
                    //     // $session = new Session;
                    //     $quickbooksHelpers = new QuickBooksHelpers;
                    //     return JsonResponse::response($quickbooksHelpers->setupItems());
                    // break;

                    // case "findOrCreatequickbooksContactByEmail":
                    //     $quickbooksHelpers = new QuickBooksHelpers;
                    //     return JsonResponse::response($quickbooksHelpers->findOrCreatequickbooksContactByEmail($data));
                    // break;


                    case "listTenants":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response(["http_code"=>200, "result"=>$quickbooksHelpers->listTenants()]);
                    break;

                    case "getTenantId":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->getTenantId());
                        // return JsonResponse::response(["http_code"=>200, "result"=>$quickbooksHelpers->getTenantId()]);
                    break;

                    case "setTenantId":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->setTenantId($data));
                        // return JsonResponse::response(["http_code"=>200, "result"=>$quickbooksHelpers->setTenantId($data)]);
                    break;

                    case "getContacts":
                        // $session = new Session;
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->getContacts());
                    break;

    
                    case "getAccounts":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->getAccounts());
                        break;
                    
                    case "getEmployees":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->getEmployees($data));
                        break;

                    case "getBankTransactions":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->getBankTransactions());
                        
                        break;
                    case "makeBatchPayment":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->makeBatchPayment());
                        
                        break;        

                    case "deleteBatchPayment":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->deleteBatchPayment());
                        break;
                    
                    case "updatePlanManagers":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->updatePlanManagersWithquickbooksContactId());
                        
                        break;

                    case "setNDIAContact":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->setNDIAContact($data['quickbooks_contact_id']));
                    break;

                    case "getNDIAContact":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        return JsonResponse::response($quickbooksHelpers->getNDIAContact());
                    break;


                    case "getInvoiceById":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        // b7b88123-6b9c-473d-a624-c40d9a39b964
                        // 94df5b3e-dc15-4452-8ec7-92dc0448483e
                        return JsonResponse::response($quickbooksHelpers->getInvoiceById("94df5b3e-dc15-4452-8ec7-92dc0448483e"));
                    break;

                    case "getInvoiceAsPdf":
                        $quickbooksHelpers = new QuickBooksHelpers;
                        // b7b88123-6b9c-473d-a624-c40d9a39b964
                        // 94df5b3e-dc15-4452-8ec7-92dc0448483e
                        return JsonResponse::response($quickbooksHelpers->getInvoiceAsPdf("b7b88123-6b9c-473d-a624-c40d9a39b964"));
                    break;


                    

                    // case "listFolders":
                    //     $drive = new Drive;
                    //     return JsonResponse::response(["http_code"=>200, "result"=>$drive->listFolders()]);
                    // break;

                    // case "deleteFile":
                    //     $drive = new Drive;
                    //     $result = $drive->deleteFile($data);
                    //     if ($result === true){
                    //         return JsonResponse::response(["http_code"=>200, "result"=>$drive->deleteFile($data)]);    
                    //     }else{
                    //         return JsonResponse::response(["http_code"=>400, "result"=>$result]);
                    //     }
                        
                    // break;

                    // case "listConnections":
                    //     $drive = new Drive;
                    //     return JsonResponse::response(["http_code"=>200, "result"=>$drive->listConnections()]);
                    // break;

                }
                break;
        }

        return JsonResponse::notFound();
    }

}
