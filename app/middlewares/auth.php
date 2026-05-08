<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

return function ($method, $params) {
    if (!is_authenticated()) {
        set_flash('error', 'Please login to continue.');
        header('Location: ' . url('login'));
        exit;
    }

    $user = dominium_refresh_auth_session();
    if (!$user) {
        set_flash('error', 'Your session expired. Please login again.');
        header('Location: ' . url('login'));
        exit;
    }

    if (!empty($user['is_banned']) || (!empty($user['banned_until']) && strtotime($user['banned_until']) > time())) {
        $banInfo = dominium_user_ban_info((int)$user['id']);
        dominium_logout_session_keep_flash();

        if ($banInfo && !empty($banInfo['is_permanent'])) {
            set_flash('error', 'Your account has been permanently banned. Reason: ' . ($banInfo['ban_reason'] ?: 'Not specified'));
        } else {
            $remaining = $banInfo['remaining_time'] ?? null;
            $timeLeft = 'temporarily';
            if ($remaining) {
                $days = floor($remaining / 86400);
                $hours = floor(($remaining % 86400) / 3600);
                $minutes = floor(($remaining % 3600) / 60);
                $parts = [];
                if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
                if ($hours > 0) $parts[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
                if ($minutes > 0) $parts[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
                if ($parts) $timeLeft = implode(' ', $parts);
            }
            set_flash('error', 'Your account is temporarily banned. Reason: ' . (($banInfo['ban_reason'] ?? '') ?: 'Not specified') . '. Time remaining: ' . $timeLeft);
        }

        header('Location: ' . url('banned'));
        exit;
    }

    return true;
};
