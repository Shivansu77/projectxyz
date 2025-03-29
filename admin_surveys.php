<?php
session_start();
require_once 'dbConnect.php';

// Check admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Handle survey actions
if (isset($_GET['action'])) {
    try {
        switch ($_GET['action']) {
            case 'delete':
                $surveyId = (int)$_GET['id'];
                $pdo->beginTransaction();
                
                // Delete questions first (due to foreign key)
                $pdo->prepare("DELETE FROM questions WHERE survey_id = ?")->execute([$surveyId]);
                
                // Then delete survey
                $stmt = $pdo->prepare("DELETE FROM surveys WHERE id = ?");
                $stmt->execute([$surveyId]);
                
                $pdo->commit();
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Survey deleted successfully'];
                break;
                
            case 'toggle_status':
                $surveyId = (int)$_GET['id'];
                $stmt = $pdo->prepare("UPDATE surveys SET status = IF(status='active','inactive','active') WHERE id = ?");
                $stmt->execute([$surveyId]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Survey status updated'];
                break;
        }
        
        header('Location: admin_surveys.php');
        exit;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ' . $e->getMessage()];
        header('Location: admin_surveys.php');
        exit;
    }
}

// Get all surveys with creator info
try {
    $surveys = $pdo->query("
        SELECT s.*, u.name as creator_name, 
               (SELECT COUNT(*) FROM questions WHERE survey_id = s.id) as question_count,
               (SELECT COUNT(*) FROM responses WHERE survey_id = s.id) as response_count
        FROM surveys s
        LEFT JOIN users u ON s.user_id = u.id
        ORDER BY s.created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error loading surveys: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center p-4">
                <h2 class="text-xl font-semibold">Survey Management</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"><?= date('l, F j, Y') ?></span>
                </div>
            </div>
        </header>

        <main class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Survey Management</h2>
                <a href="admin_survey_create.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i> Create New Survey
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Questions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($surveys as $survey): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($survey['title']) ?></div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs"><?= htmlspecialchars($survey['description'] ?? 'No description') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($survey['creator_name'] ?? 'System') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $survey['question_count'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $survey['response_count'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $survey['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                            <?= ucfirst($survey['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M j, Y', strtotime($survey['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="admin_survey_view.php?id=<?= $survey['id'] ?>" class="text-blue-600 hover:text-blue-900" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="admin_survey_edit.php?id=<?= $survey['id'] ?>" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?action=toggle_status&id=<?= $survey['id'] ?>" class="text-indigo-600 hover:text-indigo-900" title="Toggle Status">
                                                <i class="fas fa-toggle-<?= $survey['status'] === 'active' ? 'on' : 'off' ?>"></i>
                                            </a>
                                            <a href="?action=delete&id=<?= $survey['id'] ?>" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
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