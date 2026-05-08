<?php
$listing = dominium_listing_by_id($id);
$page_title = $listing ? 'Book: ' . esc($listing['title']) : 'Not Found';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <?php if (!$listing): ?>
            <div class="text-center py-20">
                <h1 class="text-3xl font-bold text-black mb-4">Listing Not Found</h1>
                <a href="<?= url('listings') ?>" class="inline-block px-6 py-3 bg-black text-white rounded hover:bg-gray-800 transition">
                    Back to Listings
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <h1 class="text-3xl font-bold text-black mb-8">Complete Your Booking</h1>

                    <form action="<?= url('listings/' . $listing['id'] . '/book') ?>" method="POST" class="space-y-6">
                        <?= csrf_field() ?>
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h2 class="text-xl font-bold text-black mb-4">Property Details</h2>
                            <div class="flex gap-4">
                                <img src="<?= esc(listing_thumbnail_src($listing)) ?>" alt="<?= esc($listing['title']) ?>" class="w-24 h-24 object-cover rounded">
                                <div>
                                    <p class="text-lg font-bold text-black"><?= esc($listing['title']) ?></p>
                                    <p class="text-gray-600"><?= esc($listing['province'] ? $listing['province'] . ', ' : '') ?><?= esc($listing['city']) ?></p>
                                    <p class="text-gray-600">₱<?= esc($listing['price']) ?>/night</p>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg p-6">
                            <h2 class="text-xl font-bold text-black mb-4">Your Booking</h2>
                            
                            <?php $today = date('Y-m-d'); ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                                <input type="date" name="checkin" id="checkin" required min="<?= $today ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" <?= $is_booked ? 'disabled' : '' ?>>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                                <input type="date" name="checkout" id="checkout" required min="<?= $today ?>" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" <?= $is_booked ? 'disabled' : '' ?>>
                            </div>

                            <?php if ($is_booked): ?>
                                <div class="p-4 bg-red-50 border border-red-200 rounded mb-4">
                                    <p class="text-sm text-red-700">This property is currently booked and cannot accept new bookings until it becomes available.</p>
                                </div>
                            <?php endif; ?>

                        <div class="border border-gray-200 rounded-lg p-6">
                            <h2 class="text-xl font-bold text-black mb-4">Payment Information</h2>
                            <p class="text-sm text-gray-600 mb-4">Enter demo payment information. No real payment will be charged.</p>
                            <div class="stripe-split-fields">
                                <div class="stripe-field stripe-field-full">
                                    <label for="card-number-element">Card Number</label>
                                    <div id="card-number-element" class="stripe-element-box"></div>
                                </div>

                                <div class="stripe-field-grid">
                                    <div class="stripe-field">
                                        <label for="card-expiry-element">Expiry Date</label>
                                        <div id="card-expiry-element" class="stripe-element-box"></div>
                                    </div>

                                    <div class="stripe-field">
                                        <label for="card-cvc-element">CVC</label>
                                        <div id="card-cvc-element" class="stripe-element-box"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="card-element" class="hidden"></div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                                <input type="text" id="zip" placeholder="12345" class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">
                            </div>
                            <div id="card-errors" class="text-red-600 text-sm mt-2"></div>
                        </div>
                        
                        <button type="submit" class="w-full px-6 py-4 bg-black text-white font-semibold rounded hover:bg-gray-800 transition text-lg" id="submit-button" <?= $is_booked ? 'disabled' : '' ?>>
                            <?= $is_booked ? 'Currently Booked' : 'Confirm Booking & Pay' ?>
                        </button>
                    </form>
                </div>

                <!-- Summary -->
                <div>
                    <div class="border border-gray-200 rounded-lg p-6 sticky top-32">
                        <h3 class="text-lg font-bold text-black mb-4">Booking Summary</h3>
                        <div class="space-y-3 text-sm mb-4 pb-4 border-b border-gray-200">
                            <div class="flex justify-between">
                                <span class="text-gray-600">₱<?= esc($listing['price']) ?> × nights</span>
                                <span class="font-semibold text-black" id="total">TBD</span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-xs">Your booking will be confirmed immediately upon payment</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
