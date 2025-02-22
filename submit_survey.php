<?php
ob_start(); // Start output buffering
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    die("Unauthorized access.");
}

$user = $_SESSION['user'];

if (!isset($user['id'])) {
    die("❌ User ID not found. Please log in again.");
}

$userId = intval($user['id']); // Ensure integer type

$conn = new mysqli("localhost", "root", "root", "projectx");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Validate POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['survey_id']) || empty($_POST['answers'])) {
    die("❌ Invalid request.");
}

$surveyId = intval($_POST['survey_id']);
$answers = $_POST['answers'];

// Prepare statement for inserting responses
$stmt = $conn->prepare("INSERT INTO responses (survey_id, question_id, user_id, answer, created_at) VALUES (?, ?, ?, ?, NOW())");

if (!$stmt) {
    die("❌ SQL prepare failed: " . $conn->error);
}

foreach ($answers as $questionId => $answerText) {
    if (!is_numeric($questionId) || empty(trim($answerText))) {
        continue; // Skip invalid or empty answers
    }

    $questionId = intval($questionId);
    $stmt->bind_param("iiis", $surveyId, $questionId, $userId, $answerText);

    if (!$stmt->execute()) {
        die("❌ Execution failed: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();

// Redirect after submission
header("Location: home.php?success=1");
exit();

ob_end_flush(); // Flush output buffer
?>
