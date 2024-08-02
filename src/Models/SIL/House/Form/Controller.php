<?php
namespace NDISmate\Models\SIL\House\Form;

use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\Guard;
use Psr\Http\Message\ServerRequestInterface;

final class Controller
{
    public function __invoke(ServerRequestInterface $request)
    {
        $body = json_decode($request->getBody(), true);
        $data = $body['data'];
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        switch ($body['action']) {
            case 'saveForm':
                try {
                    $guard->protect();
                    $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\SaveForm)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'deleteForm':
                try {
                    $guard->protect();
                    $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\DeleteForm)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getForm':
                try {
                    $guard->protect();
                    $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetForm)($data);
                } catch (\Exception $e) {
                    // return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getTimeline':
                try {
                    // $guard->protect();
                    // $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetTimeline)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getStafferShiftTimeline':
                try {
                    // $guard->protect();
                    // $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetStafferShiftTimeline)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getKMs':
                try {
                    // $guard->protect();
                    // $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetKMs)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getFormSummary':
                try {
                    // $guard->protect();
                    // $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetFormSummary)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getParticipantFormSummary':
                try {
                    // $guard->protect();
                    // $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetParticipantFormSummary)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getCSVExport':
                try {
                    // $guard->protect();
                    // $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetCSVExport)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            case 'getAvailableReportTypes':
                try {
                    // $guard->protect();
                    // $data['jwt_claims'] = $guard->claims;
                    return (new \NDISmate\Models\SIL\House\Form\GetAvailableReportTypes)($data);
                } catch (\Exception $e) {
                    return JsonResponse::unauthorized([$e->getMessage()]);
                }
                break;

            default:
                return JsonResponse::notFound();
        }
    }
}

// $incident = <<<JSON
// {
//     "date": "2023-04-16",
//     "time": "12:37",
//     "staff_id": 1,
//     "participant_id": 1,
//     "report_type": "Incident",
//     "event_details": "Participant was playing with a toy and it broke. Staffer was trying to get the toy away from the participant.",
//     "staff_activity_prior": "Staffer was playing with the participant and the participant was playing with the toy.",
//     "participant_activity_prior": "Participant was playing with the toy and it broke. Staffer was trying to get the toy away from the participant.",
//     "staff_actions": "What staff did during the event",
//     "event_duration": "2hrs 30mins",
//     "staff_activity_after": "Staff watched tv.",
//     "participant_activity_after": "Participant watched tv.",
//     "impact_ranking": 3,
//     "report_required": "Yes",
//     "incident_types": [
//       {
//         "name": "Property Damage",
//         "details": "Door was damaged. Cut on hand"
//       },
//       {
//         "name": "First Aid required for participant",
//         "details": "Put bandaid on cut on hand."
//       }
//     ],
//     "property_damage": "Yes",
//     "emergency_services": "No",
//     "additional_details": "Additional details about the incident",
//     "resolved": "Yes"
// }
// JSON;
