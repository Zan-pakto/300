<?php
require_once '../../config/database.php';
require_once '../../includes/auth.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: /soemone/login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $type = sanitize_input($_POST['type']);
    $description = sanitize_input($_POST['description']);
    
    if (empty($amount) || empty($type)) {
        $error = "Amount and type are required";
    } elseif ($amount <= 0) {
        $error = "Amount must be greater than 0";
    } else {
        $stmt = $conn->prepare("INSERT INTO donations (user_id, amount, type, description, status) VALUES (?, ?, ?, ?, 'Pending')");
        if ($stmt->execute([$_SESSION['user_id'], $amount, $type, $description])) {
            header('Location: donations.php');
            exit();
        } else {
            $error = "Failed to submit donation. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Donation - VolunteerHub</title>
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
                        <a href="/soemone/pages/volunteers/donations.php" class="border-teal-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-donate mr-2"></i> Donations
                        </a>
                        <a href="/soemone/pages/volunteers/events.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">New Donation</h2>
                    <p class="mt-1 text-sm text-gray-500">Submit a new donation to support our cause.</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-6" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span><?php echo $error; ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="new_donation.php" class="space-y-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" name="amount" id="amount" required
                                class="pl-7 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md">
                            <option value="">Select a type</option>
                            <option value="Monetary">Monetary</option>
                            <option value="In-Kind">In-Kind</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                            placeholder="Add any additional details about your donation"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="donations.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            <i class="fas fa-check mr-2"></i> Submit Donation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 