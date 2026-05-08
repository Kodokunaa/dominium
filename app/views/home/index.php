<?php
$page_title = 'Home';
$homepage_data_error = null;

try {
    $featured = dominium_listings(6);
    $featured = array_slice($featured, 0, 6);
} catch (Throwable $e) {
    error_log('Homepage listings failed: ' . $e->getMessage());
    $featured = [];
    $homepage_data_error = 'Listings are temporarily unavailable. Please check the database connection or import dominium.sql.';
}

include APP_ROOT . '/app/views/partials/header.php';
?>

<main>
    <?php if (!empty($homepage_data_error)): ?>
        <div class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8">
            <div class="p-4 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-900 text-sm">
                <?= esc($homepage_data_error) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Hero Section with Background -->
    <section class="relative bg-gradient-to-br from-gray-900 via-black to-gray-900 text-white py-20 lg:py-32 overflow-visible" style="z-index: 30;">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 right-20 w-48 h-48 bg-blue-500 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-1/3 w-40 h-40 bg-purple-500 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="z-index: 40;">
            <div class="text-center">
                <div class="mb-6">
                    <span class="inline-block px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-full mb-4">
                        Your Gateway to Philippine Properties
                    </span>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold mb-6 text-white">
                    Discover Your Perfect<br />Philippine Getaway
                </h1>

                <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed">
                    From stunning beachfront villas to cozy mountain retreats, find your dream rental among our curated collection of premium Philippine properties.
                </p>

                <!-- Enhanced Search -->
                <div class="hero-search-area max-w-3xl mx-auto mb-8 relative" style="z-index: 9999;">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-2 border border-white/20">
                        <div class="flex flex-col md:flex-row gap-2">
                            <div class="relative flex-1">
                                <input
                                    type="search"
                                    id="heroSearchInput"
                                    placeholder="Search by location, property type, or keywords..."
                                    class="w-full px-6 py-4 pr-12 bg-white/90 text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500"
                                    autocomplete="off"
                                >

                                <button
                                    type="button"
                                    id="heroSearchClear"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-900 text-2xl font-bold hidden"
                                    aria-label="Clear search"
                                >
                                    &times;
                                </button>
                            </div>

                            <button
                                type="button"
                                onclick="performHeroSearch()"
                                class="px-8 py-4 bg-black text-white font-semibold rounded-xl hover:bg-gray-800 transition-all transform hover:scale-105 shadow-lg"
                            >
                                Search Properties
                            </button>
                        </div>
                    </div>

                    <div
                        id="heroSearchResults"
                        class="hero-search-results absolute left-0 right-0 top-full mt-3 w-full bg-white rounded-xl shadow-2xl hidden border border-gray-200 overflow-hidden"
                        style="z-index: 99999;"
                    >
                        <!-- Search results will appear here -->
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="flex flex-wrap justify-center gap-8 mb-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-400">500+</div>
                        <div class="text-gray-400">Properties</div>
                    </div>

                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-400">50+</div>
                        <div class="text-gray-400">Cities</div>
                    </div>

                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-400">1000+</div>
                        <div class="text-gray-400">Happy Guests</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Listings -->
    <section class="relative py-20 bg-gradient-to-b from-gray-50 to-white" style="z-index: 10;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-black text-sm font-semibold rounded-full mb-4">
                    ⭐ Featured Properties
                </span>

                <h2 class="text-4xl md:text-5xl font-bold text-black mb-4">
                    Handpicked Premium Properties
                </h2>

                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Discover our most exclusive rentals, carefully selected for quality and comfort
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featured as $listing): ?>
                    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:-translate-y-2">
                        <!-- Image Container -->
                        <div class="relative h-56 overflow-hidden">
                            <img
                                src="<?= esc(listing_thumbnail_src($listing)) ?>"
                                alt="<?= esc($listing['title']) ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                            >

                            <!-- Overlay with Category Badge -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>

                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-sm text-xs font-semibold text-gray-800 rounded-full">
                                    <?= esc($listing['category']) ?>
                                </span>
                            </div>

                            <div class="absolute top-4 right-4 z-20 pointer-events-auto">
                                <button
                                    class="favorite-btn w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition"
                                    data-listing-id="<?= $listing['id'] ?>"
                                    type="button"
                                >
                                    <svg class="favorite-icon w-5 h-5 text-gray-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    <span class="truncate">
                                        <?= esc($listing['city']) ?>, <?= esc($listing['province']) ?>
                                    </span>
                                </div>
                            </div>

                            <h3 class="text-xl font-bold text-black mb-3 line-clamp-2 group-hover:text-blue-600 transition">
                                <?= esc($listing['title']) ?>
                            </h3>

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        <?= esc($listing['bedrooms']) ?> beds
                                    </span>

                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <?= esc($listing['guests']) ?> guests
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <span class="text-2xl font-bold text-black">₱<?= number_format((float) $listing['price']) ?></span>
                                    <span class="text-sm text-gray-500">/night</span>
                                </div>

                                <a
                                    href="<?= url('listings/' . $listing['id']) ?>"
                                    class="listing-view-details-btn"
                                >
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-16">
                <a
                    href="<?= url('listings') ?>"
                    class="inline-flex items-center px-8 py-4 bg-black text-white font-semibold rounded-xl hover:bg-gray-800 transition-all transform hover:scale-105 shadow-lg"
                >
                    <span>Explore All Properties</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Dominium -->
    <section class="bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-blue-600/20 text-blue-300 text-sm font-semibold rounded-full mb-4">
                    Why Choose Us
                </span>

                <h2 class="text-4xl md:text-5xl font-bold mb-4 bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                    Experience the Dominium Difference
                </h2>

                <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                    We're committed to making your rental experience exceptional
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold mb-3 group-hover:text-blue-300 transition">
                        Verified Hosts
                    </h3>

                    <p class="text-gray-400 leading-relaxed">
                        All listers undergo thorough verification and background checks for your safety and peace of mind
                    </p>
                </div>

                <div class="text-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold mb-3 group-hover:text-green-300 transition">
                        Secure Bookings
                    </h3>

                    <p class="text-gray-400 leading-relaxed">
                        Your personal and payment information is protected with industry-standard encryption
                    </p>
                </div>

                <div class="text-center group">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold mb-3 group-hover:text-purple-300 transition">
                        Premium Selection
                    </h3>

                    <p class="text-gray-400 leading-relaxed">
                        Handpicked properties that meet our high standards for quality and comfort
                    </p>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="mt-16 text-center">
                <div class="inline-flex flex-wrap justify-center items-center gap-8 text-sm text-gray-400">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        24/7 Support
                    </span>

                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2h-1z" clip-rule="evenodd"></path>
                        </svg>
                        Easy Payments
                    </span>

                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Best Price Guarantee
                    </span>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Define URLs for JavaScript
const API_CSRF_TOKEN_URL = '<?= url('api/csrf-token') ?>';
const API_FAVORITES_URL = '<?= url('api/favorites/') ?>';

document.addEventListener('DOMContentLoaded', function () {
    const heroSearchInput = document.getElementById('heroSearchInput');
    const heroSearchClear = document.getElementById('heroSearchClear');
    const heroSearchResults = document.getElementById('heroSearchResults');
    let searchTimeout;

    function createSearchResult(listing) {
        const link = document.createElement('a');

        const listingId = listing.id || '';
        link.href = String(listing.url || ('<?= url('listings') ?>/' + listingId));
        link.className = 'hero-search-result-link block p-4 hover:bg-gray-50 transition border-b border-gray-100 last:border-b-0';

        const row = document.createElement('div');
        row.className = 'flex gap-4 items-center';

        const image = document.createElement('img');
        image.src = String(listing.thumbnail || '');
        image.alt = String(listing.title || 'Listing image');
        image.className = 'w-16 h-16 object-cover rounded-lg shrink-0 bg-gray-100';
        row.appendChild(image);

        const content = document.createElement('div');
        content.className = 'flex-1 min-w-0 text-left';

        const title = document.createElement('h4');
        title.className = 'font-semibold text-black mb-1 truncate';
        title.textContent = String(listing.title || 'Untitled listing');
        content.appendChild(title);

        const location = document.createElement('p');
        location.className = 'text-sm text-gray-600 truncate';
        location.textContent = `${listing.province ? listing.province + ', ' : ''}${listing.city || ''}`;
        content.appendChild(location);

        const price = document.createElement('p');
        price.className = 'text-sm font-bold text-black';
        price.textContent = '₱' + Number(listing.price || 0).toLocaleString(undefined, { maximumFractionDigits: 2 }) + '/night';
        content.appendChild(price);

        row.appendChild(content);
        link.appendChild(row);

        return link;
    }

    function updateHeroResults(results) {
        if (!heroSearchResults) {
            return;
        }

        const safeResults = Array.isArray(results) ? results : [];
        heroSearchResults.replaceChildren();

        if (safeResults.length === 0) {
            const emptyState = document.createElement('div');
            emptyState.className = 'p-4 text-gray-600 text-center';
            emptyState.textContent = 'No listings found. Try a different search.';
            heroSearchResults.appendChild(emptyState);
        } else {
            safeResults.slice(0, 6).forEach(listing => {
                heroSearchResults.appendChild(createSearchResult(listing));
            });
        }
    }

    function showHeroResults() {
        if (heroSearchResults) {
            heroSearchResults.classList.remove('hidden');
        }
    }

    function hideHeroResults() {
        if (heroSearchResults) {
            heroSearchResults.classList.add('hidden');
        }
    }

    function toggleClearButton() {
        if (!heroSearchClear || !heroSearchInput) {
            return;
        }

        if (heroSearchInput.value.trim() !== '') {
            heroSearchClear.classList.remove('hidden');
        } else {
            heroSearchClear.classList.add('hidden');
        }
    }

    function performHeroSearchLocal() {
        if (!heroSearchInput) {
            window.location.href = '<?= url('listings') ?>';
            return;
        }

        const query = heroSearchInput.value.trim();

        if (query) {
            window.location.href = '<?= url('listings') ?>?q=' + encodeURIComponent(query);
        } else {
            window.location.href = '<?= url('listings') ?>';
        }
    }

    if (!heroSearchInput || !heroSearchResults) {
        console.warn('Search elements not found, skipping search initialization');
    } else {
        heroSearchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            toggleClearButton();

            if (query === '') {
                hideHeroResults();
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch('<?= url('api/search') ?>?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        updateHeroResults(data.results || data || []);
                        showHeroResults();
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        updateHeroResults([]);
                        showHeroResults();
                    });
            }, 300);
        });

        heroSearchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performHeroSearchLocal();
            }
        });

        heroSearchInput.addEventListener('focus', function () {
            if (this.value.trim() !== '') {
                showHeroResults();
            }
        });

        if (heroSearchClear) {
            heroSearchClear.addEventListener('click', function () {
                heroSearchInput.value = '';
                toggleClearButton();
                hideHeroResults();
                heroSearchInput.focus();
            });
        }

        document.addEventListener('click', function (e) {
            const searchArea = document.querySelector('.hero-search-area');

            if (searchArea && !searchArea.contains(e.target)) {
                hideHeroResults();
            }
        });
    }

    // Favorite buttons functionality
    function initializeFavoriteButtons() {
        const favoriteButtons = document.querySelectorAll('[data-listing-id]');

        favoriteButtons.forEach(button => {
            button.addEventListener('click', async function (e) {
                e.preventDefault();
                e.stopPropagation();

                const listingId = this.dataset.listingId;
                const svg = this.querySelector('svg');

                if (!svg) {
                    return;
                }

                const isCurrentlyFavorite = svg.classList.contains('text-red-500');
                const endpoint = isCurrentlyFavorite ? 'remove' : 'add';

                this.disabled = true;
                svg.classList.add('opacity-50');

                let freshToken = '';

                try {
                    const tokenResponse = await fetch(API_CSRF_TOKEN_URL);

                    if (tokenResponse.ok) {
                        const tokenData = await tokenResponse.json();
                        freshToken = tokenData.token || '';

                        const metaTag = document.querySelector('meta[name="csrf-token"]');

                        if (metaTag && freshToken) {
                            metaTag.setAttribute('content', freshToken);
                        }
                    }
                } catch (e) {
                    console.warn('Could not fetch fresh CSRF token, using meta tag:', e);
                    freshToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                }

                try {
                    const response = await fetch(API_FAVORITES_URL + endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-Token': freshToken
                        },
                        body: 'listing_id=' + encodeURIComponent(listingId) + '&csrf_token=' + encodeURIComponent(freshToken)
                    });

                    if (response.status === 401) {
                        throw new Error('Please login to favorite listings');
                    }

                    if (response.status === 403) {
                        throw new Error('CSRF token error. Please refresh the page.');
                    }

                    const data = await response.json();

                    if (data && data.success) {
                        if (isCurrentlyFavorite) {
                            svg.classList.remove('text-red-500', 'fill-current');
                            svg.classList.add('text-gray-600');
                            showToast('Removed from favorites', 'info');
                        } else {
                            svg.classList.remove('text-gray-600');
                            svg.classList.add('text-red-500', 'fill-current');
                            showToast('Added to favorites!', 'success');
                        }
                    } else {
                        showToast(data.error || 'Failed to update favorites', 'error');
                    }
                } catch (error) {
                    console.error('Error toggling favorite:', error);
                    showToast(error.message || 'Failed to update favorites', 'error');

                    if (error.message.includes('login')) {
                        setTimeout(() => {
                            if (confirm('You need to login to favorite listings. Go to login page?')) {
                                window.location.href = '<?= url('login') ?>';
                            }
                        }, 1200);
                    }
                } finally {
                    this.disabled = false;
                    svg.classList.remove('opacity-50');
                }
            });
        });
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');

        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };

        toast.className = `fixed bottom-4 right-4 ${colors[type] || colors.info} text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full transition-transform duration-300`;
        toast.style.zIndex = '999999';
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('translate-y-full');
        }, 100);

        setTimeout(() => {
            toast.classList.add('translate-y-full');

            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    function checkAllFavoriteStatuses() {
        const favoriteBtns = document.querySelectorAll('.favorite-btn');
        const listingIds = Array.from(favoriteBtns).map(btn => btn.dataset.listingId).filter(id => id);

        if (listingIds.length === 0) {
            return;
        }

        listingIds.forEach(listingId => {
            fetch('<?= url('api/favorites/check') ?>?listing_id=' + encodeURIComponent(listingId))
                .then(response => response.json())
                .then(data => {
                    const btn = document.querySelector(`.favorite-btn[data-listing-id="${listingId}"]`);

                    if (btn && data.is_favorite) {
                        const icon = btn.querySelector('.favorite-icon');

                        if (icon) {
                            icon.classList.remove('text-gray-600');
                            icon.classList.add('text-red-500', 'fill-current');
                        }
                    }
                })
                .catch(error => console.error('Error checking favorite status:', error));
        });
    }

    function observeElements() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.group').forEach(el => {
            observer.observe(el);
        });
    }

    initializeFavoriteButtons();
    checkAllFavoriteStatuses();
    observeElements();
});

function performHeroSearch() {
    const heroSearchInput = document.getElementById('heroSearchInput');
    const query = heroSearchInput ? heroSearchInput.value.trim() : '';

    if (query) {
        window.location.href = '<?= url('listings') ?>?q=' + encodeURIComponent(query);
    } else {
        window.location.href = '<?= url('listings') ?>';
    }
}
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Search prediction clipping fix */
.hero-search-area {
    overflow: visible !important;
}

.hero-search-results {
    max-height: 420px;
    overflow-y: auto;
    color: #0f172a;
}

.hero-search-results.hidden {
    display: none !important;
}

@media (max-width: 768px) {
    .hero-search-area {
        z-index: 9999 !important;
    }

    .hero-search-results {
        position: static !important;
        margin-top: 0.75rem !important;
        max-height: 360px;
        border-radius: 1rem;
    }

    .hero-search-result-link {
        padding: 0.85rem !important;
    }
}
</style>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>