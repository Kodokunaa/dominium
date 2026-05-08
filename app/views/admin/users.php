<?php
$page_title = 'User Management';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-black mb-2">User Management</h1>
            <p class="text-gray-600">Manage registered users, demote listers, and ban or unban accounts.</p>
        </div>

        <?php if (empty($users)): ?>
            <div class="p-6 bg-white border border-gray-200 rounded-lg text-gray-600">
                There are no users to manage right now.
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($users as $user): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
                        <div class="md:flex md:justify-between md:items-center gap-6">
                            <div>
                                <h2 class="text-xl font-bold text-black"><?= esc($user['name']) ?></h2>
                                <p class="text-gray-600"><?= esc($user['email']) ?></p>
                                <div class="mt-2 flex flex-wrap gap-2 text-sm">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-800">Role: <?= esc($user['role']) ?></span>
                                    <?php if (!empty($user['is_banned'])): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800">Banned</span>
                                    <?php elseif ($user['role'] === 'lister' && empty($user['is_approved'])): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">Pending Approval</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">Active</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                                <?php if ($user['role'] === 'lister' && empty($user['is_banned'])): ?>
                                    <form action="<?= url('admin/users/' . $user['id'] . '/demote') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="px-4 py-2 bg-yellow-500 text-black rounded hover:bg-yellow-400 transition">Demote to Renter</button>
                                    </form>
                                <?php endif; ?>

                                <?php if (!empty($user['is_banned'])): ?>
                                    <form action="<?= url('admin/users/' . $user['id'] . '/unban') ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= esc($user['id']) ?>">
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Unban</button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= url('admin/users/' . $user['id'] . '/ban') ?>" method="POST" class="inline" onsubmit="return confirm('Ban this user and pause their listings?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="user_id" value="<?= esc($user['id']) ?>">
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Ban User</button>
                                    </form>
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