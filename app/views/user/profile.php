<?php
$page_title = 'My Profile';
include APP_ROOT . '/app/views/partials/header.php';

$user = auth_user();

if (!$user) {
    header('Location: ' . url('login'));
    exit;
}

$errors = $_SESSION['profile_errors'] ?? [];
$formData = $_SESSION['profile_form_data'] ?? [];
unset($_SESSION['profile_errors'], $_SESSION['profile_form_data']);

try {
    $freshUser = db()->table('users')->where('id', $user['id'])->get();

    if ($freshUser) {
        $user = array_merge($user, $freshUser);
        $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], $freshUser);
    }

    $listerApplicationPending = !empty($freshUser['lister_application_pending']);
} catch (Exception $e) {
    $listerApplicationPending = !empty($user['lister_application_pending']);
}

$name = $user['name'] ?? trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$name = trim($name) !== '' ? $name : 'User';

$role = $user['role'] ?? 'user';
$email = $user['email'] ?? '';
?>

<main class="min-h-screen bg-page">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-primary">My Profile</h1>
                    <p class="text-secondary mt-2">Manage your account information and preferences</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="<?= url('dashboard') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Dashboard
                    </a>

                    <?php if ($role !== 'admin'): ?>
                        <a href="<?= url('favorites') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100">
                            Favorites
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($successMessage = get_flash('success')): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="text-green-800"><?= esc($successMessage) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-red-800 text-sm space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="card overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg sm:text-xl font-bold text-primary">Profile Information</h2>
                    <a href="<?= url('logout') ?>" class="text-sm text-red-700 hover:text-red-900 font-medium">Logout</a>
                </div>
            </div>

            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-8">

                    <!-- Left Profile Summary -->
                    <aside class="text-center lg:text-left">
                        <div class="flex flex-col items-center lg:items-start">
                            <img
                                src="<?= dominium_avatar_src('', $name) ?>"
                                alt="<?= esc($name) ?>"
                                class="w-28 h-28 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg"
                            >

                            <div class="mt-4 w-full">
                                <h3 class="text-xl font-bold text-primary break-words">
                                    <?= esc($name) ?>
                                </h3>

                                <p class="text-secondary text-sm break-all mt-1">
                                    <?= esc($email) ?>
                                </p>

                                <div class="flex flex-wrap gap-2 justify-center lg:justify-start mt-3">
                                    <span class="badge badge-primary capitalize">
                                        <?= esc($role) ?>
                                    </span>

                                    <?php if ($listerApplicationPending): ?>
                                        <span class="badge badge-warning">
                                            Pending Application
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Profile Form -->
                    <section class="min-w-0">
                        <form method="POST" action="<?= url('profile/update') ?>" class="space-y-6">
                            <?= csrf_field() ?>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input
                                        type="text"
                                        name="first_name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-colors"
                                        value="<?= esc($formData['first_name'] ?? $user['first_name'] ?? '') ?>"
                                        placeholder="Enter your first name"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input
                                        type="text"
                                        name="last_name"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-colors"
                                        value="<?= esc($formData['last_name'] ?? $user['last_name'] ?? '') ?>"
                                        placeholder="Enter your last name"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input
                                    type="email"
                                    value="<?= esc($email) ?>"
                                    disabled
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed"
                                >
                                <p class="text-xs text-gray-500 mt-2">
                                    Email address cannot be changed. Contact support if needed.
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                                <a
                                    href="<?= url('dashboard') ?>"
                                    class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 text-center hover:bg-gray-100 transition-colors"
                                >
                                    Cancel
                                </a>

                                <button
                                    type="submit"
                                    class="w-full sm:w-auto px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-primary hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-colors"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </section>

                </div>
            </div>
        </div>

    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>