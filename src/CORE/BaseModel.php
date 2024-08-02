<?php
namespace NDISmate\CORE;

use NDISmate\CORE\CRUD;
use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class BaseModel
{
    var $CRUD;
    var $actions;

    public function invoke(ServerRequestInterface $request, ResponseInterface $response, array $args, $model): ResponseInterface
    {
        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $fields = $body['fields'] ?? null;
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);
        $user_roles = [];

        foreach ($this->actions as $action) {
            $the_action = $action[0];
            $class_method = $action[1];
            $require_guard = $action[2];
            $role = $action[3];

            if ($the_action == $body['action']) {
                if ($require_guard == true) {  // we need to guard this

                    try {
                        $guard->protect($role);

                        // the following data is now being passed via $guard rather than in $data
                        // $data['user_id'] =$guard->user_id;
                        // $user_roles = $guard->roles;
                    } catch (\Exception $e) {
                        return JsonResponse::unauthorized([$e->getMessage()]);
                    }
                }

                return JsonResponse::response($model->{$class_method}($data, $fields, $guard));
            }
        }

        return JsonResponse::notFound();
    }

    function archive($data)
    {
        $result = $this->getOne($data);
        $result = $result['result'];
        $result['archived'] = true;
        $result = $this->update($result);
        $result = $result['result'];
        return ['http_code' => 200, 'result' => $result];
    }

    function restore($data)
    {
        $result = $this->getOne($data);
        $result = $result['result'];
        $result['archived'] = false;
        $result = $this->update($result);
        $result = $result['result'];
        return ['http_code' => 200, 'result' => $result];
    }

    function create($data)
    {
        $result = $this->CRUD->create($data);
        return $result;
    }

    function getOne($data)
    {
        $result = $this->CRUD->getOne($data['id']);
        if (isset($result['result'])) {
            $result = $result['result'];
            return ['http_code' => 200, 'result' => $result];
        } else {
            // Handle the case when the 'result' key is not present
            // Maybe set a default value or throw an exception
            return ['http_code' => 404, 'error_message' => 'Not Found'];
        }
    }

    function getAll($data)
    {
        $result = $this->CRUD->getAll();

        if ($result['http_code'] == 200) {
            $result = $result['result'];
            return ['http_code' => 200, 'result' => $result];
        } else {
            return $result;
        }
    }

    function update($data)
    {
        $result = $this->CRUD->update($data);
        $result = $result['result'];
        return ['http_code' => 200, 'result' => $result];
    }

    function delete($data)
    {
        $this->CRUD->delete($data['id']);
        return ['http_code' => 204];
    }
}
