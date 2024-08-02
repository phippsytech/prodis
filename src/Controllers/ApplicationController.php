<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\JsonResponse;
use NDISmate\CORE\KeyValue;
use NDISmate\Services\ApplicationService\TriggerVersionUpdateMessage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApplicationController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = json_decode($request->getBody(), true);
        if (isset($body['data'])) {
            $data = $body['data'];
        } else {
            $data = [];
        }
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);

        if (!isset($body['action']))
            return JsonResponse::notFound();

        switch ($body['action']) {
            case 'updateVersion':
                (new TriggerVersionUpdateMessage)();
                return JsonResponse::response(['http_code' => 200]);

            case 'setKeyValue':
                KeyValue::set($data['key'], $data['value']);
                return JsonResponse::response(['http_code' => 200]);
                break;

            case 'getKeyValue':
                $value = KeyValue::get($data['key']);
                return JsonResponse::response(['http_code' => 200, 'result' => $value]);
                break;
        }

        return JsonResponse::notFound();
    }
}
