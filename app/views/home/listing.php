<?php
$listing = dominium_listing_by_id($id);
$page_title = $listing ? esc($listing['title']) : 'Listing Not Found';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <?php if (!$listing): ?>
            <div class="text-center py-20">
                <h1 class="text-3xl font-bold text-black mb-4">Listing Not Found</h1>
                <p class="text-gray-600 mb-8">The property you're looking for doesn't exist.</p>
                <a href="<?= url('listings') ?>" class="inline-block px-6 py-3 bg-black text-white rounded hover:bg-gray-800 transition">
                    Back to Listings
                </a>
            </div>
        <?php else: ?>
            <?php $gallery_images = dominium_listing_gallery_paths((int) $listing['id']); ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="mb-8">
                        <!-- Image Carousel -->
                        <div class="relative" id="imageCarousel">
                            <!-- Main Image Container -->
                            <div class="relative overflow-hidden rounded-lg bg-gray-100" style="height: 500px;">
                                <div id="mainImageContainer" class="relative w-full h-full cursor-zoom-in">
                                    <img id="mainImage" src="<?= esc(listing_thumbnail_src($listing)) ?>" alt="<?= esc($listing['title']) ?>" class="w-full h-full object-contain">
                                    
                                    <!-- Navigation Arrows -->
                                    <?php if (!empty($gallery_images)): ?>
                                        <button id="prevBtn" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition opacity-0 lg:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <button id="nextBtn" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition opacity-0 lg:opacity-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Image Counter -->
                                    <div class="absolute top-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                        <span id="imageCounter">1</span> / <span id="totalImages"><?= 1 + count($gallery_images) ?></span>
                                    </div>
                                    
                                    <!-- Expand Button -->
                                    <button id="expandBtn" class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-70 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Favorite Button -->
                                    <?php if (is_authenticated()): 
                                        $currentUser = auth_user();
                                        $isFavorited = dominium_is_favorite($currentUser['id'], $listing['id']);
                                    ?>
                                        <button id="favoriteBtn" class="absolute bottom-4 left-4 bg-white bg-opacity-90 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-100 transition z-20 pointer-events-auto" data-listing-id="<?= $listing['id'] ?>">
                                            <svg id="favoriteIcon" class="favorite-icon w-5 h-5 <?= $isFavorited ? 'text-red-500' : 'text-gray-400' ?> transition" fill="<?= $isFavorited ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= url('login') ?>" class="absolute bottom-4 left-4 bg-white bg-opacity-90 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-100 transition z-20 pointer-events-auto" title="Login to favorite">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Thumbnail Navigation -->
                            <?php if (!empty($gallery_images)): ?>
                                <div class="mt-4">
                                    <div class="flex gap-2 overflow-x-auto pb-2" id="thumbnailContainer">
                                        <!-- Thumbnail for main image -->
                                        <button class="thumbnail-btn flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-black transition" data-index="0">
                                            <img src="<?= esc(listing_thumbnail_src($listing)) ?>" alt="Thumbnail 1" class="w-full h-full object-cover">
                                        </button>
                                        
                                        <!-- Thumbnails for gallery images -->
                                        <?php foreach ($gallery_images as $index => $gpath): ?>
                                            <button class="thumbnail-btn flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-transparent transition hover:border-gray-400" data-index="<?= $index + 1 ?>">
                                                <img src="<?= esc(listing_image_src($gpath)) ?>" alt="Thumbnail <?= $index + 2 ?>" class="w-full h-full object-cover">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-2 mt-4"><?= esc($listing['province'] ? $listing['province'] . ', ' : '') ?><?= esc($listing['city']) ?> · <?= esc($listing['category']) ?></p>
                        <h1 class="text-4xl font-bold text-black mb-4"><?= esc($listing['title']) ?></h1>
                    </div>

                    <div class="listing-stats-grid grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-8">
                        <div class="listing-stat-card p-4 border border-gray-200 rounded-lg text-center bg-white">
                            <p class="listing-stat-value text-2xl sm:text-3xl font-bold text-black">
                                <?= esc($listing['bedrooms']) ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                Bedroom<?= (int)$listing['bedrooms'] !== 1 ? 's' : '' ?>
                            </p>
                        </div>

                        <div class="listing-stat-card p-4 border border-gray-200 rounded-lg text-center bg-white">
                            <p class="listing-stat-value text-2xl sm:text-3xl font-bold text-black">
                                <?= esc($listing['guests']) ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                Guest<?= (int)$listing['guests'] !== 1 ? 's' : '' ?>
                            </p>
                        </div>

                        <div class="listing-stat-card p-4 border border-gray-200 rounded-lg text-center bg-white">
                            <p class="listing-price-value text-2xl sm:text-3xl font-bold text-black break-words">
                                ₱<?= esc(number_format((float)$listing['price'], 0)) ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                Per night
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-8 mb-8">
                        <h2 class="text-2xl font-bold text-black mb-4">About This Property</h2>
                        <p class="text-gray-700 leading-relaxed"><?= esc($listing['description']) ?></p>
                    </div>

                    <?php if ($host = dominium_user_by_id($listing['user_id'])): ?>
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-bold text-black mb-4">Hosted by <?= esc($host['name']) ?></h3>
                            <p class="text-gray-700"><?= esc($host['bio'] ?? 'Experienced host on Dominium') ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div>
                    <div class="border border-gray-200 rounded-lg p-6 sticky top-32">
                        <div class="mb-6">
                            <p class="text-4xl font-bold text-black mb-1">₱<?= esc($listing['price']) ?></p>
                            <p class="text-gray-600">per night</p>
                        </div>

                        <?php if (is_authenticated()): ?>
                            <?php if (dominium_is_listing_booked($listing['id'])): ?>
                                <button disabled class="w-full block text-center px-6 py-3 bg-gray-400 text-white font-semibold rounded cursor-not-allowed mb-4">
                                    Currently Booked
                                </button>
                            <?php else: ?>
                                <a href="<?= url('listings/' . $listing['id'] . '/book') ?>" class="w-full block text-center px-6 py-3 bg-black text-white font-semibold rounded hover:bg-gray-800 transition mb-4">
                                    Book Now
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button onclick="openAuthModal()" class="w-full block text-center px-6 py-3 bg-black text-white font-semibold rounded hover:bg-gray-800 transition mb-4">
                                Login to Book
                            </button>
                        <?php endif; ?>

                        <div class="space-y-2 text-sm text-gray-600">
                            <p>✓ Free cancellation</p>
                            <p>✓ Instant confirmation</p>
                            <p>✓ Verified property</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lightbox Overlay -->
            <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center">
    <div class="relative w-full h-full flex items-center justify-center p-4">
        <!-- Close Button -->
        <button id="lightboxClose" class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full w-12 h-12 flex items-center justify-center hover:bg-opacity-70 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Lightbox Image Container -->
        <div class="relative max-w-6xl max-h-full">
            <div id="lightboxImageContainer" class="relative overflow-hidden rounded-lg" style="max-height: 90vh;">
                <img id="lightboxImage" src="" alt="" class="max-w-full max-h-full object-contain cursor-zoom-out">
                
                <!-- Lightbox Navigation -->
                <button id="lightboxPrev" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full w-12 h-12 flex items-center justify-center hover:bg-opacity-70 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button id="lightboxNext" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full w-12 h-12 flex items-center justify-center hover:bg-opacity-70 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                
                <!-- Lightbox Counter -->
                <div class="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                    <span id="lightboxCounter">1</span> / <span id="lightboxTotal"><?= 1 + count($gallery_images) ?></span>
                </div>
            </div>
            
            <!-- Lightbox Thumbnails -->
            <?php if (!empty($gallery_images)): ?>
                <div class="mt-4">
                    <div class="flex gap-2 overflow-x-auto justify-center pb-2" id="lightboxThumbnails">
                        <!-- Thumbnail for main image -->
                        <button class="lightbox-thumbnail-btn flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-white transition" data-index="0">
                            <img src="<?= esc(listing_thumbnail_src($listing)) ?>" alt="Thumbnail 1" class="w-full h-full object-cover">
                        </button>
                        
                        <!-- Thumbnails for gallery images -->
                        <?php foreach ($gallery_images as $index => $gpath): ?>
                            <button class="lightbox-thumbnail-btn flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 border-transparent transition hover:border-gray-400" data-index="<?= $index + 1 ?>">
                                <img src="<?= esc(listing_image_src($gpath)) ?>" alt="Thumbnail <?= $index + 2 ?>" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
        <?php endif; ?>
    </div>
</main>

<?php if ($listing): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image data
    const images = [
        '<?= esc(listing_thumbnail_src($listing)) ?>'
        <?php if (!empty($gallery_images)): ?>
            <?php foreach ($gallery_images as $gpath): ?>
                , '<?= esc(listing_image_src($gpath)) ?>'
            <?php endforeach; ?>
        <?php endif; ?>
    ];
    
    let currentIndex = 0;
    let isZoomed = false;
    let touchStartX = 0;
    let touchEndX = 0;
    
    // DOM elements
    const mainImage = document.getElementById('mainImage');
    const mainImageContainer = document.getElementById('mainImageContainer');
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxImageContainer = document.getElementById('lightboxImageContainer');
    const imageCounter = document.getElementById('imageCounter');
    const lightboxCounter = document.getElementById('lightboxCounter');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const expandBtn = document.getElementById('expandBtn');
    const lightboxClose = document.getElementById('lightboxClose');
    const thumbnailContainer = document.getElementById('thumbnailContainer');
    const lightboxThumbnails = document.getElementById('lightboxThumbnails');
    
    // Update image display with pan animation
    function updateImage(index, animate = true) {
        if (index < 0 || index >= images.length) return;
        
        const oldIndex = currentIndex;
        currentIndex = index;
        
        // Determine animation direction and type
        let direction = index > oldIndex ? 'right' : 'left';
        let isCircular = false;
        
        // Check for circular navigation
        if (index === 0 && oldIndex === images.length - 1) {
            direction = 'right'; // Forward circular
            isCircular = true;
        } else if (index === images.length - 1 && oldIndex === 0) {
            direction = 'left'; // Backward circular
            isCircular = true;
        }
        
        if (animate) {
            // Animate main image with pan effect
            animateImagePanTransition(mainImage, images[currentIndex], direction, isCircular);
            // Animate lightbox image with pan effect
            animateImagePanTransition(lightboxImage, images[currentIndex], direction, isCircular);
        } else {
            // Direct update (no animation)
            mainImage.src = images[currentIndex];
            lightboxImage.src = images[currentIndex];
        }
        
        imageCounter.textContent = currentIndex + 1;
        lightboxCounter.textContent = currentIndex + 1;
        
        // Update thumbnail active states
        updateThumbnailStates();
        
        // Update navigation button visibility
        updateNavigationButtons();
    }
    
    // Animate image with pan transition
    function animateImagePanTransition(imgElement, newSrc, direction, isCircular) {
        const container = imgElement.parentElement;
        
        // Create transition container
        const transitionContainer = document.createElement('div');
        transitionContainer.className = 'absolute inset-0 overflow-hidden z-10';
        
        // Create sliding panels
        const currentPanel = document.createElement('div');
        currentPanel.className = 'absolute inset-0 flex items-center justify-center';
        currentPanel.innerHTML = `<img src="${imgElement.src}" class="max-w-full max-h-full object-contain">`;
        
        const newPanel = document.createElement('div');
        newPanel.className = 'absolute inset-0 flex items-center justify-center';
        newPanel.innerHTML = `<img src="${newSrc}" class="max-w-full max-h-full object-contain">`;
        
        // Set initial positions based on direction
        if (direction === 'right') {
            newPanel.style.transform = 'translateX(100%)';
        } else {
            newPanel.style.transform = 'translateX(-100%)';
        }
        
        transitionContainer.appendChild(currentPanel);
        transitionContainer.appendChild(newPanel);
        container.appendChild(transitionContainer);
        
        // Start animation
        setTimeout(() => {
            if (direction === 'right') {
                currentPanel.style.transform = 'translateX(-100%)';
                newPanel.style.transform = 'translateX(0%)';
            } else {
                currentPanel.style.transform = 'translateX(100%)';
                newPanel.style.transform = 'translateX(0%)';
            }
        }, 10);
        
        // Update actual image and cleanup
        setTimeout(() => {
            imgElement.src = newSrc;
        }, 150);
        
        // Remove transition container
        setTimeout(() => {
            if (container.contains(transitionContainer)) {
                container.removeChild(transitionContainer);
            }
        }, 450);
    }
    
    // Fallback fade transition for older browsers
    function animateImageTransition(imgElement, newSrc, direction) {
        const container = imgElement.parentElement;
        
        // Create overlay for smooth transition
        const overlay = document.createElement('div');
        overlay.className = 'absolute inset-0 bg-black transition-opacity duration-300 z-10';
        overlay.style.opacity = '0';
        container.appendChild(overlay);
        
        // Fade to black
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
        
        // Change image mid-transition
        setTimeout(() => {
            imgElement.src = newSrc;
        }, 150);
        
        // Fade from black
        setTimeout(() => {
            overlay.style.opacity = '0';
        }, 150);
        
        // Remove overlay
        setTimeout(() => {
            if (container.contains(overlay)) {
                container.removeChild(overlay);
            }
        }, 450);
    }
    
    // Update thumbnail active states with animation
    function updateThumbnailStates() {
        // Main carousel thumbnails
        if (thumbnailContainer) {
            const thumbnails = thumbnailContainer.querySelectorAll('.thumbnail-btn');
            thumbnails.forEach((thumb, index) => {
                const img = thumb.querySelector('img');
                if (index === currentIndex) {
                    thumb.classList.add('border-black');
                    thumb.classList.remove('border-transparent');
                    img.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
                    thumb.style.transform = 'scale(1.05)';
                } else {
                    thumb.classList.remove('border-black');
                    thumb.classList.add('border-transparent');
                    img.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
                    thumb.style.transform = 'scale(1)';
                }
            });
        }
        
        // Lightbox thumbnails
        if (lightboxThumbnails) {
            const lightboxThumbBtns = lightboxThumbnails.querySelectorAll('.lightbox-thumbnail-btn');
            lightboxThumbBtns.forEach((thumb, index) => {
                const img = thumb.querySelector('img');
                if (index === currentIndex) {
                    thumb.classList.add('border-white');
                    thumb.classList.remove('border-transparent');
                    img.classList.add('ring-2', 'ring-white', 'ring-offset-2');
                    thumb.style.transform = 'scale(1.1)';
                } else {
                    thumb.classList.remove('border-white');
                    thumb.classList.add('border-transparent');
                    img.classList.remove('ring-2', 'ring-white', 'ring-offset-2');
                    thumb.style.transform = 'scale(1)';
                }
            });
        }
    }
    
    // Update navigation button visibility
    function updateNavigationButtons() {
        const hasMultiple = images.length > 1;
        
        // Main carousel
        if (prevBtn) prevBtn.style.display = hasMultiple ? 'flex' : 'none';
        if (nextBtn) nextBtn.style.display = hasMultiple ? 'flex' : 'none';
        
        // Lightbox
        if (lightboxPrev) lightboxPrev.style.display = hasMultiple ? 'flex' : 'none';
        if (lightboxNext) lightboxNext.style.display = hasMultiple ? 'flex' : 'none';
    }
    
    // Navigation functions with circular navigation
    function nextImage() {
        let nextIndex = currentIndex + 1;
        if (nextIndex >= images.length) {
            nextIndex = 0; // Loop to first image
        }
        updateImage(nextIndex);
    }
    
    function prevImage() {
        let prevIndex = currentIndex - 1;
        if (prevIndex < 0) {
            prevIndex = images.length - 1; // Loop to last image
        }
        updateImage(prevIndex);
    }
    
    // Zoom functionality
    function toggleZoom(container, image) {
        isZoomed = !isZoomed;
        
        if (isZoomed) {
            container.classList.add('cursor-zoom-out');
            container.classList.remove('cursor-zoom-in');
            image.classList.add('scale-150');
            image.classList.remove('scale-100');
        } else {
            container.classList.add('cursor-zoom-in');
            container.classList.remove('cursor-zoom-out');
            image.classList.add('scale-100');
            image.classList.remove('scale-150');
        }
    }
    
    // Lightbox functions
    function openLightbox() {
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        lightboxImage.src = images[currentIndex];
    }
    
    function closeLightbox() {
        lightbox.classList.add('hidden');
        document.body.style.overflow = '';
        // Reset zoom when closing
        if (isZoomed) {
            toggleZoom(lightboxImageContainer, lightboxImage);
        }
    }
    
    // Touch gesture support
    function handleTouchStart(e) {
        touchStartX = e.changedTouches[0].screenX;
    }
    
    function handleTouchEnd(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipeGesture();
    }
    
    function handleSwipeGesture() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextImage(); // Swipe left, go to next
            } else {
                prevImage(); // Swipe right, go to previous
            }
        }
    }
    
    // Event listeners
    
    // Main carousel navigation
    if (prevBtn) prevBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        prevImage();
    });
    if (nextBtn) nextBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        nextImage();
    });
    
    // Thumbnail clicks
    if (thumbnailContainer) {
        thumbnailContainer.addEventListener('click', function(e) {
            const thumbBtn = e.target.closest('.thumbnail-btn');
            if (thumbBtn) {
                e.stopPropagation();
                const index = parseInt(thumbBtn.dataset.index);
                updateImage(index);
            }
        });
    }
    
    // Zoom functionality - only trigger if clicking directly on image, not on buttons
    if (mainImageContainer) {
        mainImageContainer.addEventListener('click', function(e) {
            // Check if click is on the image itself or empty space
            if (e.target === mainImage || e.target === this) {
                toggleZoom(this, mainImage);
            }
        });
    }
    
    // Lightbox controls
    if (expandBtn) {
        expandBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            openLightbox();
        });
    }
    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }
    
    if (lightboxPrev) lightboxPrev.addEventListener('click', function(e) {
        e.stopPropagation();
        prevImage();
    });
    if (lightboxNext) lightboxNext.addEventListener('click', function(e) {
        e.stopPropagation();
        nextImage();
    });
    
    // Lightbox thumbnails
    if (lightboxThumbnails) {
        lightboxThumbnails.addEventListener('click', function(e) {
            const thumbBtn = e.target.closest('.lightbox-thumbnail-btn');
            if (thumbBtn) {
                e.stopPropagation();
                const index = parseInt(thumbBtn.dataset.index);
                updateImage(index);
            }
        });
    }
    
    // Lightbox zoom - only trigger if clicking directly on image
    if (lightboxImageContainer) {
        lightboxImageContainer.addEventListener('click', function(e) {
            // Check if click is on the image itself or empty space
            if (e.target === lightboxImage || e.target === this) {
                toggleZoom(this, lightboxImage);
            }
        });
    }
    
    // Close lightbox on background click
    if (lightbox) {
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    }
    
    // Touch gestures for main carousel
    if (mainImageContainer) {
        mainImageContainer.addEventListener('touchstart', handleTouchStart, { passive: true });
        mainImageContainer.addEventListener('touchend', handleTouchEnd, { passive: true });
    }
    
    // Touch gestures for lightbox
    if (lightboxImageContainer) {
        lightboxImageContainer.addEventListener('touchstart', handleTouchStart, { passive: true });
        lightboxImageContainer.addEventListener('touchend', handleTouchEnd, { passive: true });
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (lightbox && !lightbox.classList.contains('hidden')) {
            switch(e.key) {
                case 'ArrowLeft':
                    prevImage();
                    break;
                case 'ArrowRight':
                    nextImage();
                    break;
                case 'Escape':
                    closeLightbox();
                    break;
            }
        } else {
            // Keyboard navigation for main carousel when not in lightbox
            switch(e.key) {
                case 'ArrowLeft':
                    prevImage();
                    break;
                case 'ArrowRight':
                    nextImage();
                    break;
            }
        }
    });
    
    // Add CSS transitions for thumbnails and pan animations
    function addThumbnailTransitions() {
        const style = document.createElement('style');
        style.textContent = `
            .thumbnail-btn {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .thumbnail-btn img {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .lightbox-thumbnail-btn {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .lightbox-thumbnail-btn img {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #mainImage, #lightboxImage {
                transition: opacity 0.3s ease-in-out;
            }
            .carousel-loading {
                opacity: 0.5;
            }
            .carousel-transition-panel {
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .carousel-transition-container {
                pointer-events: none;
            }
            .carousel-transition-panel img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Initialize
    addThumbnailTransitions();
    updateImage(0, false); // No animation on initial load
    
    // Favorites functionality
    const favoriteBtn = document.getElementById('favoriteBtn');
    const favoriteIcon = document.getElementById('favoriteIcon');
    
    // Only initialize favorites if user is logged in and button exists
    if (favoriteBtn && favoriteIcon) {
        // Check if listing is already favorited
        function checkFavoriteStatus() {
            const listingId = favoriteBtn?.dataset.listingId;
            if (!listingId) {
                console.error('No listing ID found for favorite check');
                return;
            }
            
            console.log('Checking favorite status for listing:', listingId);
            
            fetch('<?= url('api/favorites/check') ?>?listing_id=' + listingId)
                .then(response => {
                    console.log('Favorite check response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Favorite check response data:', data);
                    updateFavoriteButton(data.is_favorite);
                })
                .catch(error => {
                    console.error('Error checking favorite status:', error);
                });
        }
    
    // Update favorite button appearance
    function updateFavoriteButton(isFavorite) {
        if (!favoriteIcon) return;
        
        if (isFavorite) {
            favoriteIcon.classList.remove('text-gray-400');
            favoriteIcon.classList.add('text-red-500');
            favoriteIcon.setAttribute('fill', 'currentColor');
        } else {
            favoriteIcon.classList.remove('text-red-500');
            favoriteIcon.classList.add('text-gray-400');
            favoriteIcon.setAttribute('fill', 'none');
        }
    }
    
    // Toggle favorite status
    async function toggleFavorite() {
        const listingId = favoriteBtn?.dataset.listingId;
        if (!listingId) {
            console.error('No listing ID found');
            return;
        }
        
        const isCurrentlyFavorite = favoriteIcon.classList.contains('text-red-500');
        const endpoint = isCurrentlyFavorite ? 'remove' : 'add';
        
        // Show loading state
        favoriteBtn.disabled = true;
        favoriteIcon.classList.add('opacity-50');
        
        // Fetch fresh CSRF token before each request (token regenerates after POST)
        let freshToken = '';
        try {
            const tokenResponse = await fetch('<?php echo url('api/csrf-token'); ?>');
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
        
        fetch('<?php echo url('api/favorites/'); ?>' + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': freshToken
            },
            body: 'listing_id=' + listingId + '&csrf_token=' + encodeURIComponent(freshToken)
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (response.status === 401) {
                throw new Error('Please login to favorite listings');
            }
            if (response.status === 403) {
                throw new Error('CSRF token error. Please refresh the page.');
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data && data.success) {
                updateFavoriteButton(!isCurrentlyFavorite);
                
                // Show toast notification
                showToast(data.message, isCurrentlyFavorite ? 'removed' : 'added');
            } else {
                showToast(data.error || 'Failed to update favorites', 'error');
            }
        })
        .catch(error => {
            console.error('Error toggling favorite:', error);
            let errorMessage = 'Failed to update favorites';
            
            if (error.message.includes('login')) {
                errorMessage = 'Please login to favorite listings';
                // Optionally redirect to login after a delay
                setTimeout(() => {
                    if (confirm('You need to login to favorite listings. Go to login page?')) {
                        window.location.href = '<?= url('login') ?>';
                    }
                }, 2000);
            }
            
            showToast(errorMessage, 'error');
        })
        .finally(() => {
            // Remove loading state
            favoriteBtn.disabled = false;
            favoriteIcon.classList.remove('opacity-50');
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
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
    
    // Event listeners
        if (favoriteBtn) {
            favoriteBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleFavorite();
            });
        }
        
        // Check favorite status on page load
        checkFavoriteStatus();
    }
});
</script>
<?php endif; ?>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
