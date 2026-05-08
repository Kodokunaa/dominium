<?php
$page_title = 'Pending Listings';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-black mb-2">Pending Listings</h1>
            <p class="text-gray-600">Approve or reject newly submitted listings before they go live.</p>
        </div>

        <?php if (empty($listings)): ?>
            <div class="p-6 bg-white border border-gray-200 rounded-lg text-gray-600">
                There are no pending listings at the moment.
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($listings as $listing): ?>
                    <?php $owner = dominium_user_by_id($listing['user_id']); ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <div class="md:flex md:justify-between md:items-start gap-6">
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-black mb-2"><?= esc($listing['title']) ?></h2>
                                <p class="text-gray-600 mb-2"><?= esc($listing['province'] ? $listing['province'] . ', ' : '') ?><?= esc($listing['city']) ?> · <?= esc($listing['category']) ?></p>
                                <p class="text-gray-600 mb-3"><?= esc($listing['description']) ?></p>
                                <div class="text-sm text-gray-500">
                                    <p>Owner: <?= esc($owner['name'] ?? 'User ' . $listing['user_id']) ?> <?= esc($owner['email'] ?? '') ?></p>
                                    <p>Submitted: <?= date('F j, Y', strtotime($listing['created_at'])) ?></p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                                <form action="<?= url('admin/listings/' . $listing['id'] . '/approve') ?>" method="POST" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Approve</button>
                                </form>
                                <form action="<?= url('admin/listings/' . $listing['id'] . '/reject') ?>" method="POST" class="inline" onsubmit="return confirm('Reject this listing? This will permanently delete it.');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Reject</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
