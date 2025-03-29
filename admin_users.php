<?php
session_start();
require_once 'dbConnect.php';

// Check admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Handle user actions
if (isset($_GET['action'])) {
    try {
        $userId = (int)$_GET['id'];
        
        switch ($_GET['action']) {
            case 'delete':
                // Prevent deleting own account
                if ($userId === $_SESSION['user']['id']) {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'You cannot delete your own account'];
                    break;
                }
                
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'User deleted successfully'];
                break;
                
            case 'toggle_status':
                $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active','suspended','active') WHERE id = ?");
                $stmt->execute([$userId]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'User status updated'];
                break;
                
            case 'make_admin':
                $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
                $stmt->execute([$userId]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'User promoted to admin'];
                break;
                
            case 'revoke_admin':
                // Prevent revoking own admin
                if ($userId === $_SESSION['user']['id']) {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'You cannot revoke your own admin privileges'];
                    break;
                }
                
                $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
                $stmt->execute([$userId]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Admin privileges revoked'];
                break;
        }
        
        header('Location: admin_users.php');
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ' . $e->getMessage()];
        header('Location: admin_users.php');
        exit;
    }
}

// Get all users
try {
    $users = $pdo->query("
        SELECT u.*, 
               (SELECT COUNT(*) FROM surveys WHERE user_id = u.id) as survey_count,
               (SELECT COUNT(*) FROM responses WHERE user_id = u.id) as response_count
        FROM users u
        ORDER BY u.created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error loading users: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center p-4">
                <h2 class="text-xl font-semibold">User Management</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"><?= date('l, F j, Y') ?></span>
                </div>
            </div>
        </header>

        <main class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">User Management</h2>
                <a href="admin_user_create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-user-plus mr-2"></i> Add New User
                </a>
            </div>

            <!-- Message Alert -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="mb-4 p-4 rounded-lg <?= $_SESSION['message']['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= $_SESSION['message']['text'] ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surveys</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">
                                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900"><?= htmlspecialchars($user['name']) ?></div>
                                                <div class="text-sm text-gray-500">Last login: <?= $user['last_login'] ? date('M j, Y', strtotime($user['last_login'])) : 'Never' ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $user['survey_count'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $user['response_count'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="admin_user_edit.php?id=<?= $user['id'] ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=toggle_status&id=<?= $user['id'] ?>" class="text-indigo-600 hover:text-indigo-900" title="Toggle Status">
                                                <i class="fas fa-toggle-<?= $user['status'] === 'active' ? 'on' : 'off' ?>"></i>
                                            </a>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                                    <a href="?action=revoke_admin&id=<?= $user['id'] ?>" class="text-purple-600 hover:text-purple-900" title="Revoke Admin" onclick="return confirm('Revoke admin privileges?')">
                                                        <i class="fas fa-user-shield"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <a href="?action=make_admin&id=<?= $user['id'] ?>" class="text-purple-600 hover:text-purple-900" title="Make Admin" onclick="return confirm('Grant admin privileges?')">
                                                    <i class="fas fa-user-cog"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                                <a href="?action=delete&id=<?= $user['id'] ?>" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Delete this user permanently?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>