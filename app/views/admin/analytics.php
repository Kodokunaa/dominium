<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Dominium Admin</title>
    <link rel="stylesheet" href="<?= url('public/css/dominium-theme.css') ?>">
    <link rel="stylesheet" href="<?= url('public/css/analytics.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include APP_ROOT . '/app/views/partials/header.php'; ?>
    
    <div class="analytics-container">
        <div class="analytics-header">
            <h1>Analytics Dashboard</h1>
            <p>Real-time insights into your rental marketplace</p>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filter-group">
                <label for="timeRange">Time Range:</label>
                <select id="timeRange">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                
                <label for="chartType">Chart Type:</label>
                <select id="chartType">
                    <option value="line" selected>Line Chart</option>
                    <option value="bar">Bar Chart</option>
                </select>
                
                <button onclick="refreshAnalytics()">Refresh Data</button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid" id="statsGrid">
            <!-- Stats will be populated by JavaScript -->
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- User Registrations Chart -->
            <div class="chart-card full-width">
                <h3>User Registration Trends</h3>
                <div class="chart-container large">
                    <canvas id="userRegistrationsChart"></canvas>
                </div>
            </div>

            <!-- Booking Trends Chart -->
            <div class="chart-card">
                <h3>Booking Trends</h3>
                <div class="chart-container">
                    <canvas id="bookingTrendsChart"></canvas>
                </div>
            </div>

            <!-- Listings by Category Chart -->
            <div class="chart-card">
                <h3>Listings by Category</h3>
                <div class="chart-container">
                    <canvas id="listingsCategoryChart"></canvas>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="chart-card">
                <h3>Revenue Overview</h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart instances
        let userRegistrationsChart, bookingTrendsChart, listingsCategoryChart, revenueChart;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadCharts();
        });

        // Load statistics cards
        async function loadStats() {
            try {
                const response = await fetch('<?= url('admin/analytics/data?type=stats') ?>');
                const stats = await response.json();
                
                // Handle missing or undefined values with defaults
                const safeStats = {
                    users_total: stats.users_total ?? 0,
                    users_new_today: stats.users_new_today ?? 0,
                    users_active: stats.users_active ?? 0,
                    listings_approved: stats.listings_approved ?? 0,
                    listings_pending: stats.listings_pending ?? 0,
                    bookings_total: stats.bookings_total ?? 0,
                    favorites_total: stats.favorites_total ?? 0,
                    favorites_today: stats.favorites_today ?? 0,
                    revenue_total: stats.revenue_total ?? 0,
                    revenue_today: stats.revenue_today ?? 0,
                    users_banned: stats.users_banned ?? 0
                };
                
                const statsGrid = document.getElementById('statsGrid');
                statsGrid.innerHTML = `
                    <div class="stat-card primary">
                        <div class="stat-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg></div>
                        <div class="stat-value">${safeStats.users_total}</div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-change ${safeStats.users_new_today > 0 ? 'positive' : ''}">
                            ${safeStats.users_new_today > 0 ? '↑' : ''} ${safeStats.users_new_today} new today
                        </div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg></div>
                        <div class="stat-value">${safeStats.listings_approved}</div>
                        <div class="stat-label">Active Listings</div>
                        <div class="stat-change ${safeStats.listings_pending > 0 ? 'warning' : ''}">
                            ${safeStats.listings_pending} pending approval
                        </div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                        <div class="stat-value">${safeStats.bookings_total}</div>
                        <div class="stat-label">Total Bookings</div>
                        <div class="stat-change positive">
                            Instant confirmation enabled
                        </div>
                    </div>
                    
                    <div class="stat-card info">
                        <div class="stat-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg></div>
                        <div class="stat-value">${safeStats.favorites_total}</div>
                        <div class="stat-label">Total Favorites</div>
                        <div class="stat-change ${safeStats.favorites_today > 0 ? 'positive' : ''}">
                            ${safeStats.favorites_today} today
                        </div>
                    </div>
                    
                    <div class="stat-card primary">
                        <div class="stat-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                        <div class="stat-value">₱${Number(safeStats.revenue_total || 0).toLocaleString()}</div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-change ${safeStats.revenue_today > 0 ? 'positive' : ''}">
                            ₱${Number(safeStats.revenue_today || 0).toLocaleString()} today
                        </div>
                    </div>
                    
                    <div class="stat-card ${safeStats.users_banned > 0 ? 'warning' : 'success'}">
                        <div class="stat-icon"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg></div>
                        <div class="stat-value">${safeStats.users_banned}</div>
                        <div class="stat-label">Banned Users</div>
                        <div class="stat-change">
                            ${safeStats.users_active} active
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load all charts
        async function loadCharts() {
            await loadUserRegistrationsChart();
            await loadBookingTrendsChart();
            await loadListingsCategoryChart();
            await loadRevenueChart();
        }

        // User Registrations Chart
        async function loadUserRegistrationsChart() {
            try {
                const months = document.getElementById('timeRange').value === '365' ? 12 : 6;
                const response = await fetch(`<?= url('admin/analytics/data?type=user_registrations&months=') ?>${months}`);
                const data = await response.json();
                
                const ctx = document.getElementById('userRegistrationsChart').getContext('2d');
                
                if (userRegistrationsChart) {
                    userRegistrationsChart.destroy();
                }
                
                userRegistrationsChart = new Chart(ctx, {
                    type: document.getElementById('chartType').value,
                    data: {
                        labels: data.map(item => item.month),
                        datasets: [
                            {
                                label: 'Email Registrations',
                                data: data.map(item => item.email_count || 0),
                                borderColor: '#0F172A',
                                backgroundColor: 'rgba(15, 23, 42, 0.1)',
                                borderWidth: 2,
                                tension: 0.4
                            },
                            {
                                label: 'Google Registrations',
                                data: data.map(item => item.google_count || 0),
                                borderColor: '#4285F4',
                                backgroundColor: 'rgba(66, 133, 244, 0.1)',
                                borderWidth: 2,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading user registrations chart:', error);
            }
        }

        // Booking Trends Chart
        async function loadBookingTrendsChart() {
            try {
                const days = document.getElementById('timeRange').value;
                const response = await fetch(`<?= url('admin/analytics/data?type=booking_trends&days=') ?>${days}`);
                const data = await response.json();
                
                const ctx = document.getElementById('bookingTrendsChart').getContext('2d');
                
                if (bookingTrendsChart) {
                    bookingTrendsChart.destroy();
                }
                
                bookingTrendsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.date),
                        datasets: [{
                            label: 'Bookings',
                            data: data.map(item => item.count),
                            borderColor: '#F97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            borderWidth: 2,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading booking trends chart:', error);
            }
        }

        // Listings by Category Chart
        async function loadListingsCategoryChart() {
            try {
                const response = await fetch('<?= url('admin/analytics/data?type=listings_by_category') ?>');
                const data = await response.json();
                
                const ctx = document.getElementById('listingsCategoryChart').getContext('2d');
                
                if (listingsCategoryChart) {
                    listingsCategoryChart.destroy();
                }
                
                listingsCategoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.map(item => item.category),
                        datasets: [{
                            data: data.map(item => item.count),
                            backgroundColor: [
                                '#0F172A',
                                '#475569',
                                '#F97316',
                                '#059669',
                                '#DC2626',
                                '#7C3AED',
                                '#0891B2'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading listings category chart:', error);
            }
        }

        // Revenue Chart - uses real approved booking revenue
        async function loadRevenueChart() {
            try {
                const days = document.getElementById('timeRange').value;
                const selectedChartType = document.getElementById('chartType').value;
                const response = await fetch(`<?= url('admin/analytics/data?type=revenue_trends&days=') ?>${days}`);
                const data = await response.json();
                const ctx = document.getElementById('revenueChart').getContext('2d');

                if (revenueChart) {
                    revenueChart.destroy();
                }

                revenueChart = new Chart(ctx, {
                    type: selectedChartType === 'line' ? 'line' : 'bar',
                    data: {
                        labels: data.map(item => item.label || item.date),
                        datasets: [{
                            label: 'Revenue (₱)',
                            data: data.map(item => Number(item.revenue || 0)),
                            backgroundColor: selectedChartType === 'line' ? 'rgba(5, 150, 105, 0.15)' : '#059669',
                            borderColor: '#047857',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: selectedChartType === 'line'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Revenue: ₱' + Number(context.raw || 0).toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + Number(value || 0).toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error loading revenue chart:', error);
            }
        }

        // Refresh all analytics
        function refreshAnalytics() {
            loadStats();
            loadCharts();
        }

        // Handle time range change
        document.getElementById('timeRange').addEventListener('change', refreshAnalytics);
        document.getElementById('chartType').addEventListener('change', loadCharts);
    </script>
</body>
</html>
