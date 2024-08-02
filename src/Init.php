<?php
namespace NDISmate;

use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Init
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $init = json_decode(file_get_contents(__DIR__ . '/init.json'), true);
        $init['http_code'] = 200;

        return JsonResponse::response($init);
    }
}
