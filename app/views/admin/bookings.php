<?php
$page_title = 'Pending Bookings';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-black mb-2">Pending Bookings</h1>
                <p class="text-gray-600">Review and approve/reject booking requests from guests.</p>
            </div>
            <a href="<?= url('admin') ?>" class="px-5 py-3 border border-gray-300 rounded hover:bg-gray-50 transition">Back to dashboard</a>
        </div>

        <?php if (empty($bookings)): ?>
            <div class="p-6 bg-white border border-gray-200 rounded-lg text-gray-600">
                There are no pending bookings at the moment.
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($bookings as $booking): ?>
                    <?php 
                        $listing = dominium_listing_by_id($booking['listing_id']);
                        $renter = dominium_user_by_id($booking['user_id']);
                    ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">PROPERTY</p>
                                <p class="font-semibold text-black"><?= esc($listing['title'] ?? 'Listing #' . $booking['listing_id']) ?></p>
                                <p class="text-sm text-gray-600"><?= esc($listing['city'] ?? '') ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">GUEST</p>
                                <p class="font-semibold text-black"><?= esc($booking['guest_name']) ?></p>
                                <p class="text-sm text-gray-600"><?= esc($booking['guest_email']) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">DATES</p>
                                <p class="font-semibold text-black"><?= date('M j', strtotime($booking['checkin'])) ?> - <?= date('M j, Y', strtotime($booking['checkout'])) ?></p>
                                <p class="text-sm text-gray-600">₱<?= number_format($booking['price'], 2) ?>/night</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                            <form action="<?= url('admin/bookings/' . $booking['id'] . '/approve') ?>" method="POST" class="inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-semibold">
                                    ✓ Approve
                                </button>
                            </form>
                            <form action="<?= url('admin/bookings/' . $booking['id'] . '/reject') ?>" method="POST" class="inline" onsubmit="return confirm('Reject this booking?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition font-semibold">
                                    ✕ Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
