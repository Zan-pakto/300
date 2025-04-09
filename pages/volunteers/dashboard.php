<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';
requireLogin();

$database = new Database();
$conn = $database->getConnection();

// Get current user's profile
$stmt = $conn->prepare("
    SELECT * FROM users 
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get upcoming events
$stmt = $conn->prepare("
    SELECT * FROM events 
    WHERE status = 'Open' 
    AND start_date >= CURDATE()
    ORDER BY start_date ASC, start_time ASC 
    LIMIT 5
");
$stmt->execute();
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent donations
$stmt = $conn->prepare("
    SELECT * FROM donations 
    WHERE user_id = ? 
    ORDER BY donation_date DESC 
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recentDonations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/soemone" class="font-bold text-xl">VolunteerHub</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/soemone/pages/volunteers/dashboard.php" class="hover:text-gray-200">Dashboard</a>
                        <a href="/soemone/pages/volunteers/events.php" class="hover:text-gray-200">Events</a>
                        <a href="/soemone/pages/volunteers/donations.php" class="hover:text-gray-200">Donations</a>
                        <a href="/soemone/pages/volunteers/profile.php" class="hover:text-gray-200">Profile</a>
                        <a href="/soemone/logout.php" class="hover:text-gray-200">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!
                </h1>
                <p class="mt-2 text-gray-600">
                    Here's your volunteer dashboard where you can manage your profile, view upcoming events, and track your donations.
                </p>
            </div>

            <!-- Stats Section -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-calendar text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Upcoming Events</h3>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo count($upcomingEvents); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-hand-holding-usd text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Total Donations</h3>
                            <p class="text-2xl font-semibold text-gray-900"><?php 
                                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM donations WHERE user_id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                echo $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-user text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Account Type</h3>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo ucfirst($user['role']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events Section -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Upcoming Events</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($upcomingEvents as $event): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['title']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($event['start_date'])); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo date('h:i A', strtotime($event['start_time'])) . ' - ' . date('h:i A', strtotime($event['end_time'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($event['location']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $event['status'] === 'Open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Donations Section -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Donations</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recentDonations as $donation): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo date('M d, Y', strtotime($donation['donation_date'])); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">$<?php echo number_format($donation['amount'], 2); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($donation['type']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $donation['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                                               ($donation['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo ucfirst($donation['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 