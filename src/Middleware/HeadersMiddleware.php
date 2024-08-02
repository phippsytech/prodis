<?php

namespace NDISmate\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class HeadersMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $serverParams = $request->getServerParams();

        // Convert the PSR-7 request to the $_SERVER array format
        $_SERVER = array_merge($_SERVER, $serverParams);

        // Output the contents of $_SERVER for demonstration purposes
        ob_start();
        var_dump($_SERVER);
        $output = ob_get_clean();

        // Log all headers
        $headers = $request->getHeaders();
        foreach ($headers as $name => $values) {
            error_log($name . ': ' . implode(', ', $values));
        }

        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null);

        print_r($_SERVER);

        error_log('TOKEN: ' . $token);

        // Log the HTTP_AUTHORIZATION environment variable
        $httpAuthorization = getenv('HTTP_AUTHORIZATION');
        error_log('HTTP_AUTHORIZATION: ' . $httpAuthorization);

        // Manually retrieve Authorization header if not set
        if (!$request->hasHeader('Authorization') && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $request = $request->withHeader('Authorization', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        // Continue handling the request
        return $handler->handle($request);
    }
}
