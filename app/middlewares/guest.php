<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

return function ($method, $params) {
    if (is_authenticated()) {
        header('Location: ' . url('dashboard'));
        exit;
    }
    return true;
};
