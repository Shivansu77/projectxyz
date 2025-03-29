<?php
session_start();
require_once 'dbConnect.php';

// Check admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Get statistics
try {
    // Survey statistics
    $surveys = $pdo->query("SELECT COUNT(*) FROM surveys")->fetchColumn();
    $activeSurveys = $pdo->query("SELECT COUNT(*) FROM surveys WHERE status = 'active'")->fetchColumn();
    
    // User statistics
    $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE last_login > DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
    
    // Response statistics
    $responses = $pdo->query("SELECT COUNT(*) FROM responses")->fetchColumn();
    $todayResponses = $pdo->query("SELECT COUNT(*) FROM responses WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    
    // Recent activity
    $recentSurveys = $pdo->query("SELECT id, title, created_at FROM surveys ORDER BY created_at DESC LIMIT 5")->fetchAll();
    $recentResponses = $pdo->query("
        SELECT r.id, s.title, u.email, r.created_at 
        FROM responses r
        JOIN surveys s ON r.survey_id = s.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC LIMIT 5
    ")->fetchAll();

} catch (PDOException $e) {
    die("Error loading dashboard data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Sidebar Navigation -->
    <div class="flex h-screen">
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">Survey Admin</h1>
                <p class="text-sm text-gray-400">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="admin_dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700 bg-gray-700">
                            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="admin_surveys.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-poll mr-3"></i> Surveys
                        </a>
                    </li>
                    <li>
                        <a href="admin_users.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-users mr-3"></i> Users
                        </a>
                    </li>
                    <li>
                        <a href="admin_responses.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-clipboard-check mr-3"></i> Responses
                        </a>
                    </li>
                    <li>
                        <a href="admin_settings.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-cog mr-3"></i> Settings
                        </a>
                    </li>
                    <li class="pt-4 mt-4 border-t border-gray-700">
                        <a href="logout.php" class="flex items-center p-2 rounded hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt mr-3"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center p-4">
                    <h2 class="text-xl font-semibold">Dashboard Overview</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600"><?= date('l, F j, Y') ?></span>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Total Surveys</p>
                                <h3 class="text-2xl font-bold"><?= $surveys ?></h3>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-poll text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Active Surveys</p>
                                <h3 class="text-2xl font-bold"><?= $activeSurveys ?></h3>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Total Users</p>
                                <h3 class="text-2xl font-bold"><?= $users ?></h3>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full">
                                <i class="fas fa-users text-purple-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500">Today's Responses</p>
                                <h3 class="text-2xl font-bold"><?= $todayResponses ?></h3>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-clipboard-check text-yellow-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Surveys -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b">
                            <h3 class="font-semibold text-lg">Recent Surveys</h3>
                        </div>
                        <div class="divide-y">
                            <?php foreach ($recentSurveys as $survey): ?>
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-medium"><?= htmlspecialchars($survey['title']) ?></h4>
                                            <p class="text-sm text-gray-500">Created <?= date('M j, Y', strtotime($survey['created_at'])) ?></p>
                                        </div>
                                        <a href="admin_survey_view.php?id=<?= $survey['id'] ?>" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="p-4 border-t text-center">
                            <a href="admin_surveys.php" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                View All Surveys <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Responses -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="p-4 border-b">
                            <h3 class="font-semibold text-lg">Recent Responses</h3>
                        </div>
                        <div class="divide-y">
                            <?php foreach ($recentResponses as $response): ?>
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-medium"><?= htmlspecialchars($response['title']) ?></h4>
                                            <p class="text-sm text-gray-500">By <?= htmlspecialchars($response['email']) ?></p>
                                        </div>
                                        <span class="text-sm text-gray-500"><?= date('M j, g:i a', strtotime($response['created_at'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="p-4 border-t text-center">
                            <a href="admin_responses.php" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                View All Responses <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>