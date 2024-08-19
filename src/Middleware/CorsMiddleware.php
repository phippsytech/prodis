<?php

namespace NDISmate\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CorsMiddleware
{
    /**
     * @var bool
     */
    private $anon;

    /**
     * Constructor
     *
     * @param bool $anon
     */
    public function __construct(bool $anon = false)
    {
        $this->anon = $anon;
    }

    /**
     * Cors middleware invokable class
     *
     * @param ServerRequest $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        $http_origin = $request->getHeader('HTTP_ORIGIN')[0] ?? $request->getHeader('Origin')[0];

        // not being called from a browser.  skip cors.  This will prevent the API from being called from a server.
        // This will probably affect calls from Xero or other services.
        if (empty($http_origin))
            return $response;

        if (empty($http_origin)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error_message' => 'This API endpoint must be called from a registered HTTP_ORIGIN.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // This is to only allow CORS api calls from certain domains.
        // $http_origin = $request->getHeader('HTTP_ORIGIN')[0];

        $origin = null;

        // TODO: This line will become a database lookup to check the domain has permission.
        if (in_array($http_origin, [
            'https://self.prodis.app',
            'https://prodis.app',
            'https://my.prodis.app',
            'https://demo.prodis.app',
            'https://crystelcare.prodis.app',
            
            'https://crm.crystelcare.com.au',
            'https://crystelcare.com.au',
            
            // Developers
            'https://app.prodis.phippsy.phippsy.tech',
            'https://app.prodis.santos.phippsy.tech',
            'https://app.prodis.babasa.phippsy.tech',
            'https://app.prodis.pabustan.phippsy.tech',
            'https://admin.prodis.phippsy.phippsy.tech',
            'https://admin.prodis.santos.phippsy.tech',
            'https://admin.prodis.babasa.phippsy.tech',
            'https://admin.prodis.pabustan.phippsy.tech',

            // Staging
            'https://app.prodis.staging.phippsy.tech',
            'https://admin.prodis.staging.phippsy.tech',
            
        ]) || $this->anon) {
            $origin = $http_origin;
        }

        // if you want to allow all domains, use this line instead:
        // $origin = $http_origin;

        if ($origin == null) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error_message' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Max-Age', '3600')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-App-Version');
    }
}
