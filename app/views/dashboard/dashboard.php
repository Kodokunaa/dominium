<?php
$page_title = 'Dashboard';
include APP_ROOT . '/app/views/partials/header.php';
$user = auth_user();

$role = $user['role'] ?? 'renter';
$name = $user['name'] ?? 'User';
$email = $user['email'] ?? '';
$initial = strtoupper(substr($name, 0, 1));

if ($role === 'admin') {
    $dashboardTitle = 'Welcome back, ' . $name;
    $dashboardSubtitle = 'Manage users, listings, lister applications, and platform analytics';
} elseif ($role === 'lister') {
    $dashboardTitle = 'Welcome back, ' . $name;
    $dashboardSubtitle = 'Manage your listings, bookings, and account';
} else {
    $dashboardTitle = 'Welcome back, ' . $name;
    $dashboardSubtitle = 'Manage your account, favorites, and bookings';
}
?>

<main class="min-h-screen bg-page">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-primary mb-3">
                <?= esc($dashboardTitle) ?>
            </h1>
            <p class="text-secondary text-lg">
                <?= esc($dashboardSubtitle) ?>
            </p>
        </div>

        <!-- Profile Card -->
        <div class="card dashboard-profile-card mb-10 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
                <div class="dashboard-avatar w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center text-2xl font-bold shrink-0">
                    <?= esc(strtoupper(substr($name ?? $user['name'] ?? 'U', 0, 1))) ?>
                </div>

                <div class="dashboard-profile-info min-w-0 flex-1">
                    <h2 class="text-xl font-bold text-primary truncate">
                        <?= esc($name ?? $user['name'] ?? 'User') ?>
                    </h2>
                    <p class="text-secondary break-all text-sm sm:text-base">
                        <?= esc($email ?? $user['email'] ?? '') ?>
                    </p>
                </div>

                <div class="dashboard-role-badges flex flex-wrap items-center gap-2 sm:justify-end">
                    <span class="badge badge-primary capitalize">
                        <?= esc($role ?? $user['role'] ?? 'user') ?>
                    </span>

                    <?php if (($role ?? $user['role'] ?? '') === 'lister' && empty($user['is_approved'])): ?>
                        <span class="badge badge-warning">
                            Pending approval
                        </span>
                    <?php elseif (!empty($user['lister_application_pending'])): ?>
                        <span class="badge badge-warning">
                            Application pending
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        
        <!-- Quick Actions -->
        <h2 class="text-2xl font-bold text-primary mb-6">Quick Actions</h2>

        <div class="dashboard-action-grid grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">

            <?php if ($role === 'admin'): ?>

                <a href="<?= url('admin/analytics') ?>" class="card card-hover dashboard-action-card p-6 block">
                    <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18m4-14v14m4-10v10M7 13v8M3 17v4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-primary mb-2">Analytics</h3>
                    <p class="text-secondary text-sm">View platform activity, bookings, and real revenue trends</p>
                </a>

                <a href="<?= url('admin/users') ?>" class="card card-hover dashboard-action-card p-6 block">
                    <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-primary mb-2">Users</h3>
                    <p class="text-secondary text-sm">Manage renters, listers, bans, and account roles</p>
                </a>

                <a href="<?= url('admin/listings') ?>" class="card card-hover dashboard-action-card p-6 block">
                    <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h12a1 1 0 001-1V10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-primary mb-2">Listings</h3>
                    <p class="text-secondary text-sm">Review, approve, reject, and monitor property listings</p>
                </a>

                <a href="<?= url('admin/listers') ?>" class="card card-hover dashboard-action-card p-6 block">
                    <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 7h10M7 17h10M5 5h14v14H5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-primary mb-2">Lister Applications</h3>
                    <p class="text-secondary text-sm">Approve or reject users applying to become property listers</p>
                </a>

            <?php else: ?>

                <a href="<?= url('bookings') ?>" class="card card-hover dashboard-action-card p-6 block">
                    <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-primary mb-2">My Bookings</h3>
                    <p class="text-secondary text-sm">View your reservations and booking history</p>
                </a>

                <a href="<?= url('favorites') ?>" class="card card-hover dashboard-action-card p-6 block">
                    <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-primary mb-2">Favorites</h3>
                    <p class="text-secondary text-sm">View the listings you saved for later</p>
                </a>

                <?php if ($role === 'renter' && empty($user['lister_application_pending'])): ?>
                    <a href="<?= url('become-lister') ?>" class="card card-hover dashboard-action-card p-6 block">
                        <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-accent text-white flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2">Become a Lister</h3>
                        <p class="text-secondary text-sm">Apply to list and manage your rental properties</p>
                    </a>
                <?php elseif ($role === 'lister'): ?>
                    <a href="<?= url('my-listings') ?>" class="card card-hover dashboard-action-card p-6 block">
                        <div class="dashboard-action-icon w-12 h-12 rounded-lg bg-secondary text-white flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-primary mb-2">My Listings</h3>
                        <p class="text-secondary text-sm">Manage your rental properties and listing status</p>
                    </a>
                <?php endif; ?>

            <?php endif; ?>

        </div>

        <!-- Account Info -->
        <div class="card p-6">
            <h2 class="text-xl font-bold text-primary mb-6">Account Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-secondary text-sm mb-1">Email</p>
                    <p class="font-semibold text-primary break-all">
                        <?= esc($email) ?>
                    </p>
                </div>

                <div>
                    <p class="text-secondary text-sm mb-1">Role</p>
                    <p class="font-semibold text-primary capitalize">
                        <?= esc($role) ?>
                    </p>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>