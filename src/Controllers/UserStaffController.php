<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class UserStaffController
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
            case 'allowUserStaff':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Staff\Allow)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'denyUserStaff':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Staff\Deny)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'deleteUserStaff':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Staff\Delete)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'listAllowedByUserId':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Staff\ListAllowedByUserId)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            // case 'listDeniedUserStaffByUserId':
            //     try {
            //         $guard->protect(['super', 'sysadmin']);
            //         return JsonResponse::ok((new \NDISmate\Models\User\Staff\ListDeniedByUserId)($data));
            //     } catch (\Exception $e) {
            //         return JsonResponse::internalServerError([$e->getMessage()]);
            //     }
            //     break;

            case 'listUserStaffByUserId':
                try {
                    $guard->protect(['auditor','super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Staff\ListByUserId)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
