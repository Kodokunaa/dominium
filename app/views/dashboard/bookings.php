<?php
$page_title = 'My Bookings';
$user = auth_user();

$bookings = [];
$bookings_data_error = null;

try {
    $bookings = dominium_bookings_for_user((int)$user['id']);
} catch (Throwable $e) {
    error_log('User bookings failed: ' . $e->getMessage());
    $bookings_data_error = 'Your bookings cannot be loaded right now. Please check the database connection.';
}

include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen bg-page">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-primary">My Bookings</h1>
            <p class="text-secondary mt-2">View your reservations and booking history.</p>
        </div>

        <?php if (!empty($bookings_data_error)): ?>
            <div class="mb-6 p-4 rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-900 text-sm">
                <?= esc($bookings_data_error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="card text-center py-12 px-6">
                <p class="text-secondary mb-4">You haven't made any bookings yet.</p>
                <a href="<?= url('listings') ?>" class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:opacity-90 transition">
                    Browse Listings
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-5">
                <?php foreach ($bookings as $booking): ?>
                    <?php
                        $listing = $booking;
                        $status = $booking['status'] ?? 'approved';

                        $status_classes = [
                            'approved' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-gray-100 text-gray-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'pending' => 'bg-yellow-100 text-yellow-800',
                        ];

                        $price = isset($booking['price']) ? number_format((float)$booking['price'], 2) : '0.00';
                    ?>

                    <div class="booking-card card p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                            <?php if ($listing): ?>
                                <img
                                    src="<?= esc(listing_thumbnail_src($listing)) ?>"
                                    alt="<?= esc($booking['listing_title'] ?? 'Property') ?>"
                                    class="booking-thumb w-full sm:w-36 h-44 sm:h-32 object-cover rounded-lg"
                                >
                            <?php endif; ?>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                                    <h3 class="text-xl font-bold text-primary leading-tight break-words">
                                        <?= esc($booking['listing_title'] ?? 'Property') ?>
                                    </h3>

                                    <span class="inline-flex w-fit px-3 py-1 rounded-full text-xs font-semibold capitalize <?= $status_classes[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                        <?= esc($status) ?>
                                    </span>
                                </div>

                                <div class="booking-info-grid grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-secondary">Check-in</p>
                                        <p class="font-semibold text-primary break-words">
                                            <?= esc($booking['checkin']) ?>
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-secondary">Check-out</p>
                                        <p class="font-semibold text-primary break-words">
                                            <?= esc($booking['checkout']) ?>
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-secondary">Price</p>
                                        <p class="font-semibold text-primary break-words">
                                            ₱<?= esc($price) ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if (($booking['status'] ?? '') === 'approved' && strtotime($booking['checkin']) > time()): ?>
                                    <div class="mt-5">
                                        <form action="<?= url('bookings/' . $booking['id'] . '/cancel') ?>" method="POST" onsubmit="return confirm('Cancel this booking? Refund will be processed.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition font-medium">
                                                Cancel Booking
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>