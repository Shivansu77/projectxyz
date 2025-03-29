<div class="flex h-screen">
    <div class="w-64 bg-gray-800 text-white">
        <div class="p-4 border-b border-gray-700">
            <h1 class="text-xl font-bold">Survey Admin</h1>
            <p class="text-sm text-gray-400">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
        </div>
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="admin_dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) === 'admin_dashboard.php' ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="admin_surveys.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) === 'admin_surveys.php' ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-poll mr-3"></i> Surveys
                    </a>
                </li>
                <li>
                    <a href="admin_users.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) === 'admin_users.php' ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-users mr-3"></i> Users
                    </a>
                </li>
                <li>
                    <a href="admin_responses.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) === 'admin_responses.php' ? 'bg-gray-700' : '' ?>">
                        <i class="fas fa-clipboard-check mr-3"></i> Responses
                    </a>
                </li>
                <li>
                    <a href="admin_settings.php" class="flex items-center p-2 rounded hover:bg-gray-700 <?= basename($_SERVER['PHP_SELF']) === 'admin_settings.php' ? 'bg-gray-700' : '' ?>">
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
    <div class="flex-1 overflow-auto">