<?php
$page_title = 'Browse Listings';
$query = trim($_GET['q'] ?? '');
$listings = [];
$provinces = [];
$categories = [];
$listings_data_error = null;

try {
    $listings = $query ? dominium_search_listings($query) : dominium_listings();
    $provinces = db()->table('listings')->select('DISTINCT province')->where('province', '!=', '')->order_by('province', 'ASC')->get_all() ?: [];
    $categories = db()->table('listings')->select('DISTINCT category')->where('category', '!=', '')->order_by('category', 'ASC')->get_all() ?: [];
} catch (Throwable $e) {
    error_log('Browse listings failed: ' . $e->getMessage());
    $listings_data_error = 'Listings cannot be loaded right now. Please check the database connection or import dominium.sql.';
}

include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-primary mb-4">Browse All Listings</h1>
            <?php if (!empty($listings_data_error)): ?>
                <div class="mb-6 p-4 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-900 text-sm">
                    <?= esc($listings_data_error) ?>
                </div>
            <?php endif; ?>
            <div class="flex flex-col lg:flex-row gap-4 mb-6">
                <form action="<?= url('listings') ?>" method="GET" class="flex-1 flex gap-3">
                    <input type="search" id="searchInput" name="q" value="<?= esc($query) ?>" placeholder="Search by province, city, or title..." class="flex-1 px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                    <button type="submit" class="btn btn-primary px-6 py-3 rounded font-semibold">
                        Search
                    </button>
                </form>
                <select id="sortSelect" class="px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="relevance">Sort by Relevance</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="newest">Newest First</option>
                    <option value="guests_high">Most Guests</option>
                    <option value="bedrooms_high">Most Bedrooms</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filter Sidebar -->
            <div class="lg:w-80 flex-shrink-0">
                <div class="card p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-primary mb-4">Filters</h2>
                    
                    <!-- Price Range -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-primary mb-3">Price Range</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-secondary">Min Price (₱)</label>
                                <input type="number" id="minPrice" min="0" step="100" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                            </div>
                            <div>
                                <label class="text-sm text-secondary">Max Price (₱)</label>
                                <input type="number" id="maxPrice" min="0" step="100" placeholder="No limit" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                            </div>
                            <!-- Quick Price Filters -->
                            <div class="pt-2">
                                <p class="text-xs text-muted mb-2">Quick filters:</p>
                                <div class="flex flex-wrap gap-1">
                                    <button type="button" class="price-quick-filter px-2 py-1 text-xs btn btn-outline" data-min="0" data-max="1000">Under ₱1k</button>
                                    <button type="button" class="price-quick-filter px-2 py-1 text-xs btn btn-outline" data-min="1000" data-max="3000">₱1k-3k</button>
                                    <button type="button" class="price-quick-filter px-2 py-1 text-xs btn btn-outline" data-min="3000" data-max="5000">₱3k-5k</button>
                                    <button type="button" class="price-quick-filter px-2 py-1 text-xs btn btn-outline" data-min="5000" data-max="">₱5k+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-primary mb-3">Location</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm text-secondary">Province</label>
                                <select id="provinceFilter" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                                    <option value="">All Provinces</option>
                                    <?php foreach ($provinces as $p): ?>
                                        <option value="<?= esc($p['province']) ?>"><?= esc($p['province']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-secondary">City</label>
                                <select id="cityFilter" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" disabled>
                                    <option value="">Select province first</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Property Type -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-primary mb-3">Property Type</h3>
                        <select id="categoryFilter" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">All Types</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= esc($c['category']) ?>"><?= esc($c['category']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Guests -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-primary mb-3">Minimum Guests</h3>
                        <input type="number" id="minGuests" min="1" max="20" placeholder="Any" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                    </div>

                    <!-- Bedrooms -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-primary mb-3">Minimum Bedrooms</h3>
                        <input type="number" id="minBedrooms" min="1" max="10" placeholder="Any" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                    </div>

                    <!-- Clear Filters -->
                    <button type="button" id="clearFilters" class="w-full btn btn-outline">
                        Clear All Filters
                    </button>
                </div>
            </div>

            <!-- Listings Container -->
            <div class="flex-1">
                <!-- Search Results Header -->
                <div id="searchHeader" class="mb-6 hidden">
                    <div class="flex justify-between items-center">
                        <p id="resultsCount" class="text-secondary"></p>
                        <div id="loadingIndicator" class="hidden">
                            <div class="flex items-center gap-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                                <span class="text-sm text-secondary">Searching...</span>
                            </div>
                        </div>
                    </div>
                </div>

        <div id="listingsContainer">
        <?php if (empty($listings)): ?>
            <div class="text-center py-12">
                <p class="text-secondary text-lg">No listings found. Try a different search.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($listings as $listing): ?>
                    <a href="<?= url('listings/' . $listing['id']) ?>" class="listing-card group block border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                        <?php if (is_authenticated()): ?>
                            <div class="relative p-3 bg-white bg-opacity-95 border-b border-gray-100">
                                <button class="favorite-btn absolute top-3 right-3 bg-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition shadow-md" data-listing-id="<?= esc($listing['id']) ?>" onclick="event.stopPropagation()">
                                    <svg class="favorite-icon w-5 h-5 text-gray-400 hover:text-red-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        <?php endif; ?>
                        <div class="relative overflow-hidden h-64">
                            <img src="<?= esc(listing_thumbnail_src($listing)) ?>" alt="<?= esc($listing['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-secondary mb-2"><?= esc($listing['province'] ? $listing['province'] . ', ' : '') ?><?= esc($listing['city']) ?> · <?= esc($listing['category']) ?></p>
                            <h3 class="text-lg font-bold text-primary mb-1"><?= esc($listing['title']) ?></h3>
                            <p class="text-sm text-secondary mb-3"><?= esc($listing['guests']) ?> guests · <?= esc($listing['bedrooms']) ?> bed<?= $listing['bedrooms'] !== 1 ? 's' : '' ?></p>
                            <div class="flex justify-between items-center">
                                <p class="price text-2xl">₱<?= esc($listing['price']) ?></p>
                                <div class="flex items-center gap-2">
                                    <span class="badge <?= dominium_is_listing_booked($listing['id']) ? 'badge-error' : 'badge-success' ?>">
                                        <?= dominium_is_listing_booked($listing['id']) ? 'Booked' : 'Available' ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
            </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const listingsContainer = document.getElementById('listingsContainer');
    const sortSelect = document.getElementById('sortSelect');
    const minPriceInput = document.getElementById('minPrice');
    const maxPriceInput = document.getElementById('maxPrice');
    const provinceSelect = document.getElementById('provinceFilter');
    const citySelect = document.getElementById('cityFilter');
    const categorySelect = document.getElementById('categoryFilter');
    const minGuestsSelect = document.getElementById('minGuests');
    const minBedroomsSelect = document.getElementById('minBedrooms');
    const clearFiltersBtn = document.getElementById('clearFilters');
    
    let searchTimeout;
    let currentQuery = '<?= esc($query) ?>';

    // Initialize filters from URL parameters
    function initializeFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        searchInput.value = urlParams.get('q') || '';
        sortSelect.value = urlParams.get('sort') || 'relevance';
        minPriceInput.value = urlParams.get('min_price') || '';
        maxPriceInput.value = urlParams.get('max_price') || '';
        provinceSelect.value = urlParams.get('province') || '';
        citySelect.value = urlParams.get('city') || '';
        categorySelect.value = urlParams.get('category') || '';
        minGuestsSelect.value = urlParams.get('min_guests') || '';
        minBedroomsSelect.value = urlParams.get('min_bedrooms') || '';

        // Load cities based on selected province
        if (provinceSelect.value) {
            loadCities(provinceSelect.value);
        }
    }

    // Load cities based on province selection
    function loadCities(province) {
        if (!province) {
            citySelect.innerHTML = '<option value="">Select province first</option>';
            citySelect.disabled = true;
            return;
        }

        // Load cities from database based on province
        fetch('<?= url('api/search') ?>?province=' + encodeURIComponent(province))
            .then(response => response.json())
            .then(data => {
                citySelect.innerHTML = '<option value="">All Cities</option>';
                citySelect.disabled = false;

                if (data.results && Array.isArray(data.results)) {
                    // Extract unique cities from results
                    const cities = [...new Set(data.results.map(r => r.city).filter(c => c))];
                    cities.sort();

                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="">Error loading cities</option>';
            });
    }

    function createListingCard(listing) {
        const bookedClass = listing.is_booked ? 'text-red-700 bg-red-50' : 'text-green-700 bg-green-50';
        const bookedText = listing.is_booked ? 'Booked' : 'Available';

        const card = document.createElement('a');
        card.href = String(listing.url || '#');
        card.className = 'group block border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition';

        if (listing.isAuthenticated) {
            const favoriteWrapper = document.createElement('div');
            favoriteWrapper.className = 'relative p-3 bg-white bg-opacity-95 border-b border-gray-100';

            const favoriteButton = document.createElement('button');
            favoriteButton.type = 'button';
            favoriteButton.className = 'favorite-btn absolute top-3 right-3 bg-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition shadow-md';
            favoriteButton.dataset.listingId = String(listing.id || '');

            const favoriteSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            favoriteSvg.setAttribute('class', 'favorite-icon w-5 h-5 transition ' + (listing.is_favorite ? 'text-red-500' : 'text-gray-400 hover:text-red-500'));
            favoriteSvg.setAttribute('fill', listing.is_favorite ? 'currentColor' : 'none');
            favoriteSvg.setAttribute('stroke', 'currentColor');
            favoriteSvg.setAttribute('viewBox', '0 0 24 24');

            const favoritePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            favoritePath.setAttribute('stroke-linecap', 'round');
            favoritePath.setAttribute('stroke-linejoin', 'round');
            favoritePath.setAttribute('stroke-width', '2');
            favoritePath.setAttribute('d', 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z');
            favoriteSvg.appendChild(favoritePath);
            favoriteButton.appendChild(favoriteSvg);
            favoriteWrapper.appendChild(favoriteButton);
            card.appendChild(favoriteWrapper);
        }

        const imageWrapper = document.createElement('div');
        imageWrapper.className = 'relative overflow-hidden h-64';
        const image = document.createElement('img');
        image.src = String(listing.thumbnail || '');
        image.alt = String(listing.title || 'Listing image');
        image.className = 'w-full h-full object-cover group-hover:scale-105 transition duration-300';
        imageWrapper.appendChild(image);
        card.appendChild(imageWrapper);

        const body = document.createElement('div');
        body.className = 'p-4';

        const location = document.createElement('p');
        location.className = 'text-sm text-gray-600 mb-2';
        location.textContent = `${listing.province ? listing.province + ', ' : ''}${listing.city || ''} · ${listing.category || ''}`;
        body.appendChild(location);

        const title = document.createElement('h3');
        title.className = 'text-lg font-bold text-black mb-1';
        title.textContent = String(listing.title || 'Untitled listing');
        body.appendChild(title);

        const details = document.createElement('p');
        details.className = 'text-sm text-gray-600 mb-3';
        const bedrooms = Number(listing.bedrooms || 0);
        details.textContent = `${Number(listing.guests || 0)} guests · ${bedrooms} bed${bedrooms !== 1 ? 's' : ''}`;
        body.appendChild(details);

        const footer = document.createElement('div');
        footer.className = 'flex justify-between items-center';

        const price = document.createElement('p');
        price.className = 'text-2xl font-bold text-black';
        price.textContent = '₱' + Number(listing.price || 0).toLocaleString(undefined, { maximumFractionDigits: 2 });
        footer.appendChild(price);

        const badgeWrapper = document.createElement('div');
        badgeWrapper.className = 'flex items-center gap-2';
        const badge = document.createElement('span');
        badge.className = 'px-2 py-1 rounded text-xs ' + bookedClass;
        badge.textContent = bookedText;
        badgeWrapper.appendChild(badge);
        footer.appendChild(badgeWrapper);

        body.appendChild(footer);
        card.appendChild(body);

        return card;
    }

    function updateListings(results) {
        const safeResults = Array.isArray(results) ? results : [];

        // Update results count
        const searchHeader = document.getElementById('searchHeader');
        const resultsCount = document.getElementById('resultsCount');
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        searchHeader.classList.remove('hidden');
        loadingIndicator.classList.add('hidden');
        
        listingsContainer.replaceChildren();

        if (safeResults.length === 0) {
            resultsCount.textContent = 'No listings found';
            const emptyState = document.createElement('div');
            emptyState.className = 'text-center py-12';
            const message = document.createElement('p');
            message.className = 'text-gray-600 text-lg';
            message.textContent = 'No listings found. Try adjusting your filters.';
            emptyState.appendChild(message);
            listingsContainer.appendChild(emptyState);
        } else {
            resultsCount.textContent = `${safeResults.length} listing${safeResults.length !== 1 ? 's' : ''} found`;
            const grid = document.createElement('div');
            grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8';
            
            safeResults.forEach(listing => {
                grid.appendChild(createListingCard(listing));
            });
            
            listingsContainer.appendChild(grid);
            checkAllFavoriteStatuses();
        }
    }

    function buildApiUrl() {
        const params = new URLSearchParams();

        const query = searchInput.value.trim();
        if (query) params.set('q', query);

        const minPrice = minPriceInput.value.trim();
        if (minPrice) params.set('min_price', minPrice);

        const maxPrice = maxPriceInput.value.trim();
        if (maxPrice) params.set('max_price', maxPrice);

        const province = provinceSelect.value.trim();
        if (province) params.set('province', province);

        const city = citySelect.value.trim();
        if (city) params.set('city', city);

        const category = categorySelect.value.trim();
        if (category) params.set('category', category);

        const minGuests = minGuestsSelect.value.trim();
        if (minGuests) params.set('min_guests', minGuests);

        const minBedrooms = minBedroomsSelect.value.trim();
        if (minBedrooms) params.set('min_bedrooms', minBedrooms);

        const sort = sortSelect.value.trim();
        if (sort && sort !== 'relevance') params.set('sort', sort);

        return '<?= url('api/search') ?>?' + params.toString();
    }

    function updateUrl() {
        const params = new URLSearchParams();

        const query = searchInput.value.trim();
        if (query) params.set('q', query);

        const minPrice = minPriceInput.value.trim();
        if (minPrice) params.set('min_price', minPrice);

        const maxPrice = maxPriceInput.value.trim();
        if (maxPrice) params.set('max_price', maxPrice);

        const province = provinceSelect.value.trim();
        if (province) params.set('province', province);

        const city = citySelect.value.trim();
        if (city) params.set('city', city);

        const category = categorySelect.value.trim();
        if (category) params.set('category', category);

        const minGuests = minGuestsSelect.value.trim();
        if (minGuests) params.set('min_guests', minGuests);

        const minBedrooms = minBedroomsSelect.value.trim();
        if (minBedrooms) params.set('min_bedrooms', minBedrooms);

        const sort = sortSelect.value.trim();
        if (sort && sort !== 'relevance') params.set('sort', sort);

        const url = params.toString() ? '<?= url('listings') ?>?' + params.toString() : '<?= url('listings') ?>';
        history.replaceState(null, '', url);
    }

    function performSearch() {
        const url = buildApiUrl();
        
        // Show loading indicator
        const loadingIndicator = document.getElementById('loadingIndicator');
        const resultsCount = document.getElementById('resultsCount');
        const searchHeader = document.getElementById('searchHeader');
        
        searchHeader.classList.remove('hidden');
        loadingIndicator.classList.remove('hidden');
        resultsCount.textContent = 'Searching...';
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                updateListings(data.results);
                updateUrl();
            })
            .catch(error => {
                console.error('Search error:', error);
                loadingIndicator.classList.add('hidden');
                resultsCount.textContent = 'Error loading results';
            });
    }

    function triggerSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 300);
    }

    // Event listeners
    searchInput.addEventListener('input', triggerSearch);
    sortSelect.addEventListener('change', triggerSearch);
    minPriceInput.addEventListener('input', triggerSearch);
    maxPriceInput.addEventListener('input', triggerSearch);
    provinceSelect.addEventListener('change', function() {
        // Reset city when province changes
        citySelect.innerHTML = '<option value="">All Cities</option>';
        citySelect.value = '';
        // Load cities for selected province
        if (this.value) {
            loadCities(this.value);
        }
        triggerSearch();
    });
    citySelect.addEventListener('change', triggerSearch);
    categorySelect.addEventListener('change', triggerSearch);
    minGuestsSelect.addEventListener('change', triggerSearch);
    minBedroomsSelect.addEventListener('change', triggerSearch);
    
    // Quick price filter buttons
    document.querySelectorAll('.price-quick-filter').forEach(button => {
        button.addEventListener('click', function() {
            const min = this.dataset.min;
            const max = this.dataset.max;
            
            minPriceInput.value = min;
            maxPriceInput.value = max;
            
            // Update button styles
            document.querySelectorAll('.price-quick-filter').forEach(btn => {
                btn.classList.remove('bg-black', 'text-white');
                btn.classList.add('border-gray-300');
            });
            this.classList.remove('border-gray-300');
            this.classList.add('bg-black', 'text-white');
            
            triggerSearch();
        });
    });

    // Clear filters
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        sortSelect.value = 'relevance';
        minPriceInput.value = '';
        maxPriceInput.value = '';
        provinceSelect.value = '';
        citySelect.innerHTML = '<option value="">All Cities</option>';
        citySelect.value = '';
        categorySelect.value = '';
        minGuestsSelect.value = '';
        minBedroomsSelect.value = '';

        // Reset quick price filter buttons
        document.querySelectorAll('.price-quick-filter').forEach(btn => {
            btn.classList.remove('bg-black', 'text-white');
            btn.classList.add('border-gray-300');
        });

        performSearch();
    });

    // Initialize filters and perform initial search
    initializeFilters();
    performSearch();
    
    // Favorites functionality for listing cards
    function initializeFavorites() {
        // Add click handlers to favorite buttons
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
            
            // Define URLs for JavaScript
const API_CSRF_TOKEN_URL = '<?php echo url('api/csrf-token'); ?>';
const API_FAVORITES_BASE_URL = '<?php echo url('api/favorites/'); ?>';

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
            
            fetch(API_FAVORITES_BASE_URL + endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': freshToken
                },
                body: 'listing_id=' + listingId + '&csrf_token=' + encodeURIComponent(freshToken)
            })
            .then(response => {
                if (response.status === 401) {
                    throw new Error('Please login to favorite listings');
                }
                if (response.status === 403) {
                    throw new Error('CSRF token error. Please refresh the page.');
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    updateFavoriteButtonState(favoriteIcon, !isCurrentlyFavorite);
                    showToast(data.message, isCurrentlyFavorite ? 'removed' : 'added');
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
        
        // Check favorite status for all visible listings
        checkAllFavoriteStatuses();
    }
    
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
    
    function checkAllFavoriteStatuses() {
        const favoriteBtns = document.querySelectorAll('.favorite-btn');
        const listingIds = Array.from(favoriteBtns).map(btn => btn.dataset.listingId).filter(id => id);
        
        if (listingIds.length === 0) return;
        
        // Check each listing individually to avoid large URLs
        listingIds.forEach(listingId => {
            fetch('<?= url('api/favorites/check') ?>?listing_id=' + listingId)
                .then(response => response.json())
                .then(data => {
                    const btn = document.querySelector(`.favorite-btn[data-listing-id="${listingId}"]`);
                    const icon = btn?.querySelector('.favorite-icon');
                    if (icon) {
                        updateFavoriteButtonState(icon, data.is_favorite);
                    }
                })
                .catch(error => console.error('Error checking favorite status:', error));
        });
    }
    
    // Show toast notification
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
    
    // Initialize favorites after listings are loaded
    setTimeout(initializeFavorites, 100);
});
</script>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
