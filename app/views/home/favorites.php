<?php
$page_title = 'My Favorites';
$user = auth_user();
if (!$user) {
    header('Location: ' . url('login'));
    exit;
}

$favorites = [];
$totalCount = 0;
$preferences = ['price_alerts_enabled' => 0];
$priceAlerts = [];
$favorites_data_error = null;
try {
    $favorites = dominium_user_favorites((int)$user['id'], 20, 0);
    $totalCount = dominium_favorites_count((int)$user['id']);
    $preferences = dominium_get_user_preferences((int)$user['id']);
    $priceAlerts = dominium_user_price_alerts((int)$user['id'], 10);
} catch (Throwable $e) {
    error_log('Favorites page failed: ' . $e->getMessage());
    $favorites_data_error = 'Favorites cannot be loaded right now. Please check the database connection.';
}

include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-black mb-2">My Favorites</h1>
            <p class="text-gray-600">Properties you've saved for later</p>
            <?php if (!empty($favorites_data_error)): ?>
                <div class="mt-4 p-4 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-900 text-sm">
                    <?= esc($favorites_data_error) ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (empty($favorites)): ?>
            <div class="text-center py-20">
                <div class="mb-6">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-black mb-2">No favorites yet</h2>
                <p class="text-gray-600 mb-6">Start exploring and save properties you love!</p>
                <a href="<?php echo url('listings'); ?>" class="inline-block px-6 py-3 bg-black text-white rounded hover:bg-gray-800 transition">
                    Browse Listings
                </a>
            </div>
        <?php else: ?>
            <!-- Stats Bar -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-black"><?php echo $totalCount; ?></p>
                        <p class="text-sm text-gray-600">Saved Properties</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-black"><?php echo is_array($priceAlerts) ? count($priceAlerts) : 0; ?></p>
                        <p class="text-sm text-gray-600">Price Alerts</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-black"><?php echo $preferences['price_alerts_enabled'] ? 'On' : 'Off'; ?></p>
                        <p class="text-sm text-gray-600">Price Alerts</p>
                    </div>
                </div>
            </div>

            <!-- Price Alerts Section -->
            <?php if (!empty($priceAlerts)): ?>
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-black mb-4">Recent Price Alerts</h2>
                    <div class="space-y-3">
                        <?php foreach ($priceAlerts as $alert): ?>
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="font-semibold text-black"><?php echo esc($alert['title']); ?></h3>
                                        <p class="text-sm text-gray-600"><?php echo esc($alert['city']); ?>, <?php echo esc($alert['province']); ?></p>
                                        <p class="text-sm mt-1">
                                            Price changed from 
                                            <span class="font-bold">₱<?php echo number_format($alert['original_price'], 2); ?></span> to 
                                            <span class="font-bold <?php echo $alert['price_change_type'] === 'decrease' ? 'text-green-600' : 'text-red-600'; ?>">
                                                ₱<?php echo number_format($alert['new_price'], 2); ?>
                                            </span>
                                            <span class="text-gray-500">(<?php echo $alert['price_change_type'] === 'decrease' ? '↓' : '↑'; ?> <?php echo number_format($alert['price_change_percentage'], 1); ?>%)</span>
                                        </p>
                                    </div>
                                    <a href="<?php echo url('listings/' . $alert['listing_id']); ?>" class="px-4 py-2 bg-black text-white text-sm rounded hover:bg-gray-800 transition">
                                        View Property
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Favorites Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="favoritesContainer">
                <?php foreach ($favorites as $favorite): ?>
                    <div class="group border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                        <div class="relative overflow-hidden h-64">
                            <img src="<?php echo esc(listing_thumbnail_src($favorite)); ?>" alt="<?php echo esc($favorite['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <button class="favorite-btn absolute top-3 right-3 bg-white bg-opacity-90 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-100 transition" data-listing-id="<?php echo $favorite['id']; ?>">
                                <svg class="favorite-icon w-5 h-5 text-red-500 transition" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                            <div class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-xs">
                                <?php echo date('M j, Y', strtotime($favorite['favorited_at'])); ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-600 mb-2"><?php echo esc($favorite['province'] ? $favorite['province'] . ', ' : ''); ?><?php echo esc($favorite['city']); ?> · <?php echo esc($favorite['category']); ?></p>
                            <h3 class="text-lg font-bold text-black mb-1"><?php echo esc($favorite['title']); ?></h3>
                            <p class="text-sm text-gray-600 mb-3"><?php echo esc($favorite['guests']); ?> guests · <?php echo esc($favorite['bedrooms']); ?> bed<?php echo $favorite['bedrooms'] !== 1 ? 's' : ''; ?></p>
                            <div class="flex justify-between items-center">
                                <p class="text-2xl font-bold text-black">₱<?php echo esc($favorite['price']); ?></p>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 rounded text-xs <?php echo dominium_is_listing_booked($favorite['id']) ? 'text-red-700 bg-red-50' : 'text-green-700 bg-green-50'; ?>">
                                        <?php echo dominium_is_listing_booked($favorite['id']) ? 'Booked' : 'Available'; ?>
                                    </span>
                                    <a href="<?php echo url('listings/' . $favorite['id']); ?>" class="px-4 py-2 bg-black text-white text-sm rounded hover:bg-gray-800 transition">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Define URLs for JavaScript
const API_CSRF_TOKEN_URL = '<?php echo url('api/csrf-token'); ?>';
const API_FAVORITES_URL = '<?php echo url('api/favorites/'); ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Favorites functionality
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-600' : 'bg-red-600'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.add('translate-y-0', 'opacity-100');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    // Handle favorite button clicks
    document.addEventListener('click', async function(e) {
        const favoriteBtn = e.target.closest('.favorite-btn');
        if (!favoriteBtn) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        const listingId = favoriteBtn.dataset.listingId;
        if (!listingId) return;
        
        const favoriteIcon = favoriteBtn.querySelector('.favorite-icon');
        const isCurrentlyFavorite = favoriteIcon.classList.contains('text-red-500');
        const endpoint = isCurrentlyFavorite ? 'remove' : 'add';
        
        // Show loading state
        favoriteBtn.disabled = true;
        favoriteIcon.classList.add('opacity-50');
        
        // Fetch fresh CSRF token before each request (token regenerates after POST)
        let freshToken = '';
        try {
            const tokenResponse = await fetch(API_CSRF_TOKEN_URL);
            if (tokenResponse.ok) {
                const tokenData = await tokenResponse.json();
                freshToken = tokenData.token || '';
                // Update meta tag for future requests
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag && freshToken) {
                    metaTag.setAttribute('content', freshToken);
                }
            }
        } catch (e) {
            console.warn('Could not fetch fresh CSRF token, using meta tag:', e);
            freshToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }
        
        fetch(API_FAVORITES_URL + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': freshToken
            },
            body: 'listing_id=' + listingId + '&csrf_token=' + encodeURIComponent(freshToken)
        })
        .then(response => {
            if (response.status === 403) {
                throw new Error('CSRF token error. Please refresh the page.');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                if (endpoint === 'remove') {
                    // Remove the card from the grid
                    const card = favoriteBtn.closest('.group');
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            
                            // Check if no more favorites
                            const remainingCards = document.querySelectorAll('#favoritesContainer .group');
                            if (remainingCards.length === 0) {
                                location.reload(); // Reload to show empty state
                            }
                        }, 300);
                    }
                    showToast('Removed from favorites', 'removed');
                } else {
                    updateFavoriteButtonState(favoriteIcon, true);
                    showToast('Added to favorites', 'added');
                }
            } else {
                showToast(data.error || 'Failed to update favorites', 'error');
            }
        })
        .catch(error => {
            console.error('Error toggling favorite:', error);
            showToast('Failed to update favorites', 'error');
        })
        .finally(() => {
            // Remove loading state
            favoriteBtn.disabled = false;
            favoriteIcon.classList.remove('opacity-50');
        });
    });
    
    function updateFavoriteButtonState(icon, isFavorite) {
        if (isFavorite) {
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-red-500');
            icon.setAttribute('fill', 'currentColor');
        } else {
            icon.classList.remove('text-red-500');
            icon.classList.add('text-gray-400');
            icon.setAttribute('fill', 'none');
        }
    }
});
</script>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
