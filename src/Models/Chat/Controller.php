<?php
namespace NDISmate\Models\User\Participant;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/*
 * Chat System
 * -----------
 * - Chat is a system that allows users to send messages to each other.
 * - Channel has a name, a list of members, and a list of messages.
 * - Message has content, and a list of read receipts.
 */

final class Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // public function __invoke(ServerRequestInterface $request){

        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        switch ($body['action']) {
            case 'allowUserParticipant':
                try {
                    $guard->protect(['super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\Allow)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'denyUserParticipant':
                try {
                    $guard->protect(['super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\Deny)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'deleteUserParticipant':
                try {
                    $guard->protect(['super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\Delete)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'listAllowedUserParticipantByUserId':
                try {
                    $guard->protect(['super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\ListAllowedByUserId)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'listDeniedUserParticipantByUserId':
                try {
                    $guard->protect(['super', 'sysadmin']);
                    return JsonResponse::ok((new \NDISmate\Models\User\Participant\ListDeniedByUserId)($data));
                } catch (\Exception $e) {
                    return JsonResponse::internalServerError([$e->getMessage()]);
                }
                break;

            case 'listUserParticipantByUserId':
                try {
                    $guard->protect(['super', 'sysadmin']);
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
