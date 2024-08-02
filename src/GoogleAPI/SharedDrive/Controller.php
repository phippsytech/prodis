<?php
namespace NDISmate\GoogleAPI\SharedDrive;

use NDISmate\CORE\JsonResponse;
use NDISmate\GoogleAPI\SharedDrive\Settings;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class Controller {

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
                        $settings = new Settings;
                        return JsonResponse::response($settings->getSettings());
                    break;

                    case "setSettings":
                        $settings = new Settings;
                        return JsonResponse::response($settings->setSettings($data));
                    break;


                    // case "setSharedDrive":
                    //     $settings = new Settings;
                    //     return JsonResponse::response($settings->setSharedDrive($data));
                    // break;

                    // case "setParticipantsFolder":
                    //     $settings = new Settings;
                    //     return JsonResponse::response($settings->setParticipantsFolder($data));
                    // break;

                    // case "setStaffFolder":
                    //     $settings = new Settings;
                    //     return JsonResponse::response($settings->setStaffFolder($data));
                    // break;

                }
                break;
        }

        return JsonResponse::notFound();
    }

}
