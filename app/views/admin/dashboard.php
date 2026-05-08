<?php
$page_title = 'Admin Dashboard';
include APP_ROOT . '/app/views/partials/header.php';
$user = auth_user();
?>

<main class="min-h-screen bg-page">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-primary mb-3">Admin Dashboard</h1>
            <p class="text-secondary text-lg">Welcome, <?= esc($user['name']) ?>. Review pending applications and approve content below.</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="card p-6 flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-primary text-white flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Pending Applications</p>
                    <p class="text-3xl font-bold text-primary">Review</p>
                </div>
            </div>
            <div class="card p-6 flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-accent text-white flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Pending Listings</p>
                    <p class="text-3xl font-bold text-primary">Review</p>
                </div>
            </div>
            <div class="card p-6 flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-secondary text-white flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">User Management</p>
                    <p class="text-3xl font-bold text-primary">Manage</p>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <h2 class="text-2xl font-bold text-primary mb-6">Administration</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <a href="<?= url('admin/listers') ?>" class="card card-hover p-6 block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-primary text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="badge badge-primary">Action Required</span>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2">Pending Lister Applications</h3>
                <p class="text-secondary text-sm mb-4">Review and approve new users who want to become listers on the platform.</p>
                <span class="btn btn-outline w-full text-center">
                    Review Applications →
                </span>
            </a>
            
            <a href="<?= url('admin/listings') ?>" class="card card-hover p-6 block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-accent text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <span class="badge badge-accent">Moderation</span>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2">Pending Listings</h3>
                <p class="text-secondary text-sm mb-4">Review newly submitted property listings and approve them to make them public.</p>
                <span class="btn btn-outline w-full text-center">
                    Review Listings →
                </span>
            </a>

            <a href="<?= url('admin/analytics') ?>" class="card card-hover p-6 block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-success text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <span class="badge badge-success">Analytics</span>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2">Visual Analytics</h3>
                <p class="text-secondary text-sm mb-4">View real-time statistics, charts, and insights about your rental marketplace.</p>
                <span class="btn btn-outline w-full text-center">
                    View Analytics →
                </span>
            </a>

            <a href="<?= url('admin/users') ?>" class="card card-hover p-6 block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-secondary text-white flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="badge badge-secondary">Management</span>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2">User Management</h3>
                <p class="text-secondary text-sm mb-4">Demote listers, ban users, and manage account status from one place.</p>
                <span class="btn btn-outline w-full text-center">
                    Manage Users →
                </span>
            </a>
        </div>

        <!-- Admin Info -->
        <div class="card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center text-lg font-bold">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-primary"><?= esc($user['name']) ?></h3>
                    <p class="text-secondary text-sm"><?= esc($user['email']) ?> • Administrator</p>
                </div>
                <span class="badge badge-primary">Admin</span>
            </div>
        </div>
    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
