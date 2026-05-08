<?php
/**
 * Mock Stripe for development/testing
 * Replace with real Stripe SDK in production
 */

namespace {
    defined('APP_ROOT') OR exit('No direct script access allowed');

    class Stripe {
        public static function setApiKey($key) {
            // Mock: Do nothing for development
        }
    }
    
    class Charge {
        public static function create($data) {
            // Debug: Log the charge data
            if (defined('IS_DEV') && IS_DEV) {
                error_log('MockStripe Charge::create called with: ' . print_r($data, true));
            }
            
            // Validate required fields
            if (empty($data['amount']) || empty($data['currency']) || empty($data['source'])) {
                throw new Exception('Missing required payment information');
            }
            
            // Simulate payment processing delay
            usleep(100000); // 0.1 second delay to simulate processing
            
            // Simulate successful charge with realistic response
            return (object) [
                'id' => 'ch_sim_' . uniqid(),
                'object' => 'charge',
                'amount' => $data['amount'],
                'amount_captured' => $data['amount'],
                'amount_refunded' => 0,
                'currency' => $data['currency'],
                'description' => $data['description'] ?? 'Mock charge',
                'status' => 'succeeded',
                'paid' => true,
                'source' => (object) [
                    'id' => $data['source'],
                    'object' => 'card',
                    'brand' => 'visa',
                    'last4' => '4242',
                    'exp_month' => '12',
                    'exp_year' => '2025',
                    'fingerprint' => 'Xt5EWLLDS7FJjR1cK',
                    'funding' => 'credit',
                    'country' => 'US',
                    'name' => 'Test User'
                ],
                'created' => time(),
                'livemode' => false,
                'metadata' => [
                    'booking_id' => uniqid(),
                    'test_mode' => 'true'
                ]
            ];
        }
    }
}
