<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$host = "localhost";
$port = 8889; // Change if using a different port
$dbname = "projectx";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read incoming JSON data
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['title']) || !isset($data['description']) || !isset($data['questions'])) {
        echo json_encode(["message" => "Invalid data received"]);
        exit;
    }

    // Insert survey
    $stmt = $pdo->prepare("INSERT INTO surveys (title, description) VALUES (:title, :description)");
    $stmt->execute(["title" => $data['title'], "description" => $data['description']]);
    $survey_id = $pdo->lastInsertId();

    // Insert questions
    $stmt = $pdo->prepare("INSERT INTO questions (survey_id, question_text) VALUES (:survey_id, :question_text)");
    foreach ($data['questions'] as $question) {
        $stmt->execute(["survey_id" => $survey_id, "question_text" => $question]);
    }

    echo json_encode(["message" => "Survey created successfully!", "survey_id" => $survey_id]);
} catch (PDOException $e) {
    echo json_encode(["message" => "Database Error: " . $e->getMessage()]);
}
?>
