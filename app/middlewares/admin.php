<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

return function ($method, $params) {
    $user = dominium_refresh_auth_session();
    if (!$user) {
        set_flash('error', 'Please login to continue.');
        header('Location: ' . url('login'));
        exit;
    }

    if (($user['role'] ?? '') !== 'admin') {
        set_flash('error', 'Admin access required.');
        header('Location: ' . url('dashboard'));
        exit;
    }

    return true;
};
