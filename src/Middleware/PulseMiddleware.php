<?php
namespace NDISmate\Middleware;

use NDISmate\Services\AuthenticationService\Guard;
use NDISmate\APILog;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class PulseMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $data = json_decode($request->getBody(), true);
        
        $data['method'] = $request->getMethod();
        if ($data['method'] == 'OPTIONS')
            return $handler->handle($request);

        // Grab the token from the Authorization header so we can work out who this is
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);
        $guard = new Guard($token);

        $data['action'] = $data['action'] ?? null;
        $data['client_id'] = $data['data']['client_id'] ?? null;

        $data['user_id'] = $guard->user_id;

        $data['uri'] = $request->getUri()->getPath();  // (string)

        // Check if the X-Forwarded-For header exists
        if (isset($headers['X-Forwarded-For'])) {
            $ipAddress = $headers['X-Forwarded-For'];
        } else {
            // Fallback to the direct method
            $ipAddress = $request->getAttribute('ip_address');
        }

        if (!isset($_SERVER['REMOTE_ADDR'])) {
            $serverParams = $request->getServerParams();
            $data['ip'] = $serverParams['REMOTE_ADDR'];
        } else {
            $data['ip'] = $_SERVER['REMOTE_ADDR'];
        }

        // Log the API call
        try {
            // if the action is on the ignore list, don't log it.
            if ($this->ignoreAction($data['action']) || $data['action'] == null)
                return $handler->handle($request);

            $data['data'] = $data['data'] ?? [];  // ensure data is an array.
            $apiLog = new APILog();
            $apiLog->create($data);
        } catch (Exception $e) {
            // If there is an error logging the API call, we don't want to stop the request
        }

        // Send the data to the Pulse channel
        $json = json_encode([
            'action' => 'sendToChannel',
            'channel' => 'pulse',
            'data' => $data
        ]);

        $socket = stream_socket_client('unix:///tmp/ndismate.sock');

        if ($socket !== false) {
            fwrite($socket, $json);
            $response = fread($socket, 8192);
        }

        $response = $handler->handle($request);
        return $response;
    }

    function ignoreAction($action)
    {
        $ignore_list = [
            'addCredential', // contains large data I don't want to store
            'updateCredential', // contains large data I don't want to store
            'listPlanManagers',
            'getClientPlanService',
            'listClientPlanServicesByParticipantId',
            'listServicesByParticipantId',
            'getService',
            'listStaff',
            'getStaffer',
            'getEmployees',
            'listFilteredBillings',
            'listStaffClientsByStaffId',
            'getAvailableSessionDuration',
            'getExpiredNDISClients',
            'listClientReports',
            'getCapacity',
            'getClientsWithoutAgreement',
            'getLastSeen',
            'getParticipantBudget',
            'listUnbilled',
            'getTrip',
            'listProviderTravelByClientId',
            'getTimeTracking',
            'listSupportItems',
            'listTimeTrackingByClientId',
            'listClientStaffByClientId',
            'listServiceAgreementsByParticipantId',
            'getServiceAgreement',
            'requestRegistration',
            'register',
            'listServicesByServiceAgreementId',
            'getTimeline',
            'getPlanManager',
            'getParticipantService',
            'getErrors',
            'getTimeTrackingSummary',
            'listCaseNotesByClientId',
            'getSettings',
            'listFiles',
            'listFolders',
            'getBreadcrumbs',
            'listClientReportsByStaffId',
            'getOverdueVisits',
            'getDueReportList',
            'listTrips',
            'listClientInvoices',
            'getClientInvoiceSummary',
            'listMeetingNotes',
            'listCredentials',
            'listStaffByGroup',
            'getStaffKPI',
            'listParticipantServicesByParticipantId',
            'listInvoiceBatches',
            'getInvoiceBatch',
            'getNDIAContact',
            'getInvoice',
            'getInvoiceBatchDate',
            'listTripsByDate',
            'NULL',
            'requestChallenge',
            'authenticate',
            'getUser',
            'getMyStaffId',
            'listClients',
            'getClient',
            'listStakeholdersByClientId',
            'listNotesByClientId',
            'listClientReportsByClientId',
            'listServices',
            'getClientPlan',
            'listClientPlanServicesByClientId',
            'getClientServiceSummary',
            'getStafferKPI',
            'listParticipantsByUserId',
            'listUsers',
            'listDeniedUserParticipantByUserId',
            'listAllowedUserParticipantByUserId',
            'searchClients',
        ];

        return in_array($action, $ignore_list);
    }
}
