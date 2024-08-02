<?php
use NDISmate\CORE\CorsMiddleware;
// use NDISmate\CORE\PulseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require ('../init.php');

$app = AppFactory::create();

$app->add(new CorsMiddleware());

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

$displayErrorDetails = $logErrors = $logErrorDetails = TEST;
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

session_start();

// API Routes
$app->post('/Auth', new \NDISmate\Services\AuthenticationService\Controller);
$app->post('/User', new \NDISmate\Models\User\Controller);

$app->run();
