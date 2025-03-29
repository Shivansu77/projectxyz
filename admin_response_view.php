<?php
session_start();
require_once 'dbConnect.php';

// Check admin privileges
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get response ID
$responseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Get response details
    $stmt = $pdo->prepare("
        SELECT r.*, s.title as survey_title, u.name as user_name, u.email as user_email
        FROM responses r
        JOIN surveys s ON r.survey_id = s.id
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.id = ?
    ");
    $stmt->execute([$responseId]);
    $response = $stmt->fetch();

    if (!$response) {
        throw new Exception("Response not found");
    }

    // Get all answers for this response
    $stmt = $pdo->prepare("
        SELECT ra.*, q.question_text
        FROM response_answers ra
        JOIN questions q ON ra.question_id = q.id
        WHERE ra.response_id = ?
        ORDER BY ra.id
    ");
    $stmt->execute([$responseId]);
    $answers = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Response Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex justify-between items-center p-4">
                <h2 class="text-xl font-semibold">Response Details</h2>
                <a href="admin_responses.php" class="text-blue-600 hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Responses
                </a>
            </div>
        </header>

        <main class="p-6">
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold">Survey: <?= htmlspecialchars($response['survey_title']) ?></h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Respondent</p>
                            <p class="font-medium">
                                <?= $response['user_name'] ? htmlspecialchars($response['user_name']) : 'Anonymous' ?>
                            </p>
                            <?php if ($response['user_email']): ?>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($response['user_email']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Submitted On</p>
                            <p class="font-medium">
                                <?= date('F j, Y g:i a', strtotime($response['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold">Answers</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (count($answers) > 0): ?>
                        <?php foreach ($answers as $answer): ?>
                            <div class="p-4">
                                <h4 class="font-medium text-gray-800 mb-2">
                                    <?= htmlspecialchars($answer['question_text']) ?>
                                </h4>
                                <p class="text-gray-600"><?= htmlspecialchars($answer['answer_text']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-gray-500">
                            No answers found for this response
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>