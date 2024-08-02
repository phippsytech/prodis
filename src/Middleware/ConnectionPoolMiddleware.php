<?php

namespace NDISmate\Middleware;

use NDISmate\Services\MysqlConnectionPoolService\GetConnection;
use NDISmate\Services\MysqlConnectionPoolService\ReleaseConnection;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ConnectionPoolMiddleware
{
    private $container;

    // public function __construct(ContainerInterface $container)
    // {
    //     $this->container = $container;
    //     // error_log("ConnectionPoolMiddleware\n");
    // }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        error_log('ConnectionPoolMiddleware invoked');
        $this->container->set('pdoConnection', 'bird');

        // Get a connection from the connection pool
        // $pdoConnection = (new GetConnection)();

        // Store the connection in the container
        // $this->container->set('pdoConnection', $pdoConnection);

        // Register a shutdown function to release the connection
        // register_shutdown_function([$this, 'releaseConnection']);

        try {
            $response = $handler->handle($request);
        } catch (\Throwable $e) {
            // Handle exception and possibly log it
            // throw new HttpInternalServerErrorException($request, $e->getMessage(), $e);
        }

        return $response;
    }

    private function getConnection()
    {
        // Your connection logic here
        $pdoConnection = (new GetConnection)();
        echo "Connection acquired\n";
    }

    public function releaseConnection()
    {
        // Your connection release logic here
        (new ReleaseConnection)($this->pdoConnection);
        echo "Connection released\n";
    }
}
