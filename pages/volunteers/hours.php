<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';
requireLogin();

$database = new Database();
$conn = $database->getConnection();

// Get volunteer profile
$stmt = $conn->prepare("SELECT id FROM volunteers WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$volunteer = $stmt->fetch(PDO::FETCH_ASSOC);

// Get total hours
$stmt = $conn->prepare("
    SELECT SUM(hours) as total_hours 
    FROM volunteer_hours 
    WHERE volunteer_id = ? AND status = 'approved'
");
$stmt->execute([$volunteer['id']]);
$total_hours = $stmt->fetch(PDO::FETCH_ASSOC)['total_hours'] ?? 0;

// Get all hours with event details
$stmt = $conn->prepare("
    SELECT vh.*, e.title as event_title, e.start_date as event_date
    FROM volunteer_hours vh
    LEFT JOIN events e ON vh.event_id = e.id
    WHERE vh.volunteer_id = ?
    ORDER BY vh.date DESC
");
$stmt->execute([$volunteer['id']]);
$hours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Hours - Volunteer Management System</title>
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
                        <a href="/" class="font-bold text-xl">VolunteerHub</a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="dashboard.php" class="hover:text-gray-200">Dashboard</a>
                        <a href="events.php" class="hover:text-gray-200">Events</a>
                        <a href="hours.php" class="hover:text-gray-200">Hours</a>
                        <a href="profile.php" class="hover:text-gray-200">Profile</a>
                        <a href="/logout.php" class="hover:text-gray-200">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Volunteer Hours</h1>
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>

                <!-- Stats Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Total Hours</h3>
                                <p class="text-2xl font-semibold text-gray-900"><?php echo number_format($total_hours, 1); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Approved Hours</h3>
                                <p class="text-2xl font-semibold text-gray-900">
                                    <?php 
                                        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM volunteer_hours WHERE volunteer_id = ? AND status = 'approved'");
                                        $stmt->execute([$volunteer['id']]);
                                        echo $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-hourglass-half text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Pending Hours</h3>
                                <p class="text-2xl font-semibold text-gray-900">
                                    <?php 
                                        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM volunteer_hours WHERE volunteer_id = ? AND status = 'pending'");
                                        $stmt->execute([$volunteer['id']]);
                                        echo $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hours Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($hours)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No hours logged yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($hours as $hour): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo date('M d, Y', strtotime($hour['date'])); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($hour['event_title'] ?? 'General Volunteering'); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo number_format($hour['hours'], 1); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $hour['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                                                       ($hour['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                                <?php echo ucfirst($hour['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($hour['notes'] ?? ''); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 