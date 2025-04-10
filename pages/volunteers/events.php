<?php
require_once '../../includes/auth.php';
require_once '../../config/database.php';
requireLogin();

$database = new Database();
$conn = $database->getConnection();

// Handle event deletion
if (isset($_POST['delete_event']) && isset($_POST['event_id'])) {
    try {
        // First check if the current user is the creator of the event
        $stmt = $conn->prepare("SELECT created_by FROM events WHERE id = :id");
        $stmt->execute([':id' => $_POST['event_id']]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event && $event['created_by'] == $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM events WHERE id = :id");
            $stmt->execute([':id' => $_POST['event_id']]);
            $success_message = 'Event deleted successfully!';
        } else {
            $error_message = 'You do not have permission to delete this event.';
        }
    } catch (PDOException $e) {
        $error_message = 'Error deleting event: ' . $e->getMessage();
    }
}

// Handle event registration
if (isset($_POST['register_event']) && isset($_POST['event_id'])) {
    try {
        // Check if already registered
        $stmt = $conn->prepare("SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$_POST['event_id'], $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error_message = 'You are already registered for this event.';
        } else {
            // Check if event is open and has available slots
            $stmt = $conn->prepare("
                SELECT e.max_volunteers, COUNT(er.id) as current_registrations 
                FROM events e 
                LEFT JOIN event_registrations er ON e.id = er.event_id 
                WHERE e.id = ? AND e.status = 'Open'
                GROUP BY e.id
            ");
            $stmt->execute([$_POST['event_id']]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$event) {
                $error_message = 'Event is not available for registration.';
            } elseif ($event['current_registrations'] >= $event['max_volunteers']) {
                $error_message = 'Event is full. No more volunteers can be registered.';
            } else {
                // Register for event
                $stmt = $conn->prepare("INSERT INTO event_registrations (event_id, user_id, status) VALUES (?, ?, 'Registered')");
                if ($stmt->execute([$_POST['event_id'], $_SESSION['user_id']])) {
                    $success_message = 'Successfully registered for the event!';
                } else {
                    $error_message = 'Failed to register for the event. Please try again.';
                }
            }
        }
    } catch (PDOException $e) {
        $error_message = 'Error registering for event: ' . $e->getMessage();
    }
}

// Get all events with creator information and registration status
$stmt = $conn->prepare("
    SELECT e.*, u.full_name as creator_name,
           (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id) as registered_count,
           (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id AND er.user_id = ?) as is_registered
    FROM events e
    JOIN users u ON e.created_by = u.id
    ORDER BY e.date_time ASC
");
$stmt->execute([$_SESSION['user_id']]);
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
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            position: relative;
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/soemone" class="font-bold text-xl text-gray-800">
                            <i class="fas fa-hands-helping mr-2"></i>VolunteerHub
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/soemone/pages/volunteers/dashboard.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        <a href="/soemone/pages/volunteers/events.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-calendar-alt mr-1"></i>Events
                        </a>
                        <a href="/soemone/pages/volunteers/donations.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-hand-holding-usd mr-1"></i>Donations
                        </a>
                        <a href="/soemone/pages/volunteers/volunteers.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-users mr-1"></i>Volunteers
                        </a>
                        <a href="/soemone/pages/volunteers/profile.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-user mr-1"></i>Profile
                        </a>
                        <a href="/soemone/logout.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 text-white">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                        Volunteer Events
                    </h1>
                    <p class="mt-6 max-w-2xl mx-auto text-xl text-green-100">
                        Join our community events and make a difference in people's lives.
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <?php if (isset($success_message)): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Search and Filter Section -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700">Search Events</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search" class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search by title, location...">
                        </div>
                    </div>
                    <div class="flex-1">
                        <label for="status" class="block text-sm font-medium text-gray-700">Filter by Status</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-md">
                            <option value="all">All Events</option>
                            <option value="open">Open Events</option>
                            <option value="closed">Closed Events</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <a href="/soemone/pages/volunteers/add_event.php" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 hover:from-green-800 hover:via-emerald-800 hover:to-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <i class="fas fa-plus mr-2"></i>Add Event
                        </a>
                    </div>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($events as $event): ?>
                <div class="bg-white overflow-hidden shadow-lg rounded-lg transform transition duration-300 hover:scale-105">
                    <!-- Event Image -->
                    <div class="h-48 bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-6xl"></i>
                    </div>
                    
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $event['status'] === 'Open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </div>
                        
                        <div class="mt-4 space-y-3">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="far fa-calendar-alt mr-2 text-emerald-500"></i>
                                <?php echo date('M d, Y', strtotime($event['date_time'])); ?>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="far fa-clock mr-2 text-emerald-500"></i>
                                <?php echo date('h:i A', strtotime($event['date_time'])); ?>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-2 text-emerald-500"></i>
                                <?php echo htmlspecialchars($event['location']); ?>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-users mr-2 text-emerald-500"></i>
                                <?php echo $event['registered_count']; ?>/<?php echo htmlspecialchars($event['max_volunteers']); ?> Volunteers
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-user mr-2 text-emerald-500"></i>
                                Created by: <?php echo htmlspecialchars($event['creator_name']); ?>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between items-center">
                            <button onclick="showEventDetails(<?php echo htmlspecialchars(json_encode($event)); ?>)" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 hover:from-green-800 hover:via-emerald-800 hover:to-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                View Details
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                            <?php if ($event['created_by'] == $_SESSION['user_id']): ?>
                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="delete_event" class="inline-flex items-center px-4 py-2 border border-red-600 text-sm font-medium rounded-md text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete
                                    <i class="fas fa-trash-alt ml-2"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- No Events Message -->
            <?php if (empty($events)): ?>
            <div class="text-center py-12 bg-white rounded-lg shadow-lg">
                <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No Events Available</h3>
                <p class="mt-2 text-sm text-gray-500">
                    There are currently no events scheduled. Please check back later.
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Event Details Modal -->
        <div id="eventModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="eventDetails"></div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 text-white shadow mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-green-100 text-sm">
                    &copy; <?php echo date('Y'); ?> VolunteerHub. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <script>
        // Get the modal
        const modal = document.getElementById("eventModal");
        const closeBtn = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        function showEventDetails(event) {
            const modal = document.getElementById("eventModal");
            const detailsDiv = document.getElementById("eventDetails");
            
            // Format the event details
            const details = `
                <h2 class="text-2xl font-bold text-gray-900 mb-4">${event.title}</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="far fa-calendar-alt text-emerald-500 mr-2"></i>
                        <span>Date: ${new Date(event.date_time).toLocaleDateString()}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="far fa-clock text-emerald-500 mr-2"></i>
                        <span>Time: ${new Date(event.date_time).toLocaleTimeString()}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-emerald-500 mr-2"></i>
                        <span>Location: ${event.location}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-users text-emerald-500 mr-2"></i>
                        <span>Volunteers: ${event.registered_count}/${event.max_volunteers}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user text-emerald-500 mr-2"></i>
                        <span>Created by: ${event.creator_name}</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600">${event.description || 'No description provided.'}</p>
                    </div>
                    <div class="mt-6 flex justify-between">
                        ${event.status === 'Open' && !event.is_registered ? `
                            <form method="POST" class="inline">
                                <input type="hidden" name="event_id" value="${event.id}">
                                <button type="submit" name="register_event" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-green-700 via-emerald-700 to-teal-700 hover:from-green-800 hover:via-emerald-800 hover:to-teal-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                    Participate
                                    <i class="fas fa-user-plus ml-2"></i>
                                </button>
                            </form>
                        ` : ''}
                        ${event.is_registered ? `
                            <span class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100">
                                Already Registered
                                <i class="fas fa-check ml-2"></i>
                            </span>
                        ` : ''}
                    </div>
                </div>
            `;
            
            detailsDiv.innerHTML = details;
            modal.style.display = "block";
        }

        // Search and filter functionality
        document.getElementById('search').addEventListener('input', filterEvents);
        document.getElementById('status').addEventListener('change', filterEvents);

        function filterEvents() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const statusFilter = document.getElementById('status').value;
            const eventCards = document.querySelectorAll('.grid > div');

            eventCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const location = card.querySelector('.fa-map-marker-alt').nextSibling.textContent.toLowerCase();
                const status = card.querySelector('span').textContent.toLowerCase();
                
                const matchesSearch = title.includes(searchTerm) || location.includes(searchTerm);
                const matchesStatus = statusFilter === 'all' || 
                                    (statusFilter === 'open' && status === 'open') ||
                                    (statusFilter === 'closed' && status === 'closed');

                card.style.display = matchesSearch && matchesStatus ? 'block' : 'none';
            });
        }
    </script>
</body>
</html> 