<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

class HomeController
{
    public static function index(): void
    {
        include APP_ROOT . '/app/views/home/index.php';
    }

    public static function listings(): void
    {
        include APP_ROOT . '/app/views/home/listings.php';
    }

    public static function listing($id): void
    {
        include APP_ROOT . '/app/views/home/listing.php';
    }

    public static function favorites(): void
    {
        include APP_ROOT . '/app/views/home/favorites.php';
    }

    public static function searchApi(): void
    {
        header('Content-Type: application/json');
        
        try {
            $query = trim($_GET['q'] ?? '');
            $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
            $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : PHP_FLOAT_MAX;
            $category = trim($_GET['category'] ?? '');
            $province = trim($_GET['province'] ?? '');
            $city = trim($_GET['city'] ?? '');
            $minGuests = isset($_GET['min_guests']) ? (int)$_GET['min_guests'] : 0;
            $minBedrooms = isset($_GET['min_bedrooms']) ? (int)$_GET['min_bedrooms'] : 0;
            $sortBy = $_GET['sort'] ?? 'relevance';

            // Get base listings
            $listings = $query ? dominium_search_listings($query) : dominium_listings();

            if (!is_array($listings)) {
                error_log("Search API: dominium_search_listings returned non-array for query: $query");
                $listings = [];
            }

        // Apply filters
        $filtered = array_filter($listings, function($listing) use ($minPrice, $maxPrice, $category, $province, $city, $minGuests, $minBedrooms) {
            // Price filter
            if ($listing['price'] < $minPrice || $listing['price'] > $maxPrice) {
                return false;
            }

            // Category filter
            if ($category && $listing['category'] !== $category) {
                return false;
            }

            // Province filter
            if ($province && $listing['province'] !== $province) {
                return false;
            }

            // City filter
            if ($city && $listing['city'] !== $city) {
                return false;
            }

            // Guests filter
            if ($minGuests && $listing['guests'] < $minGuests) {
                return false;
            }

            // Bedrooms filter
            if ($minBedrooms && $listing['bedrooms'] < $minBedrooms) {
                return false;
            }

            return true;
        });
        
        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                usort($filtered, function($a, $b) { return $a['price'] <=> $b['price']; });
                break;
            case 'price_high':
                usort($filtered, function($a, $b) { return $b['price'] <=> $a['price']; });
                break;
            case 'newest':
                usort($filtered, function($a, $b) { return $b['id'] <=> $a['id']; });
                break;
            case 'guests_high':
                usort($filtered, function($a, $b) { return $b['guests'] <=> $a['guests']; });
                break;
            case 'bedrooms_high':
                usort($filtered, function($a, $b) { return $b['bedrooms'] <=> $a['bedrooms']; });
                break;
            default: // relevance
                // Keep original order for search relevance
                break;
        }
        
        $user = auth_user();
        $isAuthenticated = is_authenticated();

        $results = [];
        foreach ($filtered as $listing) {
            $listingId = (int)$listing['id'];
            $results[] = [
                'id' => $listingId,
                'title' => (string)$listing['title'],
                'province' => (string)$listing['province'],
                'city' => (string)$listing['city'],
                'category' => (string)$listing['category'],
                'price' => (float)$listing['price'],
                'guests' => (int)$listing['guests'],
                'bedrooms' => (int)$listing['bedrooms'],
                'thumbnail' => listing_thumbnail_src($listing),
                'is_booked' => dominium_is_listing_booked($listingId),
                'isAuthenticated' => $isAuthenticated,
                'is_favorite' => $isAuthenticated && $user ? dominium_is_favorite((int)$user['id'], $listingId) : false,
                'url' => url('listings/' . $listingId)
            ];
        }
        
        echo json_encode(['results' => $results]);
            
        } catch (Exception $e) {
            error_log("Search API Error: " . $e->getMessage());
            http_response_code(500);
            $response = ['error' => 'Internal server error'];
            if (defined('IS_DEV') && IS_DEV) {
                $response['details'] = $e->getMessage();
            }
            echo json_encode($response);
        }
    }

    // Favorites API methods
    public static function addFavorite(): void
    {
        header('Content-Type: application/json');
        
        $user = auth_user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            return;
        }
        
        $listingId = (int) ($_POST['listing_id'] ?? 0);
        if ($listingId === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid listing ID', 'listing_id' => $listingId]);
            return;
        }
        
        // Debug logging
        error_log("addFavorite: user_id=" . $user['id'] . ", listing_id=" . $listingId);
        
        // Verify listing exists
        $listing = dominium_listing_by_id($listingId);
        if (!$listing) {
            error_log("addFavorite: Listing not found for ID " . $listingId);
            http_response_code(404);
            echo json_encode(['error' => 'Listing not found']);
            return;
        }
        
        try {
            $success = dominium_add_favorite($user['id'], $listingId);
            error_log("addFavorite: dominium_add_favorite result=" . ($success ? 'true' : 'false'));
            
            if ($success) {
                // Track activity
                dominium_track_user_activity($user['id'], 'favorite', $listingId);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Added to favorites',
                    'favorites_count' => dominium_favorites_count($user['id'])
                ]);
            } else {
                error_log("addFavorite: Failed to add favorite");
                echo json_encode(['error' => 'Failed to add to favorites']);
            }
        } catch (Exception $e) {
            error_log("addFavorite: Exception - " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => defined('IS_DEV') && IS_DEV ? 'Database error: ' . $e->getMessage() : 'Unable to update favorites right now.']);
        }
    }

    public static function removeFavorite(): void
    {
        header('Content-Type: application/json');
        
        $user = auth_user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            return;
        }
        
        $listingId = (int) ($_POST['listing_id'] ?? 0);
        if ($listingId === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid listing ID']);
            return;
        }
        
        $success = dominium_remove_favorite($user['id'], $listingId);
        
        if ($success) {
            // Track activity
            dominium_track_user_activity($user['id'], 'unfavorite', $listingId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Removed from favorites',
                'favorites_count' => dominium_favorites_count($user['id'])
            ]);
        } else {
            echo json_encode(['error' => 'Failed to remove from favorites']);
        }
    }

    public static function userFavorites(): void
    {
        header('Content-Type: application/json');
        
        $user = auth_user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            return;
        }
        
        $limit = (int) ($_GET['limit'] ?? 20);
        $offset = (int) ($_GET['offset'] ?? 0);
        
        $favorites = dominium_user_favorites($user['id'], $limit, $offset);
        $totalCount = dominium_favorites_count($user['id']);
        
        $results = [];
        foreach ($favorites as $favorite) {
            $results[] = [
                'id' => $favorite['id'],
                'title' => $favorite['title'],
                'province' => $favorite['province'],
                'city' => $favorite['city'],
                'category' => $favorite['category'],
                'price' => $favorite['price'],
                'guests' => $favorite['guests'],
                'bedrooms' => $favorite['bedrooms'],
                'thumbnail' => listing_thumbnail_src($favorite),
                'is_booked' => dominium_is_listing_booked($favorite['id']),
                'url' => url('listings/' . $favorite['id']),
                'favorited_at' => $favorite['favorited_at'],
                'favorites_count' => dominium_listing_favorites_count($favorite['id'])
            ];
        }
        
        echo json_encode([
            'results' => $results,
            'total_count' => $totalCount,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public static function checkFavorite(): void
    {
        header('Content-Type: application/json');
        
        $user = auth_user();
        if (!$user) {
            echo json_encode(['is_favorite' => false]);
            return;
        }
        
        $listingId = (int) ($_GET['listing_id'] ?? 0);
        if ($listingId === 0) {
            echo json_encode(['is_favorite' => false]);
            return;
        }
        
        $isFavorite = dominium_is_favorite($user['id'], $listingId);
        echo json_encode(['is_favorite' => $isFavorite]);
    }

    public static function priceAlerts(): void
    {
        header('Content-Type: application/json');
        
        $user = auth_user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            return;
        }
        
        $limit = (int) ($_GET['limit'] ?? 20);
        $alerts = dominium_user_price_alerts($user['id'], $limit);
        
        echo json_encode(['alerts' => $alerts]);
    }

    public static function updatePreferences(): void
    {
        header('Content-Type: application/json');
        
        $user = auth_user();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            return;
        }
        
        $preferences = [];
        
        // Parse JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if ($data === null) {
            // Fallback to POST data
            $data = $_POST;
        }
        
        // Validate and sanitize preferences
        $allowedFields = [
            'price_alerts_enabled', 'email_notifications', 'push_notifications',
            'min_price_alert', 'max_price_alert', 'preferred_categories', 'preferred_locations'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if (in_array($field, ['price_alerts_enabled', 'email_notifications', 'push_notifications'])) {
                    $preferences[$field] = (bool) $data[$field];
                } elseif (in_array($field, ['min_price_alert', 'max_price_alert'])) {
                    $preferences[$field] = is_numeric($data[$field]) ? (float) $data[$field] : null;
                } elseif (in_array($field, ['preferred_categories', 'preferred_locations'])) {
                    if (is_array($data[$field])) {
                        $preferences[$field] = array_filter($data[$field], 'is_string');
                    } else {
                        $preferences[$field] = null;
                    }
                }
            }
        }
        
        if (empty($preferences)) {
            http_response_code(400);
            echo json_encode(['error' => 'No valid preferences provided']);
            return;
        }
        
        $success = dominium_update_user_preferences($user['id'], $preferences);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Preferences updated successfully',
                'preferences' => dominium_get_user_preferences($user['id'])
            ]);
        } else {
            echo json_encode(['error' => 'Failed to update preferences']);
        }
    }
}
