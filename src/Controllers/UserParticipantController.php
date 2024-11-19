<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class UserParticipantController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // public function __invoke(ServerRequestInterface $request){

        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);
        $data['user_id'] = $guard->user_id;

        switch ($body['action']) {
            case 'allowUserParticipant':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\Allow)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'denyUserParticipant':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\Deny)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'deleteUserParticipant':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\Delete)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'listAllowedByUserId':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\ListAllowedByUserId)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            // case 'listDeniedUserParticipantByUserId':
            //     try {
            //         $guard->protect(['super', 'sysadmin']);
            //         return JsonResponse::ok((new \NDISmate\Models\User\Participant\ListDeniedByUserId)($data));
            //     } catch (\Exception $e) {
            //         return JsonResponse::internalServerError([$e->getMessage()]);
            //     }
            //     break;

            case 'listUserParticipantByUserId':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\ListByUserId)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
