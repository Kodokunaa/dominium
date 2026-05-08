<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

class DashboardController
{
    public static function dashboard(): void
    {
        include APP_ROOT . '/app/views/dashboard/dashboard.php';
    }

    public static function bookings(): void
    {
        include APP_ROOT . '/app/views/dashboard/bookings.php';
    }
}
