<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= isset($page_title) ? esc($page_title) . ' - ' : '' ?>Dominium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>public/css/dominium-theme.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            color: #111;
        }
        a {
            color: inherit;
        }
        input, textarea, select, button {
            color: inherit;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Oswald', sans-serif;
            letter-spacing: -0.02em;
        }
        .brand {
            font-family: 'Oswald', sans-serif;
            letter-spacing: -0.05em;
        }
        
        /* Popup Trigger Buttons */
        .popup-trigger {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 40;
        }
        .btn-popup-login {
            background: var(--primary);
            color: var(--text-on-dark);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: var(--shadow-md);
            transition: all 0.2s;
        }
        .btn-popup-signup {
            background: var(--accent);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: var(--shadow-md);
            transition: all 0.2s;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                colors: {
                    'transparent': 'transparent',
                    'black': '#000000',
                    'white': '#ffffff',
                    'gray': {
                        50: '#fafafa',
                        100: '#f5f5f5',
                        200: '#e5e5e5',
                        300: '#d4d4d4',
                        400: '#a3a3a3',
                        500: '#737373',
                        600: '#525252',
                        700: '#404040',
                        800: '#262626',
                        900: '#171717',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-page">
    <header class="sticky top-0 z-50 header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="<?= url('') ?>" class="brand text-3xl font-bold text-primary">Dominium</a>
                
                <nav class="hidden md:flex items-center gap-8">
                    <a href="<?= url('listings') ?>" class="nav-link">Browse</a>
                    <?php if (is_authenticated()): ?>
                        <?php $user = auth_user(); ?>
                        <div class="flex items-center gap-4">
                            <?php if ($user['role'] === 'admin'): ?>
                                <!-- Admin Dropdown Menu -->
                                <div class="relative group">
                                    <button class="btn btn-outline flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-gray-300 hover:border-gray-400 transition-colors">
                                        <span class="font-semibold">Admin</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                        <div class="py-2">
                                            <a href="<?= url('admin/analytics') ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium">
                                                <span class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                    Analytics
                                                </span>
                                            </a>
                                            <a href="<?= url('admin/users') ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium">
                                                <span class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    Users
                                                </span>
                                            </a>
                                            <a href="<?= url('admin/listings') ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium">
                                                <span class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                                    Listings
                                                </span>
                                            </a>
                                            <a href="<?= url('admin/listers') ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium">
                                                <span class="flex items-center gap-2">
                                                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                                    Listers Application
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($user['role'] === 'renter'): ?>
                                <a href="<?= url('become-lister') ?>" class="btn btn-outline text-sm">Become a Lister</a>
                            <?php elseif ($user['role'] === 'lister'): ?>
                                <a href="<?= url('my-listings') ?>" class="nav-link">My Listings</a>
                            <?php endif; ?>
                            <!-- User Profile Dropdown -->
                            <div class="relative group">
                                <button class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                    <?php 
                                    $avatar_url = dominium_avatar_src($user['avatar_path'] ?? '', $user['name'] ?? 'User');
                                    echo '<img src="' . $avatar_url . '" alt="' . esc($user['name'] ?? 'User') . '" class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">';
                                    ?>
                                    <div class="text-left hidden sm:block">
                                        <div class="font-semibold text-gray-900 text-sm"><?= esc($user['first_name'] ?? $user['name']) ?></div>
                                        <div class="text-xs text-gray-500"><?= ucfirst($user['role']) ?></div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-2">
                                        <a href="<?= url('dashboard') ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                                </svg>
                                                Dashboard
                                            </span>
                                        </a>
                                        <a href="<?= url('favorites') ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                                Favorites
                                            </span>
                                        </a>
                                        <a href="<?= url('profile') ?>" class="block px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-gray-900 font-medium">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                My Profile
                                            </span>
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="<?= url('logout') ?>" class="block px-4 py-3 text-red-600 hover:bg-red-50 hover:text-red-900 font-medium">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Logout
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <button onclick="openAuthModal()" class="btn btn-outline flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <span class="font-semibold">Login</span>
                            </button>
                    <?php endif; ?>
                </nav>

                <!-- Mobile Menu Button -->
                <button 
                    type="button"
                    id="mobile-menu-button"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none"
                    aria-label="Open mobile menu"
                >
                    <svg id="mobile-menu-open-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>

                    <svg id="mobile-menu-close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 py-4">
                <nav class="flex flex-col gap-2">
                    <a href="<?= url('') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Home</a>
                    <a href="<?= url('listings') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Browse Listings</a>

                    <?php if (is_authenticated()): ?>
                        <?php $mobileUser = auth_user(); ?>

                        <a href="<?= url('dashboard') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Dashboard</a>
                        <a href="<?= url('bookings') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">My Bookings</a>
                        <a href="<?= url('favorites') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Favorites</a>
                        <a href="<?= url('profile') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">My Profile</a>

                        <?php if ($mobileUser['role'] === 'admin'): ?>
                            <div class="px-4 pt-4 pb-2 text-xs font-bold uppercase tracking-wide text-gray-500">Admin</div>
                            <a href="<?= url('admin/analytics') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Analytics</a>
                            <a href="<?= url('admin/users') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Users</a>
                            <a href="<?= url('admin/listings') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Listings</a>
                            <a href="<?= url('admin/listers') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Lister Applications</a>
                        <?php elseif ($mobileUser['role'] === 'renter'): ?>
                            <a href="<?= url('become-lister') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">Become a Lister</a>
                        <?php elseif ($mobileUser['role'] === 'lister'): ?>
                            <a href="<?= url('my-listings') ?>" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium">My Listings</a>
                        <?php endif; ?>

                        <div class="border-t border-gray-200 my-2"></div>

                        <a href="<?= url('logout') ?>" class="block px-4 py-3 rounded-lg text-red-700 hover:bg-red-50 font-medium">Logout</a>
                    <?php else: ?>
                        <div class="border-t border-gray-200 my-2"></div>

                        <a href="<?= url('login') ?>" class="block px-4 py-3 rounded-lg bg-black text-white text-center font-semibold">Login</a>
                        <a href="<?= url('register') ?>" class="block px-4 py-3 rounded-lg border border-gray-300 text-center font-semibold text-gray-700">Create Account</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <?php if (empty($hide_header_auth_modal)): ?>
    <!-- Auth Modal with Login/Signup Switcher -->
    <div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform scale-95 transition-transform duration-300">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Account</h3>
                    <button onclick="closeAuthModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6L12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <!-- Tab Switcher -->
                    <div class="flex justify-center border-b border-gray-200 mb-6">
                        <button onclick="switchTab('login')" id="login-tab" class="px-6 py-2 font-medium text-gray-700 border-b-2 border-indigo-600 text-indigo-600 transition-all duration-300 transform hover:scale-105">Login</button>
                        <button onclick="switchTab('signup')" id="signup-tab" class="px-6 py-2 font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition-all duration-300 transform hover:scale-105">Sign Up</button>
                    </div>
                    
                    <!-- Login Form -->
                    <div id="login-form" class="space-y-4">
                        <!-- Google OAuth Button -->
                        <a href="<?= url('auth/google') ?>" class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 text-gray-700 font-semibold py-3 rounded hover:bg-gray-50 transition mb-4">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12s.43 3.45 1.18-4.93l2.85-2.22.81-.62z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continue with Google
                        </a>
                        
                        <form action="<?= url('login') ?>" method="POST" class="space-y-4">
                            <?= csrf_field() ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="you@example.com">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="Your password">
                            </div>

                            <button type="submit" class="w-full bg-black text-white font-semibold py-3 rounded hover:bg-gray-800 transition">
                                Sign In
                            </button>
                        </form>
                    </div>
                    
                    <!-- Signup Form -->
                    <div id="signup-form" class="space-y-4 hidden">
                        <!-- Google OAuth Button -->
                        <a href="<?= url('auth/google') ?>" class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 text-gray-700 font-semibold py-3 rounded hover:bg-gray-50 transition mb-4">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12s.43 3.45 1.18-4.93l2.85-2.22.81-.62z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Continue with Google
                        </a>
                        
                        <form action="<?= url('register') ?>" method="POST" class="space-y-4">
                            <?= csrf_field() ?>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="First name">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="Last name">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="you@example.com">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="Min 6 characters">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input type="password" name="password_confirm" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black" placeholder="Repeat password">
                            </div>
                            

                            <button type="submit" class="w-full bg-black text-white font-semibold py-3 rounded hover:bg-gray-800 transition">
                                Create Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openAuthModal() {
        const modal = document.getElementById('auth-modal');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.bg-white').classList.remove('scale-95');
            modal.querySelector('.bg-white').classList.add('scale-100');
        }, 10);
        switchTab('login');
    }

    function closeAuthModal() {
        const modal = document.getElementById('auth-modal');
        modal.classList.add('opacity-0');
        modal.querySelector('.bg-white').classList.remove('scale-100');
        modal.querySelector('.bg-white').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function switchTab(tab) {
        const loginForm = document.getElementById('login-form');
        const signupForm = document.getElementById('signup-form');
        const loginTab = document.getElementById('login-tab');
        const signupTab = document.getElementById('signup-tab');
        
        if (tab === 'login') {
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
            loginTab.classList.add('border-indigo-600', 'text-indigo-600');
            loginTab.classList.remove('border-transparent', 'text-gray-500');
            signupTab.classList.add('border-transparent', 'text-gray-500');
            signupTab.classList.remove('border-indigo-600', 'text-indigo-600');
        } else {
            loginForm.classList.add('hidden');
            signupForm.classList.remove('hidden');
            signupTab.classList.add('border-indigo-600', 'text-indigo-600');
            signupTab.classList.remove('border-transparent', 'text-gray-500');
            loginTab.classList.add('border-transparent', 'text-gray-500');
            loginTab.classList.remove('border-indigo-600', 'text-indigo-600');
        }
    }

    // Close modal when clicking outside
    document.getElementById('auth-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAuthModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAuthModal();
        }
    });

        </script>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const openIcon = document.getElementById('mobile-menu-open-icon');
            const closeIcon = document.getElementById('mobile-menu-close-icon');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');

                    if (openIcon && closeIcon) {
                        openIcon.classList.toggle('hidden');
                        closeIcon.classList.toggle('hidden');
                    }
                });
            }
        });
    </script>

    <?php if ($msg = get_flash('success')): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="flash flash-success">
                <?= esc($msg) ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($msg = get_flash('error')): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="flash flash-error">
                <?= esc($msg) ?>
            </div>
        </div>
    <?php endif; ?>
