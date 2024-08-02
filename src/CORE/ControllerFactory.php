<?php
namespace NDISmate\CORE;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ControllerFactory
{
    private $controllerClass;

    public function __construct(string $controllerClass)
    {
        $this->controllerClass = $controllerClass;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        // Instantiate the controller class
        $controller = new $this->controllerClass();

        // Ensure the controller is callable and invoke it
        if (is_callable([$controller, '__invoke'])) {
            return $controller($request, $response, $args);
        }

        // Alternatively, handle the case where the controller does not have __invoke method
        // This could be a method like handle() or any specific method in the controller
        if (method_exists($controller, 'handle')) {
            return $controller->handle($request, $response, $args);
        }

        throw new \RuntimeException('Controller is not callable.');
    }
}
