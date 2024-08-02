<?php
use Monolog\Logger;
use NDISmate\Middleware\CorsMiddleware;
use NDISmate\Middleware\PulseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Factory;
use Slim\Factory\AppFactory;


use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use React\Http\Message\Response as ReactResponse;
use React\Http\Server as ReactServer;
// use React\Socket\SecureServer;
use React\Socket\Server as SocketServer;

// use NDISmate\Middleware\JWTMiddleware;
// use Monolog\Handler\RotatingFileHandler;

require '/var/www/prodis/init.php';

// Set up the Validation Factory
Factory::setDefaultInstance(
    (new Factory())
        ->withRuleNamespace('NDISmate\CORE\Validation\Rules')
        ->withExceptionNamespace('NDISmate\CORE\Validation\Exceptions')
);

$app = AppFactory::create();

$app->add(new CorsMiddleware(anon: true));  // this allows cors sessions from all users
// $app->add(new PulseMiddleware());

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

session_start();

// After the user logs in and you have the user_id
// $user_id = ...; // Get the user_id from authentication or any other source
// $_SESSION['user_id'] = $user_id; // Store the user_id in the session

// ###############
// # POST ROUTES #
// ###############

// UPDATED ROUTE
/* these routes have had their controllers moved to the new structure */
$app->post('/sign', new \NDISmate\Controllers\DocuSealWebhookController);

// $app->run();



$DOMAIN = API_DOMAIN;
$PORT = 80;


// $CERT_PATH = '/etc/letsencrypt/live/' . $DOMAIN;

// Create ReactPHP HTTP server
$server = new ReactServer(function (Request $request) use ($app) {
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


// Create socket server without SSL
$socket = new SocketServer('0.0.0.0:' . $PORT);

// Start the ReactPHP server
$server->listen($socket);

