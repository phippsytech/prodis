<?php

namespace NDISmate\CORE;

use NDISmate\CORE\JsonResponse;
use NDISmate\CORE\UnauthorizedException;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BaseController
{
    protected $guard;
    protected $controller;
    protected $data;
    protected $fields;

    abstract protected function defineController();

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = json_decode($request->getBody(), true);
        $this->data = $body['data'];
        $this->fields = $body['fields'] ?? null;
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $this->guard = new Guard($token);
        $this->defineController();

        $action = $body['action'];

        // if action doesn't exist, return not found
        if (empty($this->controller[$action]))
            return JsonResponse::notFound();

        // process the action
        return $this->processAction($this->controller[$action]);
    }

    protected function processAction($action)
    {
        $class = $action[0];
        $class_method = $action[1];
        $require_guard = $action[2];
        $role = $action[3];

        $dependencies = (isset($action[4])) ? $action[4] : [];

        // try processing the action
        try {
            if ($require_guard)
                $this->guard->protect($role);

            $parameters = array_merge([$this->data, $this->fields, $this->guard], $dependencies);

            if ($class_method === null) {
                $result = call_user_func_array($class, $parameters);
            } else {
                $result = call_user_func_array([$class, $class_method], $parameters);
            }

            return JsonResponse::ok($result);
        } catch (UnauthorizedException $e) {
            return JsonResponse::unauthorized($e->getMessage());
        } catch (\Exception $e) {
            if (method_exists($e, 'getErrors')) {
                return JsonResponse::badRequest($e->getErrors());
                // Handle the errors
            } else {
                return JsonResponse::badRequest($e->getMessage());
            }
            return JsonResponse::badRequest($e->getErrors());
        }
    }
}
