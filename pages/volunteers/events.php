<?php
// events.php

// Include necessary files or configurations
require_once '../../config/database.php';
require_once '../../includes/auth.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: /soemone/login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Fetch events
$stmt = $conn->prepare("SELECT * FROM events ORDER BY date ASC");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - VolunteerHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/soemone" class="text-xl font-bold text-teal-600">VolunteerHub</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="/soemone/pages/volunteers/dashboard.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </a>
                        <a href="/soemone/pages/volunteers/donations.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-donate mr-2"></i> Donations
                        </a>
                        <a href="/soemone/pages/volunteers/events.php" class="border-teal-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-calendar-alt mr-2"></i> Events
                        </a>
                        <a href="/soemone/pages/volunteers/hours.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-clock mr-2"></i> Hours
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-gray-700 mr-4"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="/soemone/logout.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Upcoming Events</h2>
                    <a href="/soemone/pages/volunteers/register_event.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                        <i class="fas fa-plus mr-2"></i> Register for Event
                    </a>
                </div>

                <?php if (empty($events)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">No upcoming events</h3>
                        <p class="mt-1 text-sm text-gray-500">Check back later for new events.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($events as $event): ?>
                            <div class="bg-white border rounded-lg shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $event['status'] === 'Open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo htmlspecialchars($event['status']); ?>
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-calendar-day mr-2"></i>
                                            <?php echo date('F j, Y', strtotime($event['date'])); ?>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500 mt-2">
                                            <i class="fas fa-clock mr-2"></i>
                                            <?php echo date('g:i A', strtotime($event['start_time'])); ?> - <?php echo date('g:i A', strtotime($event['end_time'])); ?>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-500 mt-2">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($event['description']); ?></p>
                                    </div>
                                    <div class="mt-6">
                                        <a href="/soemone/pages/volunteers/register_event.php?id=<?php echo $event['id']; ?>" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                                            <i class="fas fa-user-plus mr-2"></i> Register
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>