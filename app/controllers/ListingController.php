<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

class ListingController
{
    public static function becomeLister(): void
    {
        $user = auth_user();

        if ($user['role'] !== 'renter') {
            header('Location: ' . url('dashboard'));
            exit;
        }

        // Ensure lister_application_pending column exists
        try {
            db()->raw("SELECT lister_application_pending FROM users LIMIT 1");
        } catch (Exception $e) {
            db()->raw("ALTER TABLE users ADD COLUMN lister_application_pending BOOLEAN DEFAULT 0");
        }

        // Get fresh user data from database to check pending status
        $freshUser = db()->table('users')->where('id', $user['id'])->get();

        // Check if user already has a pending application
        if (isset($freshUser['lister_application_pending']) && $freshUser['lister_application_pending']) {
            set_flash('info', 'Your application is already pending approval.');
            header('Location: ' . url('dashboard'));
            exit;
        }

        include APP_ROOT . '/app/views/listings/become-lister.php';
    }

    public static function submitBecomeLister(): void
    {
        $user = auth_user();

        if ($user['role'] !== 'renter') {
            header('Location: ' . url('dashboard'));
            exit;
        }

        // Ensure lister_application_pending column exists
        try {
            db()->raw("SELECT lister_application_pending FROM users LIMIT 1");
        } catch (Exception $e) {
            db()->raw("ALTER TABLE users ADD COLUMN lister_application_pending BOOLEAN DEFAULT 0");
        }

        // Get fresh user data from database to check pending status
        $freshUser = db()->table('users')->where('id', $user['id'])->get();

        // Check if user already has a pending application
        if (isset($freshUser['lister_application_pending']) && $freshUser['lister_application_pending']) {
            set_flash('error', 'You already have a pending application. Please wait for approval before submitting another.');
            header('Location: ' . url('dashboard'));
            exit;
        }

        // Set lister_application_pending to 1 to indicate pending application
        // Role remains 'renter' until approved by admin
        db()->table('users')
            ->where('id', $user['id'])
            ->update(['lister_application_pending' => 1]);

        $_SESSION['user']['lister_application_pending'] = 1;

        set_flash('success', 'Your lister application has been submitted. Please wait for admin approval.');
        header('Location: ' . url('dashboard'));
        exit;
    }

    public static function myListings(): void
    {
        include APP_ROOT . '/app/views/listings/my-listings.php';
    }

    public static function create(): void
    {
        include APP_ROOT . '/app/views/listings/create-listing.php';
    }

    public static function submitCreate(): void
    {
        $user = auth_user();
        dominium_ensure_schema();

        // Validate form data
        $errors = dominium_validate_listing_form($_POST);

        if (!empty($errors)) {
            dominium_handle_form_error('listings/create', $errors, $_POST);
        }

        $title = trim($_POST['title'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $bedrooms = trim($_POST['bedrooms'] ?? '');
        $guests = trim($_POST['guests'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $thumbUrl = trim($_POST['thumbnail'] ?? '');
        $galleryUrls = dominium_parse_gallery_url_lines($_POST['gallery_urls'] ?? '');

        $thumbField = $_FILES['thumbnail_image'] ?? null;

        if ($thumbField && ($thumbField['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($thumbField['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $_SESSION['form_data'] = $_POST;
                set_flash('error', 'Thumbnail upload failed. Try a smaller file or use an image URL instead.');
                header('Location: ' . url('listings/create'));
                exit;
            }

            $stored = dominium_store_listing_upload($thumbField);

            if ($stored === null) {
                // Debug: Check what went wrong
                if (IS_DEV) {
                    $debug_info = [];
                    $debug_info['file_error'] = $thumbField['error'] ?? 'unknown';
                    $debug_info['file_size'] = $thumbField['size'] ?? 'unknown';
                    $debug_info['file_name'] = $thumbField['name'] ?? 'unknown';
                    $debug_info['tmp_name'] = $thumbField['tmp_name'] ?? 'unknown';

                    // Check MIME detection
                    if (extension_loaded('fileinfo') && !empty($thumbField['tmp_name'])) {
                        try {
                            $finfo = new finfo(FILEINFO_MIME_TYPE);
                            $mime = $finfo->file($thumbField['tmp_name']);
                            $debug_info['detected_mime'] = $mime;
                        } catch (Exception $e) {
                            $debug_info['mime_error'] = $e->getMessage();
                        }
                    }

                    error_log('THUMBNAIL UPLOAD DEBUG: ' . print_r($debug_info, true));

                    // Also show debug info in browser for immediate feedback
                    $debug_msg = "DEBUG: File: " . ($debug_info['file_name'] ?? 'unknown') .
                                ", Size: " . ($debug_info['file_size'] ?? 'unknown') .
                                ", MIME: " . ($debug_info['detected_mime'] ?? 'not detected') .
                                ", Error: " . ($debug_info['file_error'] ?? 'unknown');

                    set_flash('error', $debug_msg . '. Invalid thumbnail. Use JPG, PNG, WebP, GIF, SVG, BMP, TIFF, ICO, or AVIF up to 5 MB.');
                } else {
                    set_flash('error', 'Invalid thumbnail. Use JPG, PNG, WebP, GIF, SVG, BMP, TIFF, ICO, or AVIF up to 5 MB.');
                }

                $_SESSION['form_data'] = $_POST;
                header('Location: ' . url('listings/create'));
                exit;
            }

            $thumbUrl = $stored;
        }

        $defaultThumb = dominium_default_listing_thumbnail();

        if ($thumbUrl === '') {
            $thumbUrl = $defaultThumb;
        }

        $galleryUploads = dominium_collect_gallery_from_uploads($_FILES['gallery_images'] ?? null);
        $galleryPaths = array_values(array_unique(array_merge($galleryUrls, $galleryUploads)));

        $db = db();

        $listingId = (int) $db->table('listings')->insert([
            'user_id' => $user['id'],
            'title' => $title,
            'province' => $province,
            'city' => $city,
            'price' => (float)$price,
            'category' => $category,
            'bedrooms' => (int)$bedrooms ?: 1,
            'guests' => (int)$guests ?: 1,
            'description' => $description,
            'thumbnail' => $thumbUrl,
            'is_active' => 0,
            'is_approved' => 0,
        ]);

        if ($listingId > 0 && !empty($galleryPaths)) {
            dominium_listing_gallery_save($listingId, $galleryPaths);
        }

        unset($_SESSION['form_data']);

        set_flash('success', 'Listing submitted for admin approval.');
        header('Location: ' . url('my-listings'));
        exit;
    }

    public static function edit($id): void
    {
        $user = auth_user();
        $listing = dominium_listing_for_user($user['id'], $id);

        if (!$listing) {
            set_flash('error', 'Listing not found or access denied.');
            header('Location: ' . url('my-listings'));
            exit;
        }

        include APP_ROOT . '/app/views/listings/edit-listing.php';
    }

    public static function submitEdit($id): void
    {
        $user = auth_user();
        $listing = dominium_listing_for_user($user['id'], $id);

        if (!$listing) {
            set_flash('error', 'Listing not found or access denied.');
            header('Location: ' . url('my-listings'));
            exit;
        }

        dominium_ensure_schema();

        // Validate form data
        $errors = dominium_validate_listing_form($_POST, true);

        if (!empty($errors)) {
            dominium_handle_form_error('listings/' . $id . '/edit', $errors, $_POST);
        }

        $title = trim($_POST['title'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $bedrooms = trim($_POST['bedrooms'] ?? '');
        $guests = trim($_POST['guests'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $thumbUrl = trim($_POST['thumbnail'] ?? '');
        $galleryUrls = dominium_parse_gallery_url_lines($_POST['gallery_urls'] ?? '');

        /*
         * PRICE ALERT SUPPORT
         * The old price is taken from the existing listing.
         * The new price is taken from the submitted edit form.
         * After the listing is successfully updated, the app calls
         * dominium_check_price_alerts() so renters who favorited this listing
         * can see a price alert in their Favorites page.
         */
        $oldPrice = (float)($listing['price'] ?? 0);
        $newPrice = (float)$price;

        $thumbField = $_FILES['thumbnail_image'] ?? null;

        if ($thumbField && ($thumbField['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($thumbField['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $_SESSION['form_data'] = $_POST;
                set_flash('error', 'Thumbnail upload failed.');
                header('Location: ' . url('listings/' . $id . '/edit'));
                exit;
            }

            $stored = dominium_store_listing_upload($thumbField);

            if ($stored === null) {
                $_SESSION['form_data'] = $_POST;
                set_flash('error', 'Invalid thumbnail. Use JPG, PNG, WebP, GIF, SVG, BMP, TIFF, ICO, or AVIF up to 5 MB.');
                header('Location: ' . url('listings/' . $id . '/edit'));
                exit;
            }

            $thumbUrl = $stored;
        }

        $defaultThumb = dominium_default_listing_thumbnail();

        if ($thumbUrl === '') {
            $thumbUrl = $listing['thumbnail'] ?? $listing['image'] ?? $defaultThumb;
        }

        $galleryUploads = dominium_collect_gallery_from_uploads($_FILES['gallery_images'] ?? null);
        $galleryPaths = array_values(array_unique(array_merge($galleryUrls, $galleryUploads)));

        db()->table('listings')
            ->where('id', $id)
            ->update([
                'title' => $title,
                'province' => $province,
                'city' => $city,
                'price' => $newPrice,
                'category' => $category,
                'bedrooms' => (int)$bedrooms ?: 1,
                'guests' => (int)$guests ?: 1,
                'description' => $description,
                'thumbnail' => $thumbUrl,
            ]);

        dominium_listing_gallery_save((int) $id, $galleryPaths);

        /*
         * Trigger price alerts only if the price actually changed.
         * This is wrapped in try/catch so a price-alert issue will not break
         * the normal listing edit workflow.
         */
        if (
            function_exists('dominium_check_price_alerts')
            && $oldPrice > 0
            && $newPrice > 0
            && abs($oldPrice - $newPrice) > 0.0001
        ) {
            try {
                dominium_check_price_alerts((int)$id, $oldPrice, $newPrice);
            } catch (Throwable $e) {
                error_log('Price alert check failed for listing #' . $id . ': ' . $e->getMessage());
            }
        }

        unset($_SESSION['form_data']);

        set_flash('success', 'Listing updated successfully!');
        header('Location: ' . url('my-listings'));
        exit;
    }

    public static function toggle($id): void
    {
        $user = auth_user();
        $listing = dominium_listing_for_user($user['id'], $id);

        if (!$listing) {
            set_flash('error', 'Listing not found or access denied.');
            header('Location: ' . url('my-listings'));
            exit;
        }

        $newStatus = $listing['is_active'] ? 0 : 1;

        if ($newStatus === 1 && empty($listing['is_approved'])) {
            set_flash('error', 'This listing is still pending admin approval and cannot be activated yet.');
            header('Location: ' . url('my-listings'));
            exit;
        }

        db()->table('listings')
            ->where('id', $id)
            ->update(['is_active' => $newStatus]);

        set_flash('success', 'Listing status updated to ' . ($newStatus ? 'Active' : 'Inactive') . '.');
        header('Location: ' . url('my-listings'));
        exit;
    }

    public static function delete($id): void
    {
        $user = auth_user();
        $listing = dominium_listing_for_user($user['id'], $id);

        if (!$listing) {
            set_flash('error', 'Listing not found or access denied.');
            header('Location: ' . url('my-listings'));
            exit;
        }

        $bookingCount = db()->table('bookings')
            ->where('listing_id', $id)
            ->count();

        if ($bookingCount > 0) {
            db()->table('listings')
                ->where('id', $id)
                ->update(['is_active' => 0]);

            set_flash('success', 'Listing has booking records, so it was unpublished instead of deleted.');
            header('Location: ' . url('my-listings'));
            exit;
        }

        db()->table('listings')
            ->where('id', $id)
            ->delete();

        set_flash('success', 'Listing deleted successfully.');
        header('Location: ' . url('my-listings'));
        exit;
    }
}