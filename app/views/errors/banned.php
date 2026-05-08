<?php defined('APP_ROOT') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Banned - Dominium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1f2937',
                        secondary: '#4b5563',
                        accent: '#c2410c',
                        bg: '#f5f5f4',
                        surface: '#fafaf9',
                        border: '#d6d3d1',
                        success: '#84cc16',
                        error: '#b91c1c'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto p-6">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center">
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364a9 9 0 0 9 9 0 18 0 12 12 12 0 18a9 9 0 0 0-9-9-9 0-18 12-12 12-12 0 18a9 9 0 0 0 9 9 0 18 12 12 12 0 18a9 9 0 0 0-9-9-9 0-18 12 12 12 0 18z"></path>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Account Banned</h1>
                <p class="text-gray-600 mb-6">Your account has been banned.</p>
                <p class="text-gray-600 mb-8">Please contact support for assistance regarding your account status.</p>
            </div>
            
            <div class="mt-8">
                <a href="<?= url('/') ?>" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Return to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
