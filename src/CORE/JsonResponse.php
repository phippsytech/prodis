<?php
namespace NDISmate\CORE;

use Slim\Psr7\Factory\StreamFactory;
use \Slim\Psr7\Response;

class JsonResponse
{
    public static function response($result): Response
    {
        $response = new Response();

        // if (!empty($result['csv'])&&$result['csv']==true){
        //     // Set the response headers

        //     $response = $response
        //     ->withHeader('Content-Type', 'application/octet-stream')
        //     ->withHeader('Content-Disposition', 'attachment; filename=ndia.csv')
        //     ->withAddedHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        //     ->withHeader('Cache-Control', 'post-check=0, pre-check=0')
        //     ->withHeader('Pragma', 'no-cache')
        //     // ->withBody((new \Slim\Psr7\Stream($result['csv_file'])));

        //     ->withBody(
        //         (new StreamFactory())->createStream($result['csv_file'])
        //     );

        //     return $response;

        // }

        // wasn't a file, just do normal
        // if (isset($result['http_code'])) {
        $http_code = $result['http_code'];
        unset($result['http_code']);
        // } else {
        //     $http_code = 200;
        // }

        if (!empty($result)) {
            $response->getBody()->write(json_encode($result));
        } else {
            $response->getBody()->write('{}');
        }

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($http_code);
    }

    public static function ok($data): Response
    {
        return self::response([
            'http_code' => 200,
            'result' => $data
        ]);
    }

    public static function created($data): Response
    {
        return self::response([
            'http_code' => 201,
            'result' => $data
        ]);
    }

    public static function noContent(): Response
    {
        return self::response([
            'http_code' => 204
        ]);
    }

    public static function badRequest($errors): Response
    {
        return self::response([
            'http_code' => 400,
            'errors' => $errors
        ]);
    }

    public static function unauthorized($error_message = 'unauthorized'): Response
    {
        return self::response([
            'http_code' => 401,
            'error_message' => $error_message
        ]);
    }

    public static function forbidden(): Response
    {
        return self::response([
            'http_code' => 403,
            'error_message' => 'forbidden'
        ]);
    }

    public static function notFound(): Response
    {
        return self::response([
            'http_code' => 404,
            'error_message' => 'Not Found'
        ]);
    }

    public static function internalServerError(string $reason): Response
    {
        return self::response([
            'http_code' => 500,
            'error_message' => $reason
        ]);
    }

    // public static function permanentRedirect($url): Response
    // {
    //     return new Response(301, ['Location' => $url], '');
    // }

    public static function temporaryRedirect($url): Response
    {
        $response = new Response();

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
