<?php
namespace NDISmate\GoogleAPI;

use NDISmate\CORE\JsonResponse;
use NDISmate\GoogleAPI\Drive;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Controller {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $method = $request->getMethod();

        switch ($method){

            case "GET":
                $params = $request->getQueryParams();
                // $params['code'] = urlencode($_GET['code']);
                $result = (new Drive)->authorise($params);
                return JsonResponse::temporaryRedirect(DOMAIN.'/#/settings/documents');
                break;

            case "POST":
                $body = json_decode($request->getBody(), true);
                $data = $body['data'];
                // $header = $request->getHeaderLine('Authorization');
                // $token = str_replace('Bearer ', '', $header);

                switch($body['action']){

                    case "connect":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->connect()]);
                    break;

                    case "revoke":
                        $drive = new Drive;
                        $drive->revoke();
                        return JsonResponse::response(["http_code"=>200]);
                    break;

                    case "checkConnected":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->checkConnected()]);
                    break;

                    case "getBreadcrumbs":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->getBreadcrumbs($data)]);
                    break;

                    case "listDrives":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->listDrives($data)]);
                    break;

                    case "listFolders":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->listFolders($data)]);
                    break;

                    case "createFolder":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->createFolder($data)]);
                    break;

                    case "listFiles":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->listFiles($data)]);
                    break;

                
                    case "getFile":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->getFile($data)]);
                    break;

                    case "deleteFile":
                        $drive = new Drive;
                        $result = $drive->deleteFile($data);
                        if ($result === true){
                            return JsonResponse::response(["http_code"=>200, "result"=>$drive->deleteFile($data)]);    
                        }else{
                            return JsonResponse::response(["http_code"=>400, "result"=>$result]);
                        }
                        
                    break;

                    case "listConnections":
                        $drive = new Drive;
                        return JsonResponse::response(["http_code"=>200, "result"=>$drive->listConnections()]);
                    break;

                }
                break;
        }

        return JsonResponse::notFound();
    }

}
