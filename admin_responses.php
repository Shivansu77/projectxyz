<?php
session_start();
require_once 'dbConnect.php';

// Check admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Handle response deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    try {
        $responseId = (int)$_GET['id'];
        $pdo->beginTransaction();
        
        // Delete response answers first
        $pdo->prepare("DELETE FROM response_answers WHERE response_id = ?")->execute([$responseId]);
        
        // Then delete response
        $stmt = $pdo->prepare("DELETE FROM responses WHERE id = ?");
        $stmt->execute([$responseId]);
        
        $pdo->commit();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Response deleted successfully'];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Error: ' . $e->getMessage()];
    }
    
    header('Location: admin_responses.php');
    exit;
}

// Get all responses with related data
try {
    $responses = $pdo->query("
        SELECT r.*, s.title as survey_title, u.name as user_name, u.email as user_email,
               (SELECT COUNT(*) FROM response_answers WHERE response_id = r.id) as answer_count
        FROM responses r
        JOIN surveys s ON r.survey_id = s.id
        LEFT JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error loading responses: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Responses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center p-4">
                <h2 class="text-xl font-semibold">Survey Responses</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"><?= date('l, F j, Y') ?></span>
                </div>
            </div>
        </header>

        <main class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Survey Responses</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select class="block appearance-none bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-blue-500">
                            <option>All Surveys</option>
                            <?php 
                            $surveys = $pdo->query("SELECT id, title FROM surveys ORDER BY title")->fetchAll();
                            foreach ($surveys as $survey): ?>
                                <option value="<?= $survey['id'] ?>"><?= htmlspecialchars($survey['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <input type="text" placeholder="Search..." class="border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500">
                </div>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Survey</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Respondent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Answers</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($responses as $response): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($response['survey_title']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($response['user_name']): ?>
                                            <div class="font-medium"><?= htmlspecialchars($response['user_name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($response['user_email']) ?></div>
                                        <?php else: ?>
                                            <span class="text-gray-500">Anonymous</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $response['answer_count'] ?> questions answered
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M j, Y g:i a', strtotime($response['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="admin_response_view.php?id=<?= $response['id'] ?>" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?action=delete&id=<?= $response['id'] ?>" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Delete this response?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium"><?= count($responses) ?></span> results
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 border rounded text-gray-600 bg-white hover:bg-gray-50">
                                Previous
                            </button>
                            <button class="px-3 py-1 border rounded text-gray-600 bg-white hover:bg-gray-50">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>