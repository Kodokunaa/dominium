<?php
/**
 * App configurations
 * DEVELOPMENT / LOCAL XAMPP VERSION
 */

// Environment
$app_env = getenv('APP_ENV') ?: 'development';
define('IS_DEV', strtolower($app_env) !== 'production');

// Database Config (Local XAMPP MySQL)
define('DB_DRIVER', getenv('DB_DRIVER') ?: 'mysql');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'dominium');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');
define('DB_PREFIX', getenv('DB_PREFIX') ?: '');
define('DB_PATH', getenv('DB_PATH') ?: '');

// App settings
define('APP_NAME', 'Dominium');
define('APP_TAGLINE', 'Philippines premium rental marketplace');

// Use this if your folder is: C:\xampp\htdocs\dominium
define('APP_URL', rtrim(getenv('APP_URL') ?: 'http://localhost/dominium', '/'));

// Session
define('SESSION_LIFETIME', (int)(getenv('SESSION_LIFETIME') ?: (60 * 60 * 24 * 30)));

$session_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
ini_set('session.cookie_lifetime', (string) SESSION_LIFETIME);

session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'secure' => $session_https,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// Payment Config
// This app uses scheme/MockStripe.php for school/demo payment simulation.
// Put real Stripe keys in environment variables only if you replace MockStripe with the official Stripe SDK.
define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_51TRr8YEGr14FiqtzoeA5CfN2FtsFcZJ6JVfVvkNcSJwTI1IQo6oUog7d0mT8tGtfEjsbszxqb3gFRVOo230hVDj100L11mPvZP');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'sk_test_51TRr8YEGr14FiqtzhtomfOCIgilg1sxQUZhRh9TS0pOczofkITdwRaP9zMMpMtymClzC3pc8gndNhDFZ0TJopi0z00AMRFmiGJ');

// Google OAuth Config
// Use environment variables in production, replace with your actual Google OAuth credentials
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '68592317945-qbe7tlv56iqoie8lvr8j5v2pl3ib19ph.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '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');

define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: APP_URL . '/auth/google/callback');