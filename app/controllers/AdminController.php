<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

class AdminController
{
    public static function dashboard(): void
    {
        // Redirect to analytics dashboard for cleaner admin experience
        header('Location: ' . url('admin/analytics'));
        exit;
    }

    public static function listers(): void
    {
        // Ensure lister_application_pending column exists
        try {
            db()->raw("SELECT lister_application_pending FROM users LIMIT 1");
        } catch (Exception $e) {
            db()->raw("ALTER TABLE users ADD COLUMN lister_application_pending BOOLEAN DEFAULT 0");
        }

        $listers = db()->table('users')
            ->where('lister_application_pending', 1)
            ->order_by('created_at', 'DESC')
            ->get_all() ?: [];

        include APP_ROOT . '/app/views/admin/listers.php';
    }

    public static function analytics(): void
    {
        include APP_ROOT . '/app/views/admin/analytics.php';
    }

    public static function analyticsData(): void
    {
        header('Content-Type: application/json');
        
        try {
            $type = $_GET['type'] ?? 'stats';
            
            switch ($type) {
                case 'stats':
                    echo json_encode(dominium_get_dashboard_stats());
                    break;
                case 'listings_by_category':
                    echo json_encode(dominium_get_listings_by_category());
                    break;
                case 'user_registrations':
                    $months = (int)($_GET['months'] ?? 12);
                    echo json_encode(dominium_get_user_registrations_by_month($months));
                    break;
                case 'booking_trends':
                    $days = (int)($_GET['days'] ?? 30);
                    echo json_encode(dominium_get_booking_trends($days));
                    break;
                case 'revenue_trends':
                    $days = (int)($_GET['days'] ?? 30);
                    echo json_encode(dominium_get_revenue_trends($days));
                    break;
                case 'analytics_data':
                    $metricType = $_GET['metric'] ?? 'users_count';
                    $days = (int)($_GET['days'] ?? 30);
                    $data = self::getAnalyticsData($metricType, $days);
                    echo json_encode($data);
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid analytics type']);
            }
        } catch (Exception $e) {
            error_log('Analytics data error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => (defined('IS_DEV') && IS_DEV) ? 'Database error: ' . $e->getMessage() : 'Analytics data is temporarily unavailable.']);
        }
    }

    private static function getAnalyticsData(string $metricType, int $days = 30): array
    {
        $startDate = date('Y-m-d', strtotime("-$days days"));
        $data = [];
        
        try {
            switch ($metricType) {
                case 'users_count':
                    // Generate daily user count data
                    for ($i = 0; $i < $days; $i++) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $count = db()->table('users')
                            ->where('created_at', '<=', $date . ' 23:59:59')
                            ->count() ?: 0;
                        
                        $data[] = [
                            'date' => $date,
                            'value' => $count
                        ];
                    }
                    break;
                    
                case 'listings_count':
                    // Generate daily listings count data
                    for ($i = 0; $i < $days; $i++) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $count = db()->table('listings')
                            ->where('created_at', '<=', $date . ' 23:59:59')
                            ->count() ?: 0;
                        
                        $data[] = [
                            'date' => $date,
                            'value' => $count
                        ];
                    }
                    break;
                    
                case 'bookings_count':
                    // Generate daily bookings count data
                    for ($i = 0; $i < $days; $i++) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $count = db()->table('bookings')
                            ->where('booked_at', '<=', $date . ' 23:59:59')
                            ->count() ?: 0;
                        
                        $data[] = [
                            'date' => $date,
                            'value' => $count
                        ];
                    }
                    break;
                    
                case 'favorites_count':
                    // Generate daily favorites count data
                    for ($i = 0; $i < $days; $i++) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $count = db()->table('favorites')
                            ->where('created_at', '<=', $date . ' 23:59:59')
                            ->count() ?: 0;
                        
                        $data[] = [
                            'date' => $date,
                            'value' => $count
                        ];
                    }
                    break;
                    
                default:
                    return [];
            }
            
            // Reverse array to show oldest to newest
            return array_reverse($data);
            
        } catch (Exception $e) {
            return [];
        }
    }

    public static function banUser($id = null): void
    {
        $userId = (int)($id ?? $_POST['user_id'] ?? 0);
        $duration = $_POST['duration'] ?? 'permanent';
        $reason = trim($_POST['reason'] ?? '');
        
        if ($userId <= 0) {
            set_flash('error', 'Invalid user ID.');
            header('Location: ' . url('admin/users'));
            exit;
        }
        
        $targetUser = dominium_user_by_id($userId);
        if (!$targetUser || ($targetUser['role'] ?? '') === 'admin') {
            set_flash('error', 'Admin accounts cannot be banned from this panel.');
            header('Location: ' . url('admin/users'));
            exit;
        }

        if (dominium_ban_user($userId, $reason, $duration)) {
            db()->table('listings')
                ->where('user_id', $userId)
                ->update(['is_active' => 0]);
            set_flash('success', 'User has been banned and their listings were paused.');
        } else {
            set_flash('error', 'Failed to ban user.');
        }
        
        header('Location: ' . url('admin/users'));
        exit;
    }

    public static function unbanUser($id = null): void
    {
        $userId = (int)($id ?? $_POST['user_id'] ?? 0);
        
        if ($userId <= 0) {
            set_flash('error', 'Invalid user ID.');
            header('Location: ' . url('admin/users'));
            exit;
        }
        
        if (!dominium_user_by_id($userId)) {
            set_flash('error', 'User not found.');
            header('Location: ' . url('admin/users'));
            exit;
        }

        if (dominium_unban_user($userId)) {
            set_flash('success', 'User has been unbanned successfully.');
        } else {
            set_flash('error', 'Failed to unban user.');
        }
        
        header('Location: ' . url('admin/users'));
        exit;
    }

    public static function approveLister($id): void
    {
        // Ensure lister_application_pending column exists
        try {
            db()->raw("SELECT lister_application_pending FROM users LIMIT 1");
        } catch (Exception $e) {
            db()->raw("ALTER TABLE users ADD COLUMN lister_application_pending BOOLEAN DEFAULT 0");
        }

        db()->table('users')
            ->where('id', $id)
            ->where('lister_application_pending', 1)
            ->update([
                'role' => 'lister',
                'is_approved' => 1,
                'lister_application_pending' => 0
            ]);

        set_flash('success', 'Lister approved successfully.');
        header('Location: ' . url('admin/listers'));
        exit;
    }

    public static function rejectLister($id): void
    {
        // Ensure lister_application_pending column exists
        try {
            db()->raw("SELECT lister_application_pending FROM users LIMIT 1");
        } catch (Exception $e) {
            db()->raw("ALTER TABLE users ADD COLUMN lister_application_pending BOOLEAN DEFAULT 0");
        }

        db()->table('users')
            ->where('id', $id)
            ->where('lister_application_pending', 1)
            ->update(['lister_application_pending' => 0]);

        set_flash('success', 'Lister application rejected.');
        header('Location: ' . url('admin/listers'));
        exit;
    }

    public static function users(): void
    {
        dominium_ensure_user_banned_column();

        $users = db()->table('users')
            ->where('role', '!=', 'admin')
            ->order_by('created_at', 'DESC')
            ->get_all() ?: [];

        include APP_ROOT . '/app/views/admin/users.php';
    }

    public static function demoteUser($id): void
    {
        dominium_ensure_user_banned_column();

        $user = db()->table('users')
            ->where('id', $id)
            ->where('role', 'lister')
            ->get();

        if ($user) {
            db()->table('users')
                ->where('id', $id)
                ->update(['role' => 'renter', 'is_approved' => 0]);

            db()->table('listings')
                ->where('user_id', $id)
                ->update(['is_active' => 0]);

            set_flash('success', 'Lister demoted to renter and their listings were paused.');
        } else {
            set_flash('error', 'User not found or not a lister.');
        }

        header('Location: ' . url('admin/users'));
        exit;
    }

    public static function listings(): void
    {
        dominium_ensure_listing_approval_column();

        $listings = db()->table('listings')
            ->where('is_approved', 0)
            ->order_by('created_at', 'DESC')
            ->get_all() ?: [];

        include APP_ROOT . '/app/views/admin/listings.php';
    }

    public static function approveListing($id): void
    {
        dominium_ensure_listing_approval_column();

        $listing = db()->table('listings')->where('id', $id)->get();
        if (!$listing) {
            set_flash('error', 'Listing not found.');
            header('Location: ' . url('admin/listings'));
            exit;
        }

        $owner = dominium_user_by_id((int)$listing['user_id']);
        if (!$owner || ($owner['role'] ?? '') !== 'lister' || empty($owner['is_approved']) || !empty($owner['is_banned'])) {
            set_flash('error', 'Listing cannot be approved because the owner is not an approved active lister.');
            header('Location: ' . url('admin/listings'));
            exit;
        }

        db()->table('listings')
            ->where('id', $id)
            ->update(['is_active' => 1, 'is_approved' => 1]);

        set_flash('success', 'Listing approved successfully.');
        header('Location: ' . url('admin/listings'));
        exit;
    }

    public static function rejectListing($id): void
    {
        db()->table('listings')
            ->where('id', $id)
            ->delete();

        set_flash('success', 'Listing rejected and removed.');
        header('Location: ' . url('admin/listings'));
        exit;
    }

    public static function bookings(): void
    {
        $bookings = db()->table('bookings')
            ->where('status', 'pending')
            ->order_by('booked_at', 'DESC')
            ->get_all() ?: [];

        include APP_ROOT . '/app/views/admin/bookings.php';
    }

    public static function approveBooking($id): void
    {
        db()->table('bookings')
            ->where('id', $id)
            ->update(['status' => 'approved']);

        set_flash('success', 'Booking approved.');
        header('Location: ' . url('admin/bookings'));
        exit;
    }

    public static function rejectBooking($id): void
    {
        db()->table('bookings')
            ->where('id', $id)
            ->update(['status' => 'rejected']);

        set_flash('success', 'Booking rejected.');
        header('Location: ' . url('admin/bookings'));
        exit;
    }
}
