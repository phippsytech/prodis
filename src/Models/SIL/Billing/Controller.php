<?php

namespace NDISmate\Models\SIL\Billing;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Controller
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        switch ($body['action']) {
            case 'setBillingPeriodStartDate':
                try {
                    // $guard->protect(["admin"]);
                    $billing = (new \NDISmate\Models\SIL\Billing\SetBillingPeriodStartDate)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $billing]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getBillingPeriodStartDate':
                try {
                    // $guard->protect(["admin"]);
                    $start_date = (new \NDISmate\Models\SIL\Billing\GetBillingPeriodStartDate)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $start_date]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getBilling':
                try {
                    $guard->protect(['admin']);
                    $billing = (new \NDISmate\Models\SIL\Billing\GetBilling)($data);
                    return JsonResponse::response(['http_code' => 200, 'result' => $billing]);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'generateSessions':
                try {
                    $guard->protect(['admin']);
                    return JsonResponse::response((new \NDISmate\Models\SIL\Billing\GenerateSessions)($data));
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}
