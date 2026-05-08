<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

class AuthController
{
    /**
     * Redirect to Google OAuth
     */
    public static function googleAuth(): void
    {
        $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
        $clientSecret = defined('GOOGLE_CLIENT_SECRET') ? GOOGLE_CLIENT_SECRET : '';
        $redirectUri = defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI : 'http://localhost/dominium/auth/google/callback';

        if ($clientId === '' || $clientSecret === '') {
            set_flash('error', 'Google login is not configured yet. Please use email login or set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET.');
            header('Location: ' . url('login'));
            exit;
        }

        $state = bin2hex(random_bytes(16));
        
        $_SESSION['google_oauth_state'] = $state;
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email',
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'select_account'
        ];
        
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        
        header('Location: ' . $authUrl);
        exit;
    }
    
    /**
     * Handle Google OAuth callback
     */
    public static function googleCallback(): void
    {
        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';
        $error = $_GET['error'] ?? '';
        
        // Check for errors
        if ($error) {
            set_flash('error', 'Google authentication failed: ' . $error);
            header('Location: ' . url('login'));
            exit;
        }
        
        // Verify state
        if (!isset($_SESSION['google_oauth_state']) || $state !== $_SESSION['google_oauth_state']) {
            set_flash('error', 'Invalid state parameter. Please try again.');
            header('Location: ' . url('login'));
            exit;
        }
        
        unset($_SESSION['google_oauth_state']);
        
        // Exchange code for access token
        $tokenData = self::exchangeCodeForToken($code);
        if (!$tokenData) {
            set_flash('error', 'Failed to obtain access token from Google.');
            header('Location: ' . url('login'));
            exit;
        }
        
        // Get user info from Google
        $userInfo = self::getGoogleUserInfo($tokenData['access_token']);
        if (!$userInfo) {
            set_flash('error', 'Failed to retrieve user information from Google.');
            header('Location: ' . url('login'));
            exit;
        }
        
        // Find or create user
        $user = self::findOrCreateGoogleUser($userInfo);
        
        if (defined('IS_DEV') && IS_DEV) {
            error_log('Google OAuth: User lookup result: ' . ($user ? 'Found user ID: ' . $user['id'] : 'User not found'));
        }
        
        if (!$user) {
            set_flash('error', 'Failed to create user account.');
            header('Location: ' . url('login'));
            exit;
        }
        
        // Log in the user
        session_regenerate_id(true);
        $_SESSION['user'] = dominium_session_user_payload($user);
        
        if (defined('IS_DEV') && IS_DEV) {
            error_log('Google OAuth: Session data stored for user ID: ' . $user['id'] . ', Email: ' . $user['email']);
        }
        
        set_flash('success', 'Welcome back, ' . $user['name'] . '!');
        header('Location: ' . url('dashboard'));
        exit;
    }
    
    /**
     * Exchange authorization code for access token
     */
    private static function exchangeCodeForToken(string $code): ?array
    {
        $clientId = defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '';
        $clientSecret = defined('GOOGLE_CLIENT_SECRET') ? GOOGLE_CLIENT_SECRET : '';
        $redirectUri = defined('GOOGLE_REDIRECT_URI') ? GOOGLE_REDIRECT_URI : 'http://localhost/dominium/auth/google/callback';

        if ($clientId === '' || $clientSecret === '') {
            return null;
        }
        
        $data = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ];
        
        $options = [
            'http' => [
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $response = file_get_contents('https://oauth2.googleapis.com/token', false, $context);
        
        if ($response === false) {
            return null;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Get user information from Google
     */
    private static function getGoogleUserInfo(string $accessToken): ?array
    {
        $options = [
            'http' => [
                'header' => 'Authorization: Bearer ' . $accessToken,
                'method' => 'GET'
            ]
        ];
        
        $context = stream_context_create($options);
        $response = file_get_contents('https://www.googleapis.com/oauth2/v2/userinfo', false, $context);
        
        if ($response === false) {
            return null;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Find existing Google user or create new one
     */
    private static function findOrCreateGoogleUser(array $userInfo): ?array
    {
        $googleId = $userInfo['id'] ?? '';
        $email = $userInfo['email'] ?? '';
        $name = $userInfo['name'] ?? '';
        $avatar = $userInfo['picture'] ?? '';
        
        if (defined('IS_DEV') && IS_DEV) {
            error_log('Google OAuth: Looking up user - Google ID: ' . $googleId . ', Email: ' . $email);
        }
        
        if (empty($email)) {
            return null;
        }
        
        // Check if user exists by Google ID
        try {
            $existingUser = db()->table('users')->where('google_id', $googleId)->get();
            
            if (defined('IS_DEV') && IS_DEV) {
                error_log('Google OAuth: Existing user by Google ID: ' . ($existingUser ? 'Yes, ID: ' . $existingUser['id'] . ', Email: ' . $existingUser['email'] : 'No'));
            }
        } catch (Exception $e) {
            if (defined('IS_DEV') && IS_DEV) {
                error_log('Google OAuth: Database error during Google ID lookup: ' . $e->getMessage());
            }
            $existingUser = null;
        }
        
        if ($existingUser) {
            // Update timestamp
            db()->table('users')
                ->where('id', $existingUser['id'])
                ->update([
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return $existingUser;
        }
        
        // Check if user exists by email
        $existingEmailUser = db()->table('users')->where('email', $email)->get();
        
        if (defined('IS_DEV') && IS_DEV) {
            error_log('Google OAuth: Existing user by Email: ' . ($existingEmailUser ? 'Yes, ID: ' . $existingEmailUser['id'] : 'No'));
        }
        
        if ($existingEmailUser) {
            // Link Google account to existing user
            db()->table('users')
                ->where('id', $existingEmailUser['id'])
                ->update([
                    'google_id' => $googleId,
                    'auth_provider' => 'google',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            if (defined('IS_DEV') && IS_DEV) {
                error_log('Google OAuth: Linked Google account to existing user ID: ' . $existingEmailUser['id']);
            }
            return $existingEmailUser;
        }
        
        // Create new user
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0] ?: 'User';
        $lastName = $nameParts[1] ?? '';
        
        // Generate random password for Google users (they won't use it)
        $randomPassword = bin2hex(random_bytes(16));
        
        $userId = db()->table('users')->insert([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => password_hash($randomPassword, PASSWORD_DEFAULT),
            'google_id' => $googleId,
            'auth_provider' => 'google',
            'role' => 'renter',
            'is_approved' => 1,
        ]);
        
        if ($userId) {
            return db()->table('users')->where('id', $userId)->get();
        }
        
        // Log error for debugging
        if (defined('IS_DEV') && IS_DEV) {
            error_log('Google OAuth: Failed to create user. Email: ' . $email . ', Google ID: ' . $googleId);
        }
        
        return null;
    }
    public static function showRegister(): void
    {
        include APP_ROOT . '/app/views/auth/register.php';
    }

    public static function register(): void
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $password_confirm = trim($_POST['password_confirm'] ?? '');

        $errors = [];

        if (empty($firstName)) {
            $errors[] = 'First name is required.';
        }
        if (empty($lastName)) {
            $errors[] = 'Last name is required.';
        }
        if (empty($email)) {
            $errors[] = 'Email is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ($password !== $password_confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (dominium_user_by_email($email)) {
            $errors[] = 'Email already registered.';
        }

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . url('register'));
            exit;
        }

        db()->table('users')->insert([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => hash_password($password),
            'role' => 'renter',
            'auth_provider' => 'email',
        ]);

        $user = dominium_user_by_email($email);

        session_regenerate_id(true);
        $_SESSION['user'] = dominium_session_user_payload($user);

        header('Location: ' . url('profile'));
        exit;
    }

    public static function showAuthModal(): void
    {
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        $authMode = substr($requestPath, -9) === '/register' ? 'signup' : 'login';
        $hide_header_auth_modal = true;

        include APP_ROOT . '/app/views/auth/auth-modal.php';
    }

    public static function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $errors = [];

        if (empty($email)) {
            $errors[] = 'Email is required.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            header('Location: ' . url('login'));
            exit;
        }

        $user = dominium_user_by_email($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_errors'] = ['Email or password is incorrect.'];
            header('Location: ' . url('login'));
            exit;
        }

        // Check if user is banned, including temporary bans.
        if (!empty($user['is_banned']) || (!empty($user['banned_until']) && strtotime($user['banned_until']) > time())) {
            $_SESSION['login_errors'] = ['Your account has been banned. Please contact support for assistance.'];
            header('Location: ' . url('banned'));
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = dominium_session_user_payload($user);

        header('Location: ' . url('dashboard'));
        exit;
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
        session_start();
        header('Location: ' . url(''));
        exit;
    }
}
