<?php
$page_title = 'Pending Lister Applications';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-black mb-2">Pending Lister Applications</h1>
            <p class="text-gray-600">Review new lister requests and approve verified hosts.</p>
        </div>

        <?php if (empty($listers)): ?>
            <div class="p-6 bg-white border border-gray-200 rounded-lg text-gray-600">
                There are no pending lister applications at the moment.
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($listers as $lister): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <div class="md:flex md:items-center md:justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-black"><?= esc($lister['name']) ?></h2>
                                <p class="text-gray-600"><?= esc($lister['email']) ?></p>
                                <p class="text-sm text-gray-500 mt-2">Applied on <?= date('F j, Y', strtotime($lister['created_at'])) ?></p>
                            </div>
                            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                                <form action="<?= url('admin/listers/' . $lister['id'] . '/approve') ?>" method="POST" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Approve</button>
                                </form>
                                <form action="<?= url('admin/listers/' . $lister['id'] . '/reject') ?>" method="POST" class="inline" onsubmit="return confirm('Reject this application?');">
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
