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
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-teal-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/soemone" class="font-bold text-xl">VolunteerHub</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if(isLoggedIn()): ?>
                        <a href="/soemone/pages/volunteers/dashboard.php" class="hover:text-teal-200 transition-colors duration-200">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="/soemone/pages/volunteers/donations.php" class="hover:text-teal-200 transition-colors duration-200">
                            <i class="fas fa-donate mr-1"></i> Donations
                        </a>
                        <a href="/soemone/pages/volunteers/events.php" class="hover:text-teal-200 transition-colors duration-200">
                            <i class="fas fa-calendar-alt mr-1"></i> Events
                        </a>
                        <a href="/soemone/pages/volunteers/hours.php" class="hover:text-teal-200 transition-colors duration-200">
                            <i class="fas fa-clock mr-1"></i> Hours
                        </a>
                        <?php if(isAdmin()): ?>
                            <a href="/soemone/pages/admin/dashboard.php" class="hover:text-teal-200 transition-colors duration-200">
                                <i class="fas fa-user-shield mr-1"></i> Admin
                            </a>
                        <?php endif; ?>
                        <a href="/soemone/logout.php" class="hover:text-teal-200 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="/soemone/login.php" class="px-4 py-2 rounded-md bg-teal-600 hover:bg-teal-500 transition-colors duration-200">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="/soemone/register.php" class="px-4 py-2 rounded-md bg-white text-teal-700 hover:bg-teal-50 transition-colors duration-200">
                            <i class="fas fa-user-plus mr-1"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="mb-8">
                    <img src="/soemone/assets/images/hero.jpg" alt="Volunteers working together" class="mx-auto h-64 w-auto rounded-lg shadow-lg">
                </div>
                <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Make a Difference</span>
                    <span class="block text-teal-600">Join Our Community</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Join our community of volunteers and donors making a positive impact in the world.
                </p>
                <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                        <a href="/soemone/register.php" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 md:py-4 md:text-lg md:px-10">
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-teal-600 mb-4">
                        <img src="/soemone/assets/images/volunteer.png" alt="Volunteer Management" class="h-16 w-16 mx-auto">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Volunteer Management</h3>
                    <p class="text-gray-600">Efficiently manage volunteers, track hours, and coordinate events.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-teal-600 mb-4">
                        <img src="/soemone/assets/images/donation.jpg" alt="Donation Tracking" class="h-16 w-16 mx-auto">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Donation Tracking</h3>
                    <p class="text-gray-600">Securely process and track donations, generate reports, and manage campaigns.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-teal-600 mb-4">
                        <img src="/soemone/assets/images/impact.jpg" alt="Impact Reporting" class="h-16 w-16 mx-auto">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Impact Reporting</h3>
                    <p class="text-gray-600">Measure and visualize your organization's impact with detailed analytics.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; 2024 VolunteerHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="/soemone/assets/js/main.js"></script>
</body>
</html> 