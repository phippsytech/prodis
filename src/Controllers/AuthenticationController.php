<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Authenticate;
use NDISmate\Services\AuthenticationService\Guard;
use NDISmate\Services\AuthenticationService\Register;
use NDISmate\Services\AuthenticationService\RequestChallenge;
use NDISmate\Services\AuthenticationService\RequestRegistration;
use Psr\Http\Message\ServerRequestInterface;

final class AuthenticationController
{
    public function __invoke(ServerRequestInterface $request)
    {
        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        switch ($body['action']) {
            case 'requestRegistration':
                return (new RequestRegistration)($data);
                break;

            case 'register':
                try {
                    $guard->protect();
                    $data['jwt_claims'] = $guard->claims;
                    return (new Register)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'requestChallenge':
                return (new RequestChallenge)($data);
                break;

            case 'authenticate':
                return (new Authenticate)($data);
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
