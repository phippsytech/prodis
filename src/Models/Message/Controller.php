<?php

namespace NDISmate\Models\Message;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // public function __invoke(ServerRequestInterface $request){

        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        switch ($body['action']) {
            case 'sendMessage':
                try {
                    // $guard->protect(["admin"]);
                    $message = (new \NDISmate\Models\Message\SendMessage)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $message]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
