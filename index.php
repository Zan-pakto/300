<?php
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/soemone" class="font-bold text-xl">
                        <i class="fas fa-hands-helping mr-2"></i>VolunteerHub
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if(isLoggedIn()): ?>
                        <a href="/soemone/pages/volunteers/dashboard.php" class="hover:text-green-100 transition-colors duration-200">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                        <a href="/soemone/pages/volunteers/events.php" class="hover:text-green-100 transition-colors duration-200">
                            <i class="fas fa-calendar-alt mr-1"></i> Events
                        </a>
                        <a href="/soemone/pages/volunteers/donations.php" class="hover:text-green-100 transition-colors duration-200">
                            <i class="fas fa-hand-holding-usd mr-1"></i> Donations
                        </a>
                        <a href="/soemone/pages/volunteers/hours.php" class="hover:text-green-100 transition-colors duration-200">
                            <i class="fas fa-clock mr-1"></i> Hours
                        </a>
                        <a href="/soemone/pages/volunteers/volunteers.php" class="hover:text-green-100 transition-colors duration-200">
                            <i class="fas fa-users mr-1"></i> Volunteers
                        </a>
                        <?php if(isAdmin()): ?>
                            <a href="/soemone/pages/admin/dashboard.php" class="hover:text-green-100 transition-colors duration-200">
                                <i class="fas fa-user-shield mr-1"></i> Admin
                            </a>
                        <?php endif; ?>
                        <a href="/soemone/logout.php" class="hover:text-green-100 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="/soemone/login.php" class="px-4 py-2 rounded-md bg-green-600 hover:bg-green-500 transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="/soemone/register.php" class="px-4 py-2 rounded-md bg-white text-green-700 hover:bg-green-50 transition-colors duration-200">
                            <i class="fas fa-user-plus mr-1"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 text-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="mb-8">
                    <img src="/soemone/assets/images/hero.jpg" alt="Volunteers working together" class="mx-auto h-64 w-auto rounded-lg shadow-xl transform hover:scale-105 transition-transform duration-300">
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
                    <span class="block">Make a Difference</span>
                    <span class="block text-green-100">Join Our Community</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-green-100 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Join our community of volunteers and donors making a positive impact in the world.
                </p>
                <?php if(!isLoggedIn()): ?>
                <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                        <a href="/soemone/register.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 md:py-4 md:text-lg md:px-10 transform hover:scale-105 transition-transform duration-300">
                            <i class="fas fa-hands-helping mr-2"></i> Get Started
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center p-3 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-emerald-600">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">Volunteer Management</h3>
                    <p class="text-gray-600 text-center">Efficiently manage volunteers, track hours, and coordinate events.</p>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center p-3 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-emerald-600">
                            <i class="fas fa-hand-holding-usd text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">Donation Tracking</h3>
                    <p class="text-gray-600 text-center">Securely process and track donations, generate reports, and manage campaigns.</p>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center p-3 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-emerald-600">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4 text-center">Impact Reporting</h3>
                    <p class="text-gray-600 text-center">Measure and visualize your organization's impact with detailed analytics.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 text-white shadow-lg mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-green-100">&copy; <?php echo date('Y'); ?> VolunteerHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="/soemone/assets/js/main.js"></script>
</body>
</html> 
</html> 