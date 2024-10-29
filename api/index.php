<?php

use Monolog\Logger;
use NDISmate\CORE\ControllerFactory;
use NDISmate\Middleware\CorsMiddleware;
use NDISmate\Middleware\PulseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use React\Http\Message\Response as ReactResponse;
use React\Http\Server as ReactServer;
use React\Socket\SecureServer;
use React\Socket\Server as SocketServer;
use RedBeanPHP\R;
use Respect\Validation\Factory;
use Slim\Factory\AppFactory;
// use Slim\Psr7\Factory\StreamFactory;

use React\Http\Middleware\StreamingRequestMiddleware;
use React\Http\Middleware\LimitConcurrentRequestsMiddleware;
use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Middleware\RequestBodyParserMiddleware;

require '/var/www/prodis/init.php';

// Set up the Validation Factory
Factory::setDefaultInstance(
    (new Factory())
        ->withRuleNamespace('NDISmate\CORE\Validation\Rules')
        ->withExceptionNamespace('NDISmate\CORE\Validation\Exceptions')
);

$app = AppFactory::create();

$app->add(new CorsMiddleware());
$app->add(new PulseMiddleware());

/**
 * The routing middleware should be added earlier than the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled by the middleware
 */
$app->addRoutingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool                  $displayErrorDetails -> Should be set to false in production
 * @param bool                  $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool                  $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null  $logger -> Optional PSR-3 Logger
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$environment = TEST ? 'development' : 'production';

\Sentry\init(['dsn' => SENTRY_DSN, 'environment' => $environment]);
$client = \Sentry\ClientBuilder::create()->getClient();
$handler = new \Sentry\Monolog\Handler(new \Sentry\State\Hub($client));
$logger = new \Monolog\Logger('sentry_logger');
$logger->pushHandler($handler);

$displayErrorDetails = $logErrors = $logErrorDetails = TEST;
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails, $logger);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// API Routes
// The routes are defined using a lazy load factory function to prevent
// controllers from being instantiated until they are called.

// ##############
// # GET ROUTES #
// ##############
$app->get('/App', new ControllerFactory(\NDISmate\Init::class));

$app->get('/My', new ControllerFactory(\NDISmate\MyInit::class));
$app->get('/Xero', new ControllerFactory(\NDISmate\Xero\Controller::class));  // TODO: improve response speed

// ###############
// # POST ROUTES #
// ###############

// UPDATED ROUTE
/* these routes have had their controllers moved to the new structure */
$app->post('/ActivityLog', new ControllerFactory(\NDISmate\Controllers\ActivityLogController::class));
$app->post('/App', new ControllerFactory(\NDISmate\Controllers\ApplicationController::class));
$app->post('/Auth', new ControllerFactory(\NDISmate\Controllers\AuthenticationController::class));
$app->post('/Client', new ControllerFactory(\NDISmate\Controllers\ClientController::class));  // This is an alias for Participant
$app->post('/Participant', new ControllerFactory(\NDISmate\Controllers\ParticipantController::class));
$app->post('/Client/CaseNote', new ControllerFactory(\NDISmate\Controllers\ClientCaseNoteController::class));
$app->post('/Client/Note', new ControllerFactory(\NDISmate\Controllers\ClientNoteController::class));
$app->post('/Client/Report', new ControllerFactory(\NDISmate\Controllers\ClientReportController::class));
$app->post('/Client/Staff', new ControllerFactory(\NDISmate\Controllers\ClientStaffController::class));
$app->post('/Client/Stakeholder', new ControllerFactory(\NDISmate\Controllers\ClientStakeholderController::class));
$app->post('/DocumentType', new ControllerFactory(\NDISmate\Controllers\DocumentTypeController::class));
$app->post('/Document/Participant', new ControllerFactory(\NDISmate\Controllers\DocumentParticipantController::class));
$app->post('/Form', new ControllerFactory(\NDISmate\Controllers\FormController::class));
$app->post('/Geo', new ControllerFactory(\NDISmate\Controllers\GeoController::class));
$app->post('/Participant/Document', new ControllerFactory(\NDISmate\Controllers\ParticipantDocumentController::class));

$app->post('/Participant/ServiceBooking', new ControllerFactory(\NDISmate\Controllers\ParticipantServiceBookingController::class));
$app->post('/Participant/ServiceAgreement', new ControllerFactory(\NDISmate\Controllers\ParticipantServiceAgreementController::class));
$app->post('/Postcode', new ControllerFactory(\NDISmate\Controllers\PostcodeController::class));
$app->post('/Report', new ControllerFactory(\NDISmate\Controllers\ReportController::class));
$app->post('/Roster', new ControllerFactory(\NDISmate\Controllers\RosterController::class));
$app->post('/Service', new ControllerFactory(\NDISmate\Controllers\ServiceController::class));
$app->post('/SignatureRequest', new ControllerFactory(\NDISmate\Controllers\SignatureRequestController::class));

$app->post('/Staff/Credential', new ControllerFactory(\NDISmate\Controllers\StaffCredentialController::class));

$app->post('/SupportItem', new ControllerFactory(\NDISmate\Controllers\SupportItemController::class));
$app->post('/Task', new ControllerFactory(\NDISmate\Controllers\TaskController::class));
$app->post('/TimeTracking', new ControllerFactory(\NDISmate\Controllers\TimeTrackingController::class));
$app->post('/Trip', new ControllerFactory(\NDISmate\Controllers\TripController::class));
$app->post('/Utilities', new ControllerFactory(\NDISmate\Controllers\UtilityController::class));
$app->post('/Storage', new ControllerFactory(\NDISmate\Controllers\ObjectStorageController::class));


// CONVERSION IN PROGRESS
$app->post('/User', new ControllerFactory(\NDISmate\Controllers\UserController::class));
$app->post('/User/Participant', new ControllerFactory(\NDISmate\Controllers\UserParticipantController::class));

// OTHER ROUTES
/* these routes have NOT been updated to the new structure */
$app->post('/Billing', new ControllerFactory(\NDISmate\Models\Billing::class));

/* new routes */


$app->post('/Credential', new ControllerFactory(\NDISmate\Models\Credential::class));
$app->post('/Document', new ControllerFactory(\NDISmate\Models\Document::class));
$app->post('/Invoice', new ControllerFactory(\NDISmate\Models\Invoice::class));
$app->post('/Invoice/NDIA/PaymentRequestStatus', new ControllerFactory(\NDISmate\Models\Invoice\NDIA\PaymentRequestStatus::class));
$app->post('/Invoice/NDIA/Remittance', new ControllerFactory(\NDISmate\Models\Invoice\NDIA\Remittance::class));
$app->post('/MeetingNote', new ControllerFactory(\NDISmate\Models\MeetingNote::class));
$app->post('/Message', new ControllerFactory(\NDISmate\Models\Message\Controller::class));
$app->post('/Payroll/Leave', new ControllerFactory(\NDISmate\Models\Payroll\Leave\Controller::class));
$app->post('/Payroll/Leave/LeaveRequest', new ControllerFactory(\NDISmate\Models\Payroll\Leave\LeaveRequest::class));
$app->post('/Payroll', new ControllerFactory(\NDISmate\Models\Payroll\Controller::class));
$app->post('/Payroll/Payrun', new ControllerFactory(\NDISmate\Models\Payroll\Payrun\Controller::class));
$app->post('/Payroll/Payrun/Payslip', new ControllerFactory(\NDISmate\Models\Payroll\Payrun\Payslip\Controller::class));
$app->post('/Payroll/Payrun/Adjustment', new ControllerFactory(\NDISmate\Models\Payroll\Payrun\Adjustment::class));
$app->post('/Payroll/Payrun/SalaryPackagingDeduction', new ControllerFactory(\NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction::class));
$app->post('/Payroll/PayGrade', new ControllerFactory(\NDISmate\Models\Payroll\PayGrade::class));
$app->post('/Payroll/Template/EarningsLine', new ControllerFactory(\NDISmate\Models\Payroll\Template\EarningsLine::class));
$app->post('/Payroll/Template/TaxLine', new ControllerFactory(\NDISmate\Models\Payroll\Template\TaxLine::class));
$app->post('/PlanManager', new ControllerFactory(\NDISmate\Models\PlanManager::class));
$app->post('/Register/Feedback', new ControllerFactory(\NDISmate\Models\Register\Feedback::class));
$app->post('/Register/Risk', new ControllerFactory(\NDISmate\Models\Register\Risk::class));
$app->post('/Register/ConflictOfInterest', new ControllerFactory(\NDISmate\Models\Register\ConflictOfInterest::class));
$app->post('/SIL/Billing', new ControllerFactory(\NDISmate\Models\SIL\Billing\Controller::class));
$app->post('/SIL/House', new ControllerFactory(\NDISmate\Models\SIL\House::class));
$app->post('/SIL/House/Form', new ControllerFactory(\NDISmate\Models\SIL\House\Form\Controller::class));
$app->post('/SIL/House/Roster', new ControllerFactory(\NDISmate\Models\SIL\House\Roster::class));
$app->post('/SIL/House/Roster/Template', new ControllerFactory(\NDISmate\Models\SIL\House\Roster\Template::class));
$app->post('/SIL/House/Staff', new ControllerFactory(\NDISmate\Models\SIL\House\Staff::class));
$app->post('/SIL/Payrun', new ControllerFactory(\NDISmate\Models\SIL\Payrun\Controller::class));  // TODO: improve response speed
$app->post('/Staff', new ControllerFactory(\NDISmate\Models\Staff::class));
$app->post('/Staff/Schedule', new ControllerFactory(\NDISmate\Models\Staff\Schedule\Controller::class));

$app->post('/Staff/Team', new ControllerFactory(\NDISmate\Models\Staff\Team::class));
$app->post('/Tickets', new ControllerFactory(\NDISmate\Models\Tickets\Controller::class));
$app->post('/TimeLog', new ControllerFactory(\NDISmate\Models\TimeLog::class));
$app->post('/Xero', new ControllerFactory(\NDISmate\Xero\Controller::class));  // TODO: improve response speed
$app->post('/Xero/Payroll', new ControllerFactory(\NDISmate\Xero\Payroll\Controller::class));  // TODO: improve response speed
$app->post('/Xero/Payroll/Migrate', new ControllerFactory(\NDISmate\Xero\Payroll\Migrate\Controller::class));  // TODO: improve response speed

// ## DISABLED ENDPOINTS ##

// $app->post('/SIL/RosterOfCare', new ControllerFactory(\NDISmate\Models\SIL\RosterOfCare::class));
// $app->post('/SIL/RosterOfCare/Shift', new ControllerFactory(\NDISmate\Models\SIL\RosterOfCare\Shift::class));
// $app->post('/Stakeholder', new ControllerFactory(\NDISmate\Models\Stakeholder::class));
// $app->post('/appsec', new ControllerFactory(\NDISmate\Services\AuthenticationService\Controller::class));
// $app->post('/Client/ServiceAgreement/Service', new ControllerFactory(\NDISmate\Models\Client\Plan\Service::class));

// Make it a ReactPHP Server

$DOMAIN = API_DOMAIN;
$PORT = 8080;

// // Middleware components
$streamingMiddleware = new StreamingRequestMiddleware();
$limitMiddleware = new LimitConcurrentRequestsMiddleware(100); // 100 concurrent requests
$bufferMiddleware = new RequestBodyBufferMiddleware(16 * 1024 * 1024); // Max 16 MiB per request
$parserMiddleware = new RequestBodyParserMiddleware();

// Create ReactPHP HTTP server
$server = new ReactServer($streamingMiddleware, $limitMiddleware, $bufferMiddleware, $parserMiddleware, function (Request $request) use ($app) {

    try {
        $slimResponse = $app->handle($request);
        return new ReactResponse(
            $slimResponse->getStatusCode(),
            $slimResponse->getHeaders(),
            (string) $slimResponse->getBody()
        );
    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());
        return new React\Http\Message\Response(
            500,
            ['Content-Type' => 'text/plain'],
            'Internal Server Error'
        );
    }
});

// Create socket server
$socket = new SocketServer('0.0.0.0:' . $PORT);

// Start the ReactPHP server
$server->listen($socket);

// Set up a periodic timer to keep the database connection alive
$interval = 300;  // Interval in seconds (e.g., every 5 minutes)
\React\EventLoop\Loop::addPeriodicTimer($interval, function () {
    $dbHandler = R::getDatabaseAdapter()->getDatabase();
    try {
        @$dbHandler->getPDO()->query('SELECT 1');
        return true;
    } catch (\PDOException $e) {
        try {
            $dbHandler->close();
            $dbHandler->connect();
        } catch (\PDOException $e) {
            // Database Unavailable - TODO: this needs to trigger an alert
        }
    }
});
