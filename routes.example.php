<?php
/**
 * Dominium Routes
 */

require_once APP_ROOT . '/app/controllers/HomeController.php';
require_once APP_ROOT . '/app/controllers/AuthController.php';
require_once APP_ROOT . '/app/controllers/DashboardController.php';
require_once APP_ROOT . '/app/controllers/BookingController.php';
require_once APP_ROOT . '/app/controllers/ListingController.php';
require_once APP_ROOT . '/app/controllers/AdminController.php';
require_once APP_ROOT . '/app/controllers/UserController.php';
require_once APP_ROOT . '/app/controllers/PSGCController.php';

// ========== Public Routes ==========
$router->get('/', ['HomeController', 'index']);
$router->get('/listings', ['HomeController', 'listings']);

// ========== Protected Routes ==========
$router->get('/favorites', ['HomeController', 'favorites'])->middleware('auth');

$router->get('/api/psgc/regions', ['PSGCController', 'regions']);
$router->get('/api/psgc/provinces', ['PSGCController', 'provinces']);
$router->get('/api/psgc/cities', ['PSGCController', 'cities']);
$router->get('/api/search', ['HomeController', 'searchApi']);

// Color schemes showcase page
$router->get('/color-schemes', function () {
    include APP_ROOT . '/app/views/color-schemes.php';
});

// CSRF Token endpoint (no CSRF validation needed for this)
$router->get('/api/csrf-token', function () {
    header('Content-Type: application/json');
    echo json_encode(['token' => csrf_token()]);
});

// Favorites API routes
$router->group(['middleware' => 'auth'], function ($router) {
    $router->post('/api/favorites/add', ['HomeController', 'addFavorite']);
    $router->post('/api/favorites/remove', ['HomeController', 'removeFavorite']);
    $router->get('/api/favorites', ['HomeController', 'userFavorites']);
    $router->get('/api/favorites/check', ['HomeController', 'checkFavorite']);
    $router->get('/api/price-alerts', ['HomeController', 'priceAlerts']);
    $router->post('/api/preferences/update', ['HomeController', 'updatePreferences']);
});

// ========== Auth Routes ==========
$router->get('/login', ['AuthController', 'showAuthModal']);
$router->post('/login', ['AuthController', 'login'])->middleware('guest');
$router->get('/register', ['AuthController', 'showAuthModal']);
$router->post('/register', ['AuthController', 'register'])->middleware('guest');

// Google OAuth Routes
$router->get('/auth/google', ['AuthController', 'googleAuth'])->middleware('guest');
$router->get('/auth/google/callback', ['AuthController', 'googleCallback'])->middleware('guest');
$router->get('/logout', ['AuthController', 'logout'])->middleware('auth');

// ========== Admin Routes ==========
$router->group(['middleware' => 'admin'], function ($router) {
    $router->get('/admin', ['AdminController', 'dashboard']);
    $router->get('/admin/users', ['AdminController', 'users']);
    $router->get('/admin/listings', ['AdminController', 'listings']);
    $router->get('/admin/listers', ['AdminController', 'listers']);
    $router->get('/admin/analytics', ['AdminController', 'analytics']);
    $router->get('/admin/analytics/data', ['AdminController', 'analyticsData']);
    $router->get('/admin/bookings', ['AdminController', 'bookings']);

    // Current admin view form routes
    $router->post('/admin/users/{id}/ban', ['AdminController', 'banUser']);
    $router->post('/admin/users/{id}/unban', ['AdminController', 'unbanUser']);
    $router->post('/admin/users/{id}/demote', ['AdminController', 'demoteUser']);
    $router->post('/admin/listers/{id}/approve', ['AdminController', 'approveLister']);
    $router->post('/admin/listers/{id}/reject', ['AdminController', 'rejectLister']);
    $router->post('/admin/listings/{id}/approve', ['AdminController', 'approveListing']);
    $router->post('/admin/listings/{id}/reject', ['AdminController', 'rejectListing']);
    $router->post('/admin/bookings/{id}/approve', ['AdminController', 'approveBooking']);
    $router->post('/admin/bookings/{id}/reject', ['AdminController', 'rejectBooking']);

    // Backward-compatible legacy routes
    $router->post('/admin/ban-user', ['AdminController', 'banUser']);
    $router->post('/admin/unban-user', ['AdminController', 'unbanUser']);
    $router->post('/admin/approve-lister/{id}', ['AdminController', 'approveLister']);
    $router->post('/admin/reject-lister/{id}', ['AdminController', 'rejectLister']);
    $router->post('/admin/approve-listing/{id}', ['AdminController', 'approveListing']);
    $router->post('/admin/reject-listing/{id}', ['AdminController', 'rejectListing']);
    $router->post('/admin/demote-user/{id}', ['AdminController', 'demoteUser']);
});

// ========== Protected Routes ==========
$router->group(['middleware' => 'auth'], function ($router) {
    $router->get('/dashboard', ['DashboardController', 'dashboard']);
    $router->get('/bookings', ['DashboardController', 'bookings']);
    $router->post('/bookings/{id}/cancel', ['BookingController', 'cancel']);
    $router->get('/profile', ['UserController', 'profile']);
    $router->post('/profile/update', ['UserController', 'updateProfile']);
    $router->post('/profile/change-name', ['UserController', 'changeName']);

    $router->get('/listings/{id}/book', ['BookingController', 'show']);
    $router->post('/listings/{id}/book', ['BookingController', 'submit']);

    $router->get('/become-lister', ['ListingController', 'becomeLister']);
    $router->post('/become-lister', ['ListingController', 'submitBecomeLister']);

    $router->group(['middleware' => 'lister'], function ($router) {
        $router->get('/my-listings', ['ListingController', 'myListings']);
        $router->get('/listings/create', ['ListingController', 'create']);
        $router->post('/listings/create', ['ListingController', 'submitCreate']);
        $router->get('/listings/{id}/edit', ['ListingController', 'edit']);
        $router->post('/listings/{id}/edit', ['ListingController', 'submitEdit']);
        $router->post('/listings/{id}/toggle', ['ListingController', 'toggle']);
        $router->post('/listings/{id}/delete', ['ListingController', 'delete']);
    });
});

// Ban notification page
$router->get('/banned', function () {
    include APP_ROOT . '/app/views/errors/banned.php';
});

// Public listing detail route (must come after /listings/create to avoid conflicts)
$router->get('/listings/{id}', ['HomeController', 'listing']);
