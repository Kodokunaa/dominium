<?php
defined('APP_ROOT') OR exit('No direct script access allowed');
/**
 * Global Helper Functions
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//get base url
function base_url(): string
{
    if (defined('APP_URL') && APP_URL !== '') {
        return rtrim(APP_URL, '/') . '/';
    }

    $scheme = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ) ? 'https' : 'http';

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (!preg_match('/^[A-Za-z0-9\.\-:]+$/', $host)) {
        $host = 'localhost';
    }

    $path = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');

    return $scheme . '://' . $host . ($path ? $path : '') . '/';
}


//generate url based on BASE_URL
function url(string $path = ''): string
{
    return rtrim(base_url(), '/') . '/' . ltrim($path, '/');
}

/** Absolute URL for listing images (external URL or stored upload path). */
function listing_image_src(string $image): string
{
    $image = trim($image);
    if ($image === '') {
        return '';
    }
    if (preg_match('#\Ahttps?://#i', $image) || str_starts_with($image, '//')) {
        return $image;
    }
    return url(ltrim($image, '/'));
}

/** Card/hero image: uses `thumbnail` (or legacy `image` column). */
function listing_thumbnail_src(array $listing): string
{
    $raw = (string) ($listing['thumbnail'] ?? $listing['image'] ?? '');
    return listing_image_src($raw);
}

function dominium_listing_categories(): array
{
    return [
        'Apartment',
        'Condominium',
        'Townhouse',
        'House',
        'Bungalow',
        'Villa',
        'Loft',
        'Studio',
        'Penthouse',
        'Duplex',
        'Cottage',
        'Cabin',
        'Beach House',
        'Resort',
        'Farm Stay',
        'Guesthouse',
        'Bed & Breakfast',
        'Hostel',
        'Commercial Space',
        'Office',
        'Retail Space',
        'Warehouse',
        'Industrial',
        'Land / Lot',
        'Parking Space',
        'Storage Unit',
        'Other',
    ];
}

function dominium_valid_listing_category(string $cat): bool
{
    return in_array($cat, dominium_listing_categories(), true);
}

function dominium_parse_gallery_url_lines(string $text): array
{
    $out = [];
    foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
        $line = trim($line);
        if ($line !== '') {
            $out[] = $line;
        }
    }
    return array_values(array_unique($out));
}

/**
 * @param array|null $filesField $_FILES['gallery_images'] for multi-upload input name="gallery_images[]"
 * @return list<string> Stored relative paths
 */
function dominium_collect_gallery_from_uploads(?array $filesField): array
{
    if ($filesField === null || !isset($filesField['tmp_name'])) {
        return [];
    }
    $paths = [];
    $tmpNames = $filesField['tmp_name'];
    if (!is_array($tmpNames)) {
        $single = [
            'name' => $filesField['name'],
            'type' => $filesField['type'],
            'tmp_name' => $filesField['tmp_name'],
            'error' => $filesField['error'],
            'size' => $filesField['size'],
        ];
        $p = dominium_store_listing_upload($single);
        return $p ? [$p] : [];
    }
    foreach ($tmpNames as $i => $tmp) {
        if ($tmp === '' || ($filesField['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            continue;
        }
        $file = [
            'name' => $filesField['name'][$i],
            'type' => $filesField['type'][$i],
            'tmp_name' => $filesField['tmp_name'][$i],
            'error' => $filesField['error'][$i],
            'size' => $filesField['size'][$i],
        ];
        $p = dominium_store_listing_upload($file);
        if ($p) {
            $paths[] = $p;
        }
    }
    return $paths;
}

function dominium_listing_gallery_paths(int $listingId): array
{
    try {
        $rows = db()->table('listing_images')
            ->where('listing_id', $listingId)
            ->order_by('sort_order', 'ASC')
            ->get_all();
    } catch (Throwable $e) {
        return [];
    }
    if (!$rows) {
        return [];
    }
    return array_column($rows, 'path');
}

/** Replace all gallery rows for a listing. */
function dominium_listing_gallery_save(int $listingId, array $paths): void
{
    db()->table('listing_images')->where('listing_id', $listingId)->delete();
    $sort = 0;
    foreach ($paths as $p) {
        $p = trim((string) $p);
        if ($p === '') continue;
        db()->table('listing_images')->insert([
            'listing_id' => $listingId,
            'path' => $p,
            'sort_order' => $sort
        ]);
        $sort++;
    }
}

//generate or get CSRF token
function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

//render CSRF field
function csrf_field(): void {
    echo '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

//set session flash data
function set_flash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

//get session flash data
function get_flash(string $key): ?string {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

//json response
function json_response($data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

//Database connection with singleton pattern to avoid reconnecting on every call
function db() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
    }
    return $db;
}

//escape output
function esc($var, $double_encode = TRUE): string|array
{
    if (empty($var))
    {
        return $var;
    }

    if (is_array($var))
    {
        foreach (array_keys($var) as $key)
        {
            $var[$key] = esc($var[$key], $double_encode);
        }

        return $var;
    }

    return htmlspecialchars($var, ENT_QUOTES, 'utf-8', $double_encode);
}


// ========== DOMINIUM HELPERS ==========

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

/** Build one consistent session payload from a fresh users row. */
function dominium_session_user_payload(array $user): array
{
    $firstName = (string)($user['first_name'] ?? '');
    $lastName = (string)($user['last_name'] ?? '');
    $displayName = trim((string)($user['name'] ?? ''));

    if ($displayName === '') {
        $displayName = trim($firstName . ' ' . $lastName);
    }
    if ($displayName === '') {
        $displayName = (string)($user['email'] ?? 'User');
    }

    return [
        'id' => (int)($user['id'] ?? 0),
        'name' => $displayName,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => (string)($user['email'] ?? ''),
        'role' => (string)($user['role'] ?? 'renter'),
        'is_approved' => (int)($user['is_approved'] ?? 0),
        'is_banned' => (int)($user['is_banned'] ?? 0),
        'banned_until' => $user['banned_until'] ?? null,
        'lister_application_pending' => (int)($user['lister_application_pending'] ?? 0),
        'auth_provider' => (string)($user['auth_provider'] ?? 'email'),
    ];
}

/** Refresh session role/ban state from DB so admin changes take effect without relogin. */
function dominium_refresh_auth_session(): ?array
{
    $sessionUser = $_SESSION['user'] ?? null;
    $userId = (int)($sessionUser['id'] ?? 0);
    if ($userId <= 0) {
        unset($_SESSION['user']);
        return null;
    }

    try {
        $freshUser = dominium_user_by_id($userId);
    } catch (Throwable $e) {
        error_log('Session refresh failed: ' . $e->getMessage());
        return $sessionUser;
    }

    if (!$freshUser) {
        unset($_SESSION['user']);
        return null;
    }

    $_SESSION['user'] = dominium_session_user_payload($freshUser);
    return $_SESSION['user'];
}

/** Clear the current login session and immediately start a fresh session for flash messages. */
function dominium_logout_session_keep_flash(): void
{
    $_SESSION['user'] = null;
    unset($_SESSION['user']);
    session_regenerate_id(true);
}

function is_authenticated(): bool
{
    return !empty($_SESSION['user']) && empty($_SESSION['user']['is_banned']);
}

function auth_check(?string $role = null): bool
{
    $user = auth_user();
    if (!$user || !empty($user['is_banned'])) return false;
    if ($role === null) return true;
    return $user['role'] === $role;
}

function redirect_if_not_auth($redirect_to = 'login'): void
{
    if (!is_authenticated()) {
        header('Location: ' . url($redirect_to));
        exit;
    }
}

function redirect_if_auth($redirect_to = ''): void
{
    if (is_authenticated()) {
        header('Location: ' . url($redirect_to));
        exit;
    }
}

function hash_password(string $password): string
{
    return password_hash($password, PASSWORD_BCRYPT);
}

function verify_password(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function dominium_default_listing_thumbnail(): string
{
    return 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80';
}

function dominium_user_by_email(string $email): ?array
{
    return db()->table('users')
        ->where('email', $email)
        ->get() ?: null;
}

function dominium_user_by_id(int $id): ?array
{
    return db()->table('users')
        ->where('id', $id)
        ->get() ?: null;
}

function dominium_ensure_schema(): void
{
    static $schema_checked = false;
    if ($schema_checked) return;
    
    $checks = [
        'listings' => [
            'is_approved' => 'BOOLEAN DEFAULT 0',
            'province' => 'VARCHAR(100) NOT NULL DEFAULT ""'
        ],
        'users' => [
            'is_banned' => 'BOOLEAN DEFAULT 0',
            'banned_until' => 'TIMESTAMP NULL DEFAULT NULL',
            'ban_reason' => 'TEXT NULL DEFAULT NULL',
            'lister_application_pending' => 'TINYINT(1) DEFAULT 0',
            'name_changed_at' => 'TIMESTAMP NULL DEFAULT NULL'
        ]
    ];
    
    foreach ($checks as $table => $columns) {
        foreach ($columns as $column => $definition) {
            try {
                db()->raw("SELECT $column FROM $table LIMIT 1");
            } catch (PDOException $e) {
                db()->raw("ALTER TABLE $table ADD COLUMN $column $definition");
            }
        }
    }
    
    // Check and fix users table auto_increment
    try {
        $result = db()->raw("SHOW CREATE TABLE users");
        $createTable = $result->fetchColumn(1);
        if (!str_contains($createTable, 'AUTO_INCREMENT')) {
            db()->raw("ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
            // Set auto_increment to start from 2 (admin is ID 1)
            db()->raw("ALTER TABLE `users` AUTO_INCREMENT = 2");
        }
    } catch (PDOException $e) {
        // Table doesn't exist or other error, will be handled by main schema
    }
    
    // Create analytics table if it doesn't exist
    try {
        db()->raw("SELECT 1 FROM analytics LIMIT 1");
    } catch (PDOException $e) {
        db()->raw("CREATE TABLE IF NOT EXISTS analytics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            metric_type ENUM('listings_count', 'bookings_count', 'users_count', 'favorites_count') NOT NULL,
            metric_value INT NOT NULL,
            recorded_date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_metric_date (metric_type, recorded_date),
            KEY metric_type (metric_type),
            KEY recorded_date (recorded_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    
    $schema_checked = true;
}

// Legacy functions for backward compatibility
function dominium_ensure_listing_approval_column(): void
{
    dominium_ensure_schema();
}

function dominium_ensure_user_banned_column(): void
{
    dominium_ensure_schema();
}

/**
 * Check if a user is currently banned (permanently or temporarily)
 */
function dominium_is_user_banned(int $userId): bool
{
    $user = dominium_user_by_id($userId);
    if (!$user) return false;
    
    // Check permanent ban
    if ($user['is_banned']) return true;
    
    // Check temporary ban
    if ($user['banned_until'] && strtotime($user['banned_until']) > time()) {
        return true;
    }
    
    return false;
}

/**
 * Get ban information for a user
 */
function dominium_user_ban_info(int $userId): ?array
{
    $user = dominium_user_by_id($userId);
    if (!$user) return null;
    
    if (!$user['is_banned'] && !$user['banned_until']) {
        return null;
    }
    
    $info = [
        'is_permanent' => (bool) $user['is_banned'],
        'banned_until' => $user['banned_until'],
        'ban_reason' => $user['ban_reason'],
        'remaining_time' => null
    ];
    
    if ($user['banned_until']) {
        $remaining = strtotime($user['banned_until']) - time();
        if ($remaining > 0) {
            $info['remaining_time'] = $remaining;
        }
    }
    
    return $info;
}

/**
 * Ban a user (permanent or temporary)
 */
function dominium_ban_user(int $userId, string $reason = '', string $duration = 'permanent'): bool
{
    dominium_ensure_schema();
    
    $updateData = [
        'ban_reason' => $reason
    ];
    
    if ($duration === 'permanent') {
        $updateData['is_banned'] = 1;
        $updateData['banned_until'] = null;
    } else {
        $updateData['is_banned'] = 0;
        $updateData['banned_until'] = dominium_calculate_ban_duration($duration);
    }
    
    $result = db()->table('users')
        ->where('id', $userId)
        ->update($updateData);
    
    return $result !== false;
}

/**
 * Unban a user (remove both permanent and temporary bans)
 */
function dominium_unban_user(int $userId): bool
{
    dominium_ensure_schema();
    
    $result = db()->table('users')
        ->where('id', $userId)
        ->update([
            'is_banned' => 0,
            'banned_until' => null,
            'ban_reason' => null
        ]);
    
    return $result !== false;
}

/**
 * Calculate ban end time based on duration string
 */
function dominium_calculate_ban_duration(string $duration): ?string
{
    $now = date('Y-m-d H:i:s');
    
    switch ($duration) {
        case 'permanent':
            return null;
        case '1_hour':
        case '1 hour':
        case 'hour':
            return date('Y-m-d H:i:s', strtotime($now . ' + 1 hour'));
        case '6_hours':
        case '6 hours':
            return date('Y-m-d H:i:s', strtotime($now . ' + 6 hours'));
        case '12_hours':
        case '12 hours':
            return date('Y-m-d H:i:s', strtotime($now . ' + 12 hours'));
        case '1_day':
        case '1 day':
        case 'day':
            return date('Y-m-d H:i:s', strtotime($now . ' + 1 day'));
        case '3_days':
        case '3 days':
            return date('Y-m-d H:i:s', strtotime($now . ' + 3 days'));
        case '1_week':
        case '1 week':
        case 'week':
            return date('Y-m-d H:i:s', strtotime($now . ' + 1 week'));
        case '2_weeks':
        case '2 weeks':
            return date('Y-m-d H:i:s', strtotime($now . ' + 2 weeks'));
        case '1_month':
        case '1 month':
        case 'month':
            return date('Y-m-d H:i:s', strtotime($now . ' + 1 month'));
        case '3_months':
        case '3 months':
            return date('Y-m-d H:i:s', strtotime($now . ' + 3 months'));
        case '6_months':
        case '6 months':
            return date('Y-m-d H:i:s', strtotime($now . ' + 6 months'));
        case '1_year':
        case '1 year':
        case 'year':
            return date('Y-m-d H:i:s', strtotime($now . ' + 1 year'));
        default:
            // Try to parse custom duration (e.g., "5 days", "2 weeks")
            if (preg_match('/^(\d+)\s+(hour|day|week|month|year)s?$/i', $duration, $matches)) {
                $amount = (int) $matches[1];
                $unit = $matches[2];
                return date('Y-m-d H:i:s', strtotime($now . " + $amount $unit" . ($amount > 1 ? 's' : '')));
            }
            return null;
    }
}

/**
 * Get all banned users with their ban information
 */
function dominium_get_banned_users(): array
{
    dominium_ensure_schema();
    
    $users = db()->raw(
        'SELECT * FROM users WHERE is_banned = ? OR banned_until IS NOT NULL ORDER BY updated_at DESC',
        [1]
    )->fetchAll(PDO::FETCH_ASSOC) ?: [];
    
    foreach ($users as &$user) {
        $user['ban_info'] = dominium_user_ban_info($user['id']);
    }
    
    return $users;
}

/**
 * Auto-lift expired temporary bans (call this periodically)
 */
function dominium_lift_expired_bans(): int
{
    dominium_ensure_schema();
    
    $result = db()->table('users')
        ->where_not_null('banned_until')
        ->where('banned_until', '<=', date('Y-m-d H:i:s'))
        ->update([
            'banned_until' => null,
            'ban_reason' => null
        ]);
    
    return $result !== false ? $result : 0;
}

/**
 * Save an uploaded listing image. Returns relative path for DB or null if rejected.
 */
function dominium_store_listing_upload(?array $file): ?string
{
    if ($file === null) {
        if (IS_DEV) {
            error_log("Upload debug - File is null");
        }
        return null;
    }
    
    $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_OK) {
        if (IS_DEV) {
            error_log("Upload debug - Upload error code: $uploadError");
        }
        return null;
    }
    
    $tmp = $file['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        if (IS_DEV) {
            error_log("Upload debug - Invalid temp file: '$tmp'");
        }
        return null;
    }
    
    $maxBytes = 5 * 1024 * 1024;
    $fileSize = $file['size'] ?? 0;
    if ($fileSize > $maxBytes) {
        if (IS_DEV) {
            error_log("Upload debug - File too large: $fileSize bytes (max: $maxBytes)");
        }
        return null;
    }
    
    if (IS_DEV) {
        error_log("Upload debug - File passed basic validation: " . ($file['name'] ?? 'unknown') . " ($fileSize bytes)");
    }
    // Try to detect MIME type with fallback methods
    $mime = '';
    if (extension_loaded('fileinfo')) {
        try {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmp);
        } catch (Exception $e) {
            if (IS_DEV) {
                error_log("Upload debug - finfo exception: " . $e->getMessage());
            }
        }
    }
    
    // Fallback to MIME type from file extension if finfo failed
    if (empty($mime)) {
        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'tiff' => 'image/tiff',
            'ico' => 'image/x-icon',
            'avif' => 'image/avif',
        ];
        $mime = $mimeMap[$extension] ?? '';
        if (IS_DEV) {
            error_log("Upload debug - Used fallback MIME detection: '$mime' from extension '$extension'");
        }
    }
    
    // Debug: Log the detected MIME type
    if (IS_DEV) {
        error_log("Upload debug - Detected MIME: '$mime' for file: " . ($file['name'] ?? 'unknown'));
    }
    
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
        'image/bmp' => 'bmp',
        'image/tiff' => 'tiff',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
        // Additional possible PNG MIME types
        'image/x-png' => 'png',
        // AVIF support
        'image/avif' => 'avif',
    ];
    
    if (!isset($allowed[$mime])) {
        if (IS_DEV) {
            error_log("Upload debug - MIME type '$mime' not in allowed list");
        }
        return null;
    }
    $ext = $allowed[$mime];
    $dir = APP_ROOT . '/uploads/listings';
    if (!is_dir($dir)) {
        if (IS_DEV) {
            error_log("Upload debug - Creating directory: $dir");
        }
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $dir . '/' . $name;
    
    if (IS_DEV) {
        error_log("Upload debug - Moving file from '$tmp' to '$dest'");
    }
    
    if (!move_uploaded_file($tmp, $dest)) {
        if (IS_DEV) {
            error_log("Upload debug - Failed to move uploaded file. Check permissions for: $dir");
        }
        return null;
    }
    
    if (IS_DEV) {
        error_log("Upload debug - Successfully uploaded file: $name");
    }
    
    return 'uploads/listings/' . $name;
}

/**
 * Save an uploaded avatar image. Returns relative path for DB or null if rejected.
 */
function dominium_store_avatar_upload(?array $file): ?string
{
    if ($file === null) {
        if (IS_DEV) {
            error_log("Avatar upload debug - File is null");
        }
        return null;
    }
    
    $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_OK) {
        if (IS_DEV) {
            error_log("Avatar upload debug - Upload error code: $uploadError");
        }
        return null;
    }
    
    $tmp = $file['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        if (IS_DEV) {
            error_log("Avatar upload debug - Invalid temp file: '$tmp'");
        }
        return null;
    }
    
    // Smaller size limit for avatars (2MB vs 5MB for listings)
    $maxBytes = 2 * 1024 * 1024;
    $fileSize = $file['size'] ?? 0;
    if ($fileSize > $maxBytes) {
        if (IS_DEV) {
            error_log("Avatar upload debug - File too large: $fileSize bytes (max: $maxBytes)");
        }
        return null;
    }
    
    if (IS_DEV) {
        error_log("Avatar upload debug - File passed basic validation: " . ($file['name'] ?? 'unknown') . " ($fileSize bytes)");
    }
    
    // Try to detect MIME type with fallback methods
    $mime = '';
    if (extension_loaded('fileinfo')) {
        try {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmp);
        } catch (Exception $e) {
            if (IS_DEV) {
                error_log("Avatar upload debug - finfo exception: " . $e->getMessage());
            }
        }
    }
    
    // Fallback to MIME type from file extension if finfo failed
    if (empty($mime)) {
        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'tiff' => 'image/tiff',
            'ico' => 'image/x-icon',
            'avif' => 'image/avif',
        ];
        $mime = $mimeMap[$extension] ?? '';
        if (IS_DEV) {
            error_log("Avatar upload debug - Used fallback MIME detection: '$mime' from extension '$extension'");
        }
    }
    
    // Debug: Log the detected MIME type
    if (IS_DEV) {
        error_log("Avatar upload debug - Detected MIME: '$mime' for file: " . ($file['name'] ?? 'unknown'));
    }
    
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
        'image/bmp' => 'bmp',
        'image/tiff' => 'tiff',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
        // Additional possible PNG MIME types
        'image/x-png' => 'png',
        // AVIF support
        'image/avif' => 'avif',
    ];
    
    if (!isset($allowed[$mime])) {
        if (IS_DEV) {
            error_log("Avatar upload debug - MIME type '$mime' not in allowed list");
        }
        return null;
    }
    
    $ext = $allowed[$mime];
    $dir = APP_ROOT . '/uploads/avatars';
    if (!is_dir($dir)) {
        if (IS_DEV) {
            error_log("Avatar upload debug - Creating directory: $dir");
        }
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $dir . '/' . $name;
    
    if (IS_DEV) {
        error_log("Avatar upload debug - Moving file from '$tmp' to '$dest'");
    }
    
    if (!move_uploaded_file($tmp, $dest)) {
        if (IS_DEV) {
            error_log("Avatar upload debug - Failed to move uploaded file. Check permissions for: $dir");
        }
        return null;
    }
    
    if (IS_DEV) {
        error_log("Avatar upload debug - Successfully uploaded file: $name");
    }
    
    return 'uploads/avatars/' . $name;
}

/**
 * Get absolute URL for user avatar image (external URL or stored upload path).
 */
function dominium_avatar_src(string $avatar_path = '', string $user_name = 'User'): string
{
    // Always return custom SVG avatar with initials
    return dominium_generate_avatar_svg($user_name);
}

/**
 * Generate custom SVG avatar with user initials, black-to-gray gradient, and white Montserrat text.
 */
function dominium_generate_avatar_svg(string $user_name = 'User'): string
{
    // Generate initials from user name
    $initials = '';
    $name_parts = explode(' ', trim($user_name));
    
    if (count($name_parts) >= 2) {
        // Take first letter of first and last name
        $initials = strtoupper(substr($name_parts[0], 0, 1) . substr(end($name_parts), 0, 1));
    } elseif (count($name_parts) == 1) {
        // Take first two letters if only one name
        $initials = strtoupper(substr($name_parts[0], 0, 2));
    } else {
        // Fallback to 'U' for User
        $initials = 'U';
    }
    
    // Create SVG with black-to-gray gradient and white Montserrat text
    $svg = '<svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">';
    $svg .= '<defs>';
    $svg .= '<linearGradient id="avatarGradient" x1="0%" y1="0%" x2="100%" y2="100%">';
    $svg .= '<stop offset="0%" style="stop-color:#000000;stop-opacity:1" />';
    $svg .= '<stop offset="100%" style="stop-color:#4a4a4a;stop-opacity:1" />';
    $svg .= '</linearGradient>';
    $svg .= '</defs>';
    $svg .= '<rect width="200" height="200" fill="url(#avatarGradient)" rx="50%"/>';
    $svg .= '<text x="100" y="100" text-anchor="middle" dominant-baseline="middle" fill="white" font-family="Montserrat, Arial, sans-serif" font-size="48" font-weight="600">' . esc($initials) . '</text>';
    $svg .= '</svg>';
    
    // Convert SVG to data URI
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

function dominium_ensure_listing_province_column(): void
{
    dominium_ensure_schema();
}

function dominium_listings(?int $limit = null, int $offset = 0): array
{
    dominium_ensure_schema();

    $query = db()->table('listings')
        ->where('is_active', 1)
        ->where('is_approved', 1)
        ->order_by('created_at', 'DESC');
    
    if ($limit) {
        $query->limit($limit)->offset($offset);
    }
    
    return $query->get_all() ?: [];
}

function dominium_listing_by_id(int|string $id): ?array
{
    dominium_ensure_schema();

    return db()->table('listings')
        ->where('id', $id)
        ->where('is_active', 1)
        ->where('is_approved', 1)
        ->get() ?: null;
}

function dominium_search_listings(string $query): array
{
    dominium_ensure_schema();

    $q = '%' . $query . '%';
    
    return db()->table('listings')
        ->where('is_active', 1)
        ->where('is_approved', 1)
        ->grouped(function($db) use ($q) {
            $db->like('title', $q)
               ->or_like('province', $q)
               ->or_like('city', $q)
               ->or_like('category', $q)
               ->or_like('description', $q);
        })
        ->order_by('created_at', 'DESC')
        ->get_all() ?: [];
}

function dominium_bookings_for_user(int $user_id, int $limit = 50, int $offset = 0): array
{
    $limit = (int) $limit;
    $offset = (int) $offset;

    return db()->table('bookings b')
        ->select('b.id, b.listing_id, b.user_id, b.guest_name, b.guest_email, b.checkin, b.checkout, b.price, b.status, b.booked_at, l.title as listing_title, l.province, l.city, l.thumbnail, l.category, l.bedrooms, l.guests')
        ->join('listings l', 'b.listing_id = l.id', 'LEFT ')
        ->where('b.user_id', $user_id)
        ->order_by('b.booked_at', 'DESC')
        ->limit($limit, $offset)
        ->get_all() ?: [];
}

function dominium_booking_count_for_listing(int $listing_id): int
{
    return db()->table('bookings')
        ->where('listing_id', $listing_id)
        ->count();
}

function dominium_listing_for_user(int $user_id, int|string $id): ?array
{
    dominium_ensure_schema();

    return db()->table('listings')
        ->where('id', $id)
        ->where('user_id', $user_id)
        ->get() ?: null;
}

function dominium_listings_by_user(int $user_id): array
{
    dominium_ensure_schema();

    return db()->table('listings')
        ->where('user_id', $user_id)
        ->order_by('created_at', 'DESC')
        ->get_all() ?: [];
}

function dominium_save_booking(array $data): int
{
    return db()->table('bookings')->insert($data);
}

function dominium_check_booking_availability(int $listing_id, string $checkin, string $checkout): bool
{
    $existing_bookings = db()->table('bookings')
        ->where('listing_id', $listing_id)
        ->where('status', 'approved')
        ->get_all() ?: [];

    $new_checkin = strtotime($checkin);
    $new_checkout = strtotime($checkout);

    foreach ($existing_bookings as $booking) {
        $existing_checkin = strtotime($booking['checkin']);
        $existing_checkout = strtotime($booking['checkout']);

        // Check for overlap
        if ($new_checkin < $existing_checkout && $new_checkout > $existing_checkin) {
            return false;
        }
    }

    return true;
}

function dominium_is_listing_booked(int $listing_id): bool
{
    $existing_bookings = db()->table('bookings')
        ->where('listing_id', $listing_id)
        ->where('status', 'approved')
        ->where('checkout', '>', date('Y-m-d'))
        ->get_all() ?: [];

    return !empty($existing_bookings);
}

function dominium_get_form_data(): array
{
    $data = $_SESSION['form_data'] ?? [];
    unset($_SESSION['form_data']);
    return $data;
}

function dominium_form_value(string $field, $default = '')
{
    $data = $_SESSION['form_data'] ?? [];
    return $data[$field] ?? $default;
}

// Centralized form validation helper
function dominium_validate_listing_form(array $data, bool $isEdit = false): array
{
    $errors = [];
    
    // Required fields
    $required = ['title', 'province', 'city', 'price', 'description'];
    foreach ($required as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            $errors[] = ucfirst($field) . ' is required.';
        }
    }
    
    // Category validation
    $category = trim($data['category'] ?? '');
    if (!empty($category) && !dominium_valid_listing_category($category)) {
        $errors[] = 'Please choose a valid category.';
    }
    
    // Numeric validation
    $price = trim($data['price'] ?? '');
    if (!empty($price) && (!is_numeric($price) || (float)$price <= 0)) {
        $errors[] = 'Price must be a positive number.';
    }
    
    $bedrooms = trim($data['bedrooms'] ?? '');
    if (!empty($bedrooms) && (!is_numeric($bedrooms) || (int)$bedrooms < 1)) {
        $errors[] = 'Bedrooms must be at least 1.';
    }
    
    $guests = trim($data['guests'] ?? '');
    if (!empty($guests) && (!is_numeric($guests) || (int)$guests < 1)) {
        $errors[] = 'Guests must be at least 1.';
    }
    
    return $errors;
}

function dominium_handle_form_error(string $redirectUrl, array $errors, array $formData = []): void
{
    if (!empty($formData)) {
        $_SESSION['form_data'] = $formData;
    }
    $_SESSION['validation_errors'] = $errors;
    set_flash('error', implode(' ', $errors));
    header('Location: ' . url($redirectUrl));
    exit;
}

function dominium_get_validation_errors(): array
{
    $errors = $_SESSION['validation_errors'] ?? [];
    unset($_SESSION['validation_errors']);
    return $errors;
}

// Favorites/Wishlist Functions
function dominium_add_favorite(int $userId, int $listingId): bool
{
    try {
        error_log("dominium_add_favorite: Attempting to add favorite - user_id=$userId, listing_id=$listingId");
        
        // Check if already favorited
        $existing = db()->table('favorites')
            ->where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->count();
            
        if ($existing > 0) {
            error_log("dominium_add_favorite: Already favorited");
            return true; // Already favorited, consider success
        }
        
        $result = db()->table('favorites')->insert(['user_id' => $userId, 'listing_id' => $listingId]);
        $success = $result > 0;
        
        error_log("dominium_add_favorite: Insert result=$result, success=" . ($success ? 'true' : 'false'));
        
        return $success;
    } catch (Exception $e) {
        error_log("dominium_add_favorite: Exception - " . $e->getMessage());
        return false;
    }
}

function dominium_remove_favorite(int $userId, int $listingId): bool
{
    return db()->table('favorites')->where('user_id', $userId)->where('listing_id', $listingId)->delete() > 0;
}

function dominium_is_favorite(int $userId, int $listingId): bool
{
    return db()->table('favorites')->where('user_id', $userId)->where('listing_id', $listingId)->count() > 0;
}

function dominium_user_favorites(int $userId, int $limit = 50, int $offset = 0): array
{
    $limit = (int) $limit;
    $offset = (int) $offset;
    
    $results = db()->table('favorites f')
        ->select('f.listing_id, f.created_at, l.id, l.user_id, l.title, l.province, l.city, l.price, l.category, l.bedrooms, l.guests, l.description, l.thumbnail, l.is_active, l.is_approved, l.created_at as listing_created_at')
        ->join('listings l', 'f.listing_id = l.id', 'INNER ')
        ->where('f.user_id', $userId)
        ->order_by('f.created_at', 'DESC')
        ->limit($limit)
        ->offset($offset)
        ->get_all() ?: [];
    
    // Normalize column names for view compatibility
    foreach ($results as &$row) {
        $row['favorited_at'] = $row['created_at'];
        $row['id'] = $row['listing_id'];
    }
    
    return $results;
}

function dominium_favorites_count(int $userId): int
{
    return db()->table('favorites')->where('user_id', $userId)->count();
}

function dominium_listing_favorites_count(int $listingId): int
{
    return db()->table('favorites')->where('listing_id', $listingId)->count();
}

// User Preferences Functions
function dominium_get_user_preferences(int $userId): array
{
    $preferences = db()->table('user_preferences')->where('user_id', $userId)->get();
    
    if (!$preferences) {
        // Create default preferences
        db()->table('user_preferences')->insert([
            'user_id' => $userId,
            'price_alerts_enabled' => true,
            'email_notifications' => true
        ]);
        
        return [
            'price_alerts_enabled' => true,
            'email_notifications' => true,
            'push_notifications' => false,
            'min_price_alert' => null,
            'max_price_alert' => null,
            'preferred_categories' => null,
            'preferred_locations' => null
        ];
    }
    
    // Decode JSON fields
    if ($preferences['preferred_categories']) {
        $preferences['preferred_categories'] = json_decode($preferences['preferred_categories'], true);
    }
    if ($preferences['preferred_locations']) {
        $preferences['preferred_locations'] = json_decode($preferences['preferred_locations'], true);
    }
    
    return $preferences;
}

function dominium_update_user_preferences(int $userId, array $preferences): bool
{
    $allowed = ['email_notifications', 'price_alerts_enabled', 'push_notifications', 
                'min_price_alert', 'max_price_alert', 'preferred_categories', 'preferred_locations'];
    $updateData = [];
    
    foreach ($allowed as $field) {
        if (isset($preferences[$field])) {
            // Encode JSON fields
            if (in_array($field, ['preferred_categories', 'preferred_locations']) && is_array($preferences[$field])) {
                $updateData[$field] = json_encode($preferences[$field]);
            } else {
                $updateData[$field] = $preferences[$field];
            }
        }
    }
    
    if (empty($updateData)) return false;
    
    return db()->table('user_preferences')
        ->where('user_id', $userId)
        ->update($updateData) > 0;
}

// Price Alerts Functions
function dominium_check_price_alerts(int $listingId, float $oldPrice, float $newPrice): void
{
    // Get all users who favorited this listing and have price alerts enabled
    $users = db()->table('favorites f')
        ->select('f.user_id, up.email_notifications, up.price_alerts_enabled')
        ->join('user_preferences up', 'f.user_id = up.user_id')
        ->where('f.listing_id', $listingId)
        ->where('up.price_alerts_enabled', 1)
        ->get_all() ?: [];
    
    if (empty($users)) return;
    
    $priceChange = $newPrice - $oldPrice;
    $priceChangePercent = abs(($priceChange / $oldPrice) * 100);
    $priceChangeType = $priceChange < 0 ? 'decrease' : 'increase';
    
    // Create price alert records
    foreach ($users as $user) {
        db()->table('price_alerts')->insert([
            'user_id' => $user['user_id'],
            'listing_id' => $listingId,
            'original_price' => $oldPrice,
            'new_price' => $newPrice,
            'price_change_type' => $priceChangeType,
            'price_change_amount' => abs($priceChange),
            'price_change_percentage' => $priceChangePercent
        ]);
        
        // Send email notification if enabled
        if ($user['email_notifications']) {
            $userData = db()->table('users')->where('id', $user['user_id'])->get();
            if ($userData) {
                $subject = "Price Alert: " . ($priceChangeType === 'decrease' ? 'Price Drop!' : 'Price Increase');
                $message = "The price for a property you favorited has changed from ₱$oldPrice to ₱$newPrice.\n\n";
                $message .= "Change: " . number_format($priceChangePercent, 1) . "% " . $priceChangeType . "\n";
                $message .= "View listing: " . url('listings/' . $listingId) . "\n";
                @mail($userData['email'], $subject, $message, 'From: noreply@' . parse_url(APP_URL, PHP_URL_HOST));
            }
        }
    }
}

function dominium_user_price_alerts(int $userId, int $limit = 20): array
{
    return db()->table('price_alerts pa')
        ->select('pa.id, pa.user_id, pa.listing_id, pa.original_price, pa.new_price, pa.price_change_type, pa.price_change_amount, pa.price_change_percentage, pa.is_notified, pa.created_at, l.title, l.city, l.province, l.price as current_price')
        ->join('listings l', 'pa.listing_id = l.id', 'INNER ')
        ->where('pa.user_id', $userId)
        ->order_by('pa.created_at', 'DESC')
        ->limit($limit)
        ->get_all() ?: [];
}

// Activity Tracking Functions
function dominium_track_user_activity(int $userId, string $activityType, ?int $listingId = null, array $activityData = []): void
{
    dominium_ensure_schema();
    
    db()->table('user_activity')->insert([
        'user_id' => $userId,
        'activity_type' => $activityType,
        'listing_id' => $listingId,
        'activity_data' => empty($activityData) ? null : json_encode($activityData),
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

// Analytics Data Collection Functions

/**
 * Record analytics data for dashboard
 */
function dominium_record_analytics(string $metricType, int $value, ?string $date = null): void
{
    dominium_ensure_schema();
    
    $date = $date ?: date('Y-m-d');
    
    // Check if record exists for this metric and date
    $existing = db()->table('analytics')
        ->where('metric_type', $metricType)
        ->where('recorded_date', $date)
        ->get();
    
    if ($existing) {
        // Update existing record
        db()->table('analytics')
            ->where('id', $existing['id'])
            ->update(['metric_value' => $value]);
    } else {
        // Insert new record
        db()->table('analytics')->insert([
            'metric_type' => $metricType,
            'metric_value' => $value,
            'recorded_date' => $date
        ]);
    }
}

/**
 * Get analytics data for charts
 */
function dominium_get_analytics_data(string $metricType, int $days = 30): array
{
    dominium_ensure_schema();
    
    $startDate = date('Y-m-d', strtotime("-$days days"));
    
    return db()->table('analytics')
        ->where('metric_type', $metricType)
        ->where('recorded_date', '>=', $startDate)
        ->order_by('recorded_date', 'ASC')
        ->get_all() ?: [];
}

/**
 * Get current statistics for dashboard
 */
function dominium_get_dashboard_stats(): array
{
    dominium_ensure_schema();
    
    $stats = [
        'users_total' => 0,
        'users_active' => 0,
        'users_new_today' => 0,
        'listings_total' => 0,
        'listings_approved' => 0,
        'listings_pending' => 0,
        'listings_new_today' => 0,
        'bookings_total' => 0,
        'bookings_pending' => 0,
        'bookings_approved' => 0,
        'bookings_today' => 0,
        'favorites_total' => 0,
        'favorites_today' => 0,
        'revenue_total' => 0,
        'revenue_today' => 0,
        'users_banned' => 0
    ];
    
    try {
        // Total users
        $stats['users_total'] = db()->table('users')->count() ?: 0;
        $stats['users_active'] = db()->table('users')
            ->where('is_banned', 0)
            ->count() ?: 0;
        $stats['users_new_today'] = db()->table('users')
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))
            ->count() ?: 0;
        
        // Total listings
        $stats['listings_total'] = db()->table('listings')
            ->where('is_active', 1)
            ->count() ?: 0;
        $stats['listings_approved'] = db()->table('listings')
            ->where('is_active', 1)
            ->where('is_approved', 1)
            ->count() ?: 0;
        $stats['listings_pending'] = db()->table('listings')
            ->where('is_approved', 0)
            ->count() ?: 0;
        $stats['listings_new_today'] = db()->table('listings')
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))
            ->count() ?: 0;
        
        // Total bookings
        $stats['bookings_total'] = db()->table('bookings')->count() ?: 0;
        $stats['bookings_pending'] = 0;
        $stats['bookings_approved'] = db()->table('bookings')
            ->where('status', 'approved')
            ->count() ?: 0;
        $stats['bookings_today'] = db()->table('bookings')
            ->where('booked_at', '>=', date('Y-m-d 00:00:00'))
            ->count() ?: 0;
        
        // Total favorites
        $stats['favorites_total'] = db()->table('favorites')->count() ?: 0;
        $stats['favorites_today'] = db()->table('favorites')
            ->where('created_at', '>=', date('Y-m-d 00:00:00'))
            ->count() ?: 0;
        
        // Revenue (from approved bookings)
        $stats['revenue_total'] = db()->table('bookings')
            ->where('status', 'approved')
            ->sum('price') ?: 0;
        $stats['revenue_today'] = db()->table('bookings')
            ->where('status', 'approved')
            ->where('booked_at', '>=', date('Y-m-d 00:00:00'))
            ->sum('price') ?: 0;
        
        // Banned users
        $bannedRow = db()->raw(
            'SELECT COUNT(*) AS count FROM users WHERE is_banned = ? OR banned_until IS NOT NULL',
            [1]
        )->fetch(PDO::FETCH_ASSOC);
        $stats['users_banned'] = (int)($bannedRow['count'] ?? 0);
            
    } catch (Exception $e) {
        // Return default stats if database query fails
        error_log('Error getting dashboard stats: ' . $e->getMessage());
    }
    
    return $stats;
}

/**
 * Get listings by category for pie chart
 */
function dominium_get_listings_by_category(): array
{
    dominium_ensure_schema();
    
    // Try with approval filters first
    $data = db()->table('listings')
        ->select('category, COUNT(*) as count')
        ->where('is_active', 1)
        ->where('is_approved', 1)
        ->group_by('category')
        ->order_by('count', 'DESC')
        ->get_all();
    
    // If no approved listings, try all listings
    if (empty($data)) {
        $data = db()->table('listings')
            ->select('category, COUNT(*) as count')
            ->group_by('category')
            ->order_by('count', 'DESC')
            ->get_all();
    }
    
    return $data ?: [];
}

/**
 * Get user registrations by month for line chart
 */
function dominium_get_user_registrations_by_month(int $months = 12): array
{
    dominium_ensure_schema();
    
    $data = [];
    for ($i = $months - 1; $i >= 0; $i--) {
        $monthStart = date('Y-m-01', strtotime("-$i months"));
        $monthEnd = date('Y-m-t', strtotime("-$i months"));
        
        // Count all users including email and Google OAuth registrations
        $count = db()->table('users')
            ->where('created_at', '>=', $monthStart . ' 00:00:00')
            ->where('created_at', '<=', $monthEnd . ' 23:59:59')
            ->where('auth_provider', 'email')  // Email registrations
            ->count() ?: 0;
            
        $googleCount = db()->table('users')
            ->where('created_at', '>=', $monthStart . ' 00:00:00')
            ->where('created_at', '<=', $monthEnd . ' 23:59:59')
            ->where('auth_provider', 'google')  // Google OAuth registrations
            ->count() ?: 0;
        
        $totalCount = $count + $googleCount;
        
        $data[] = [
            'month' => date('M Y', strtotime($monthStart)),
            'count' => $totalCount,
            'email_count' => $count,
            'google_count' => $googleCount
        ];
    }
    
    return $data;
}

/**
 * Get booking trends for line chart
 */
function dominium_get_booking_trends(int $days = 30): array
{
    dominium_ensure_schema();
    
    $data = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        
        $count = db()->table('bookings')
            ->where('booked_at', '>=', $date . ' 00:00:00')
            ->where('booked_at', '<=', $date . ' 23:59:59')
            ->count() ?: 0;
        
        $data[] = [
            'date' => date('M j', strtotime($date)),
            'count' => $count
        ];
    }
    
    return $data;
}


/**
 * Get real daily revenue trends from approved bookings.
 */
function dominium_get_revenue_trends(int $days = 30): array
{
    dominium_ensure_schema();

    $days = max(1, min($days, 365));
    $startDate = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
    $endDate = date('Y-m-d');
    $revenueByDate = [];

    try {
        $rows = db()->raw(
            "SELECT
                DATE(booked_at) AS revenue_date,
                COALESCE(SUM(price), 0) AS revenue,
                COUNT(*) AS bookings
             FROM bookings
             WHERE status = ?
               AND booked_at >= ?
               AND booked_at <= ?
             GROUP BY DATE(booked_at)
             ORDER BY revenue_date ASC",
            [
                'approved',
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $revenueByDate[$row['revenue_date']] = [
                'revenue' => (float) $row['revenue'],
                'bookings' => (int) $row['bookings']
            ];
        }
    } catch (Exception $e) {
        error_log('Error getting revenue trends: ' . $e->getMessage());
    }

    $data = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $data[] = [
            'date' => $date,
            'label' => date('M j', strtotime($date)),
            'revenue' => $revenueByDate[$date]['revenue'] ?? 0,
            'bookings' => $revenueByDate[$date]['bookings'] ?? 0
        ];
    }

    return $data;
}

/**
 * Update analytics data (call this daily via cron)
 */
function dominium_update_analytics(): void
{
    $today = date('Y-m-d');
    
    // Update user count
    $userCount = db()->table('users')->count();
    dominium_record_analytics('users_count', $userCount, $today);
    
    // Update listing count
    $listingCount = db()->table('listings')
        ->where('is_active', 1)
        ->where('is_approved', 1)
        ->count();
    dominium_record_analytics('listings_count', $listingCount, $today);
    
    // Update booking count
    $bookingCount = db()->table('bookings')->count();
    dominium_record_analytics('bookings_count', $bookingCount, $today);
    
    // Update favorites count
    $favoritesCount = db()->table('favorites')->count();
    dominium_record_analytics('favorites_count', $favoritesCount, $today);
}

/**
 * Name Change Functions with 30-day cooldown
 */

/**
 * Check if user can change their name (30-day cooldown)
 */
function dominium_can_change_name(int $userId): bool
{
    $user = dominium_user_by_id($userId);
    if (!$user) return false;
    
    // Admins can always change names
    if ($user['role'] === 'admin') return true;
    
    // If never changed name, can change
    if (!$user['name_changed_at']) return true;
    
    // Check if 30 days have passed
    $daysSinceChange = (time() - strtotime($user['name_changed_at'])) / (24 * 60 * 60);
    return $daysSinceChange >= 30;
}

/**
 * Get remaining days until user can change name again
 */
function dominium_get_name_change_cooldown(int $userId): ?int
{
    $user = dominium_user_by_id($userId);
    if (!$user) return null;
    
    // Admins have no cooldown
    if ($user['role'] === 'admin') return 0;
    
    // If never changed name, no cooldown
    if (!$user['name_changed_at']) return 0;
    
    $secondsSinceChange = time() - strtotime($user['name_changed_at']);
    $cooldownSeconds = 30 * 24 * 60 * 60; // 30 days
    
    if ($secondsSinceChange >= $cooldownSeconds) {
        return 0; // Cooldown expired
    }
    
    return ceil(($cooldownSeconds - $secondsSinceChange) / (24 * 60 * 60)); // Remaining days
}

/**
 * Change user's name with cooldown validation
 */
function dominium_change_user_name(int $userId, string $firstName, string $lastName): array
{
    $user = dominium_user_by_id($userId);
    if (!$user) {
        return ['success' => false, 'message' => 'User not found.'];
    }
    
    // Validate input
    $firstName = trim($firstName);
    $lastName = trim($lastName);
    
    if (empty($firstName)) {
        return ['success' => false, 'message' => 'First name is required.'];
    }
    if (empty($lastName)) {
        return ['success' => false, 'message' => 'Last name is required.'];
    }
    if (strlen($firstName) > 100) {
        return ['success' => false, 'message' => 'First name is too long (max 100 characters).'];
    }
    if (strlen($lastName) > 100) {
        return ['success' => false, 'message' => 'Last name is too long (max 100 characters).'];
    }
    
    // Check if name actually changed
    if ($user['first_name'] === $firstName && $user['last_name'] === $lastName) {
        return ['success' => false, 'message' => 'Name is the same as current name.'];
    }
    
    // Check cooldown (except for admins)
    if ($user['role'] !== 'admin' && !dominium_can_change_name($userId)) {
        $remainingDays = dominium_get_name_change_cooldown($userId);
        return ['success' => false, 'message' => "You must wait $remainingDays more day(s) before changing your name again."];
    }
    
    // Update name
    $result = db()->table('users')
        ->where('id', $userId)
        ->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name_changed_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    
    if ($result) {
        // Update session if this is the current user
        if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $userId) {
            $_SESSION['user']['name'] = $firstName . ' ' . $lastName;
        }
        
        return ['success' => true, 'message' => 'Name changed successfully.'];
    } else {
        return ['success' => false, 'message' => 'Failed to update name. Please try again.'];
    }
}

/**
 * Get name change history for a user
 */
function dominium_get_name_change_history(int $userId): array
{
    // This would require a separate name_change_history table
    // For now, return current info
    $user = dominium_user_by_id($userId);
    if (!$user) return [];
    
    return [
        [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'changed_at' => $user['name_changed_at'],
            'is_current' => true
        ]
    ];
}

// Rate limiting helper
function check_rate_limit(string $key, int $maxAttempts = 5, int $windowSeconds = 300): bool
{
    $now = time();
    $attempts = $_SESSION['rate_limit'][$key] ?? ['count' => 0, 'reset' => $now + $windowSeconds];

    if ($now > $attempts['reset']) {
        $attempts = ['count' => 0, 'reset' => $now + $windowSeconds];
    }

    $attempts['count']++;
    $_SESSION['rate_limit'][$key] = $attempts;

    return $attempts['count'] <= $maxAttempts;
}

function get_rate_limit_remaining(string $key): int
{
    $attempts = $_SESSION['rate_limit'][$key] ?? ['count' => 0];
    return max(0, 5 - ($attempts['count'] ?? 0));
}

// Input sanitization helper
function input(string $key, $default = '', string $type = 'string'): mixed
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;

    return match ($type) {
        'string' => trim(htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')),
        'int' => filter_var($value, FILTER_VALIDATE_INT) ?: 0,
        'float' => filter_var($value, FILTER_VALIDATE_FLOAT) ?: 0.0,
        'email' => filter_var($value, FILTER_SANITIZE_EMAIL),
        'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
        'url' => filter_var($value, FILTER_VALIDATE_URL),
        default => trim((string)$value),
    };
}