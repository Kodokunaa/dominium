<?php
$page_title = 'My Listings';
$user = auth_user();
$listings = [];
$my_listings_error = null;
try {
    $listings = dominium_listings_by_user((int)$user['id']);
} catch (Throwable $e) {
    error_log('My listings failed: ' . $e->getMessage());
    $my_listings_error = 'Your listings cannot be loaded right now. Please check the database connection.';
}
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-black">My Listings</h1>
            <?php if ($user['role'] === 'lister'): ?>
                <a href="<?= url('listings/create') ?>" class="px-6 py-3 bg-black text-white rounded hover:bg-gray-800 transition font-semibold">
                    + Add Listing
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($my_listings_error)): ?>
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded mb-8 text-yellow-900 text-sm">
                <?= esc($my_listings_error) ?>
            </div>
        <?php endif; ?>

        <?php if ($user['role'] === 'lister' && !$user['is_approved']): ?>
            <div class="p-6 bg-yellow-50 border border-yellow-200 rounded mb-8 text-yellow-800">
                <p class="font-semibold mb-2">⏳ Your Lister Account is Pending Approval</p>
                <p class="text-sm">You can still submit listings, but each listing will await admin approval before going live.</p>
            </div>
        <?php endif; ?>

        <?php if (empty($listings)): ?>
            <div class="text-center py-12 border border-gray-200 rounded-lg">
                <p class="text-gray-600 mb-4">You haven't created any listings yet.</p>
                <?php if ($user['is_approved']): ?>
                    <a href="<?= url('listings/create') ?>" class="inline-block px-6 py-3 bg-black text-white rounded hover:bg-gray-800 transition">
                        Create Your First Listing
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-6">
                <?php foreach ($listings as $listing): ?>
                    <?php $booking_count = dominium_booking_count_for_listing($listing['id']); ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                        <div class="flex flex-col gap-6 p-6 lg:flex-row lg:items-start">
                            <img src="<?= esc(listing_thumbnail_src($listing)) ?>" alt="<?= esc($listing['title']) ?>" class="w-full lg:w-40 h-40 object-cover rounded">
                            <div class="flex-1">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-black mb-2"><?= esc($listing['title']) ?></h3>
                                        <p class="text-gray-600 mb-4"><?= esc($listing['province'] ? $listing['province'] . ', ' : '') ?><?= esc($listing['city']) ?> · <?= esc($listing['category']) ?></p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full <?= !$listing['is_approved'] ? 'bg-yellow-100 text-yellow-800' : ($listing['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') ?>">
                                            <?= !$listing['is_approved'] ? 'Pending Approval' : ($listing['is_active'] ? 'Active' : 'Inactive') ?>
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                            <?= $booking_count ?> booking<?= $booking_count === 1 ? '' : 's' ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm text-gray-700 mb-6">
                                    <div>
                                        <p class="text-gray-600">Price</p>
                                        <p class="font-semibold text-black">₱<?= esc($listing['price']) ?>/night</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Capacity</p>
                                        <p class="font-semibold text-black"><?= esc($listing['guests']) ?> guests</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Bedrooms</p>
                                        <p class="font-semibold text-black"><?= esc($listing['bedrooms']) ?> beds</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <a href="<?= url('listings/' . $listing['id']) ?>" class="px-4 py-2 border border-gray-300 rounded text-sm hover:bg-gray-50 transition">
                                        View
                                    </a>
                                    <a href="<?= url('listings/' . $listing['id'] . '/edit') ?>" class="px-4 py-2 bg-white border border-black text-black rounded text-sm hover:bg-gray-100 transition">
                                        Edit
                                    </a>
                                    <?php if (!$listing['is_approved']): ?>
                                        <button type="button" disabled class="px-4 py-2 bg-yellow-500 text-black rounded text-sm cursor-not-allowed">
                                            Pending approval
                                        </button>
                                    <?php else: ?>
                                        <form action="<?= url('listings/' . $listing['id'] . '/toggle') ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded text-sm hover:bg-gray-800 transition">
                                                <?= $listing['is_active'] ? 'Set Inactive' : 'Publish' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?= url('listings/' . $listing['id'] . '/delete') ?>" method="POST" class="inline" onsubmit="return confirm('Delete this listing? This cannot be undone.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-4 py-2 bg-white border border-black text-black rounded text-sm hover:bg-gray-100 transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
