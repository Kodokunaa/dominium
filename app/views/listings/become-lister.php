<?php
$page_title = 'Become a Lister';
include APP_ROOT . '/app/views/partials/header.php';
?>

<main class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-4xl font-bold text-black mb-4">Become a Lister</h1>
        <p class="text-gray-600 mb-12">List your properties and start earning on Dominium</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-bold text-black mb-6">Why List on Dominium?</h2>
                <ul class="space-y-4">
                    <li class="flex gap-3">
                        <span class="text-black font-bold">✓</span>
                        <span class="text-gray-700">Reach thousands of verified travelers</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-black font-bold">✓</span>
                        <span class="text-gray-700">Premium marketplace with vetted hosts</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-black font-bold">✓</span>
                        <span class="text-gray-700">Simple and intuitive property management</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-black font-bold">✓</span>
                        <span class="text-gray-700">24/7 support for listers</span>
                    </li>
                </ul>
            </div>

            <div class="border border-gray-200 rounded-lg p-8">
                <h2 class="text-2xl font-bold text-black mb-6">Ready to List?</h2>
                <p class="text-gray-700 mb-6">
                    Submit your application to become a verified lister. Our admin team will review your profile and approve your account within 24 hours.
                </p>
                
                <?php if ($user['lister_application_pending']): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                        <h4 class="text-yellow-800 font-medium mb-2">Application Already Submitted</h4>
                        <p class="text-yellow-700 text-sm">
                            Your lister application is currently pending review. Please wait for admin approval before submitting another application.
                        </p>
                    </div>
                <?php else: ?>
                    <form action="<?= url('become-lister') ?>" method="POST">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full px-6 py-4 bg-black text-white font-semibold rounded hover:bg-gray-800 transition text-lg">
                            Submit Application
                        </button>
                    </form>
                <?php endif; ?>
                <p class="text-xs text-gray-600 mt-4">
                    By submitting, you agree to our terms of service and community guidelines.
                </p>
            </div>
        </div>
    </div>
</main>

<?php include APP_ROOT . '/app/views/partials/footer.php'; ?>
