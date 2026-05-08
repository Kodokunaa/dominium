<?php
//app root
define('APP_ROOT', __DIR__);

//headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header_remove('X-Powered-By');

//App configurations
require_once 'config.php';

//Error reporting
if (IS_DEV) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

// Failsafe: turn uncaught fatal errors into a readable 500 page instead of a blank screen.
set_exception_handler(function (Throwable $e) {
    error_log('Uncaught exception: ' . $e->getMessage());
    http_response_code(500);
    $error = (defined('IS_DEV') && IS_DEV)
        ? 'Server error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
        : 'Something went wrong. Please try again later.';
    $view = __DIR__ . '/app/views/errors/500.php';
    if (file_exists($view)) {
        include $view;
    } else {
        echo '<h1>Server error</h1><p>' . $error . '</p>';
    }
    exit;
});

register_shutdown_function(function () {
    $errorInfo = error_get_last();
    if (!$errorInfo || !in_array($errorInfo['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        return;
    }

    error_log('Fatal error: ' . $errorInfo['message'] . ' in ' . $errorInfo['file'] . ':' . $errorInfo['line']);
    if (!headers_sent()) {
        http_response_code(500);
    }

    $message = (defined('IS_DEV') && IS_DEV)
        ? 'Fatal error: ' . htmlspecialchars($errorInfo['message'], ENT_QUOTES, 'UTF-8')
        : 'Something went wrong. Please try again later.';

    $view = __DIR__ . '/app/views/errors/500.php';
    if (file_exists($view)) {
        $error = $message;
        include $view;
    } else {
        echo '<h1>Server error</h1><p>' . $message . '</p>';
    }
});

//use database query builder
require_once __DIR__ . '/scheme/Database.php';

//use helper functions
require_once __DIR__ . '/scheme/helpers.php';

//use router class
require_once __DIR__ . '/scheme/Router.php';
$router = new Router();

//call all routes
require_once  __DIR__ . '/routes.php';

//dispatch
$router->run();