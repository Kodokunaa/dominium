<?php
defined('APP_ROOT') OR exit('No direct script access allowed');

// Load Mock Stripe for development
require_once APP_ROOT . '/scheme/MockStripe.php';

class BookingController
{
    public static function show($id): void
    {
        $listing = dominium_listing_by_id($id);
        if (!$listing) {
            http_response_code(404);
            include APP_ROOT . '/app/views/errors/404.php';
            exit;
        }

        $user = auth_user();
        if ($user && (int)$listing['user_id'] === (int)$user['id']) {
            set_flash('error', 'You cannot book your own listing.');
            header('Location: ' . url('listings/' . $id));
            exit;
        }

        $is_booked = dominium_is_listing_booked($id);

        include APP_ROOT . '/app/views/booking/book.php';
    }

    public static function submit($id): void
    {
        $listing = dominium_listing_by_id($id);
        if (!$listing) {
            http_response_code(404);
            include APP_ROOT . '/app/views/errors/404.php';
            exit;
        }

        $checkin = trim($_POST['checkin'] ?? '');
        $checkout = trim($_POST['checkout'] ?? '');
        $stripe_token = trim($_POST['stripeToken'] ?? '');
        $user = auth_user();

        if ((int)$listing['user_id'] === (int)$user['id']) {
            set_flash('error', 'You cannot book your own listing.');
            header('Location: ' . url('listings/' . $id));
            exit;
        }

        if (empty($listing['is_active']) || empty($listing['is_approved'])) {
            set_flash('error', 'This listing is not available for booking.');
            header('Location: ' . url('listings'));
            exit;
        }

        if (empty($checkin) || empty($checkout)) {
            set_flash('error', 'Please select check-in and check-out dates.');
            header('Location: ' . url('listings/' . $id . '/book'));
            exit;
        }

        // Ensure dates are in the future
        $today = date('Y-m-d');
        if ($checkin < $today) {
            set_flash('error', 'Check-in date cannot be in the past.');
            header('Location: ' . url('listings/' . $id . '/book'));
            exit;
        }
        if ($checkout < $today) {
            set_flash('error', 'Check-out date cannot be in the past.');
            header('Location: ' . url('listings/' . $id . '/book'));
            exit;
        }

        // Ensure checkout is at least one day after checkin
        if ($checkout <= $checkin) {
            set_flash('error', 'Check-out date must be at least one day after check-in date.');
            header('Location: ' . url('listings/' . $id . '/book'));
            exit;
        }

        // Debug: Log the stripe token
        if (defined('IS_DEV') && IS_DEV) {
            error_log('Stripe token received: ' . ($stripe_token ?? 'EMPTY'));
            error_log('POST data: ' . print_r($_POST, true));
        }
        
        if (empty($stripe_token)) {
            set_flash('error', 'Payment information is required.');
            header('Location: ' . url('listings/' . $id . '/book'));
            exit;
        }

        // Check availability
        if (!dominium_check_booking_availability($id, $checkin, $checkout)) {
            set_flash('error', 'These dates are not available for booking.');
            header('Location: ' . url('listings/' . $id . '/book'));
            exit;
        }

        // Calculate total price (assuming per night)
        $checkin_date = new DateTime($checkin);
        $checkout_date = new DateTime($checkout);
        $nights = $checkin_date->diff($checkout_date)->days;
        $total_price = $listing['price'] * $nights;

        // Process Stripe payment
        Stripe::setApiKey(STRIPE_SECRET_KEY);
        try {
            $charge = Charge::create([
                'amount' => (int) round($total_price * 100), // Amount in cents
                'currency' => 'php',
                'description' => 'Booking for ' . $listing['title'],
                'source' => $stripe_token,
                'metadata' => [
                    'listing_id' => $id,
                    'user_id' => $user['id'],
                    'checkin' => $checkin,
                    'checkout' => $checkout,
                    'nights' => $nights
                ]
            ]);
        } catch (Exception $e) {
            set_flash('error', 'Payment failed: ' . $e->getMessage());
            header('Location: ' . url('listings/' . $id . '/book'));
            exit;
        }

        $isMockPayment = strpos((string) STRIPE_PUBLISHABLE_KEY, 'pk_test_mock_') === 0
            || strpos((string) STRIPE_SECRET_KEY, 'sk_test_mock_') === 0;

        $postedLast4 = preg_replace('/\D+/', '', $_POST['demo_card_last4'] ?? '');
        $postedBrand = trim($_POST['demo_card_brand'] ?? '');

        $cardLast4 = $charge->source->last4 ?? '****';
        $cardBrand = $charge->source->brand ?? 'visa';

        if ($isMockPayment && strlen($postedLast4) === 4) {
            $cardLast4 = $postedLast4;
        }

        if ($isMockPayment && $postedBrand !== '') {
            $cardBrand = $postedBrand;
        }

        // Save booking with payment details
        $booking_data = [
            'listing_id' => $id,
            'user_id' => $user['id'],
            'guest_name' => $user['name'],
            'guest_email' => $user['email'],
            'checkin' => $checkin,
            'checkout' => $checkout,
            'price' => $total_price,
            'status' => 'approved',
            'booked_at' => date('Y-m-d H:i:s'),
            'payment_id' => $charge->id,
            'payment_amount' => $total_price,
            'payment_status' => $charge->status,
            'payment_method' => $isMockPayment ? 'demo_card' : 'stripe',
            'card_last4' => $cardLast4,
            'card_brand' => $cardBrand
        ];
        
        dominium_save_booking($booking_data);

        // Log successful booking
        if (defined('IS_DEV') && IS_DEV) {
            error_log('Booking successful: ' . json_encode([
                'booking_id' => uniqid(),
                'payment_id' => $charge->id,
                'amount' => $total_price,
                'listing' => $listing['title'],
                'user' => $user['email']
            ]));
        }

        set_flash('success', 'Booking confirmed! Payment of ₱' . number_format($total_price, 2) . ' processed successfully. Charge ID: ' . $charge->id);
        header('Location: ' . url('dashboard'));
        exit;
    }

    public static function cancel($id): void
    {
        $user = auth_user();
        $booking = db()->table('bookings')
            ->where('id', $id)
            ->where('user_id', $user['id'])
            ->get();

        if (!$booking || $booking['status'] !== 'approved') {
            set_flash('error', 'Booking not found or cannot be cancelled.');
            header('Location: ' . url('bookings'));
            exit;
        }

        if (strtotime($booking['checkin']) <= time()) {
            set_flash('error', 'Cannot cancel a booking that has already started.');
            header('Location: ' . url('bookings'));
            exit;
        }

        // Mock refund
        // In real implementation, use Stripe Refund API
        // For simulation, just mark as cancelled

        db()->table('bookings')
            ->where('id', $id)
            ->update(['status' => 'cancelled']);

        set_flash('success', 'Booking cancelled and refund processed.');
        header('Location: ' . url('bookings'));
        exit;
    }
}
