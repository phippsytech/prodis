<?php

namespace NDISmate\Models\Tickets;

use NDISmate\CORE\JsonResponse;
use NDISmate\Models\Tickets\Ticket\Activity\AssigneeActivity;
use NDISmate\Models\Tickets\Ticket\Activity\CommentActivity;
use NDISmate\Models\Tickets\Ticket;
use NDISmate\Models\Tickets;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Controller
{
    function __construct() {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        $ticket = new Ticket($data, $guard);

        switch ($body['action']) {
            case 'listTickets':
                try {
                    // $guard->protect(["payroll"]);
                    $result = Tickets::list();
                    return JsonResponse::ok($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'createTicket':
                try {
                    $guard->protect();
                    $result = $ticket->create();
                    return JsonResponse::ok($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getTicket':
                try {
                    $result = $ticket->get();
                    return JsonResponse::ok($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'addComment':
                try {
                    $guard->protect();
                    $ticket->addActivity(new CommentActivity);
                    return JsonResponse::ok('');
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'assignTicket':
                try {
                    $result = $ticket->addActivity(new AssigneeActivity);
                    return JsonResponse::ok($result);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
