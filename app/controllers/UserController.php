<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

class UserController
{
    public static function profile(): void
    {
        include APP_ROOT . '/app/views/user/profile.php';
    }
    
    public static function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('profile'));
            exit;
        }
        
        $user = auth_user();
        if (!$user) {
            set_flash('error', 'Please login to continue.');
            header('Location: ' . url('login'));
            exit;
        }
        
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        
        // Validate inputs
        $errors = [];
        if (empty($firstName)) {
            $errors[] = 'First name is required.';
        }
        if (empty($lastName)) {
            $errors[] = 'Last name is required.';
        }
        if (strlen($firstName) > 100) {
            $errors[] = 'First name is too long (max 100 characters).';
        }
        if (strlen($lastName) > 100) {
            $errors[] = 'Last name is too long (max 100 characters).';
        }
        
        if (!empty($errors)) {
            $_SESSION['profile_errors'] = $errors;
            $_SESSION['profile_form_data'] = $_POST;
            header('Location: ' . url('profile'));
            exit;
        }
        
        // Update user profile
        try {
            if (defined('IS_DEV') && IS_DEV) {
                error_log('Profile Update: Updating user ID ' . $user['id'] . ' with data: ' . json_encode([
                    'first_name' => $firstName,
                    'last_name' => $lastName
                ]));
            }
            
            $result = db()->table('users')
                ->where('id', $user['id'])
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            
            if (defined('IS_DEV') && IS_DEV) {
                error_log('Profile Update: Database update result: ' . ($result ? 'Success' : 'Failed'));
            }
            
            if ($result) {
                // Update session
                $_SESSION['user']['first_name'] = $firstName;
                $_SESSION['user']['last_name'] = $lastName;
                $_SESSION['user']['name'] = $firstName . ' ' . $lastName;
                
                if (defined('IS_DEV') && IS_DEV) {
                    error_log('Profile Update: Session updated with new data');
                }
                
                set_flash('success', 'Profile updated successfully!');
            } else {
                set_flash('error', 'Failed to update profile. Please try again.');
            }
        } catch (Exception $e) {
            if (defined('IS_DEV') && IS_DEV) {
                error_log('Profile Update: Database error: ' . $e->getMessage());
            }
            set_flash('error', 'Database error occurred. Please try again.');
        }
        
        header('Location: ' . url('profile'));
        exit;
    }
    
    public static function changeName(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('profile'));
            exit;
        }
        
        $user = auth_user();
        if (!$user) {
            set_flash('error', 'Please login to continue.');
            header('Location: ' . url('login'));
            exit;
        }
        
        $newFirstName = trim($_POST['first_name'] ?? '');
        $newLastName = trim($_POST['last_name'] ?? '');
        
        // Use the helper function to change name
        $result = dominium_change_user_name($user['id'], $newFirstName, $newLastName);
        
        if ($result['success']) {
            set_flash('success', $result['message']);
        } else {
            set_flash('error', $result['message']);
        }
        
        header('Location: ' . url('profile'));
        exit;
    }
}
