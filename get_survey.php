<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$host = "localhost";
$user = "root"; // MAMP default username
$pass = "root"; // MAMP default password
$dbname = "projectx"; // <-- Corrected database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$survey_id = isset($_GET['survey_id']) ? intval($_GET['survey_id']) : 0;
if (!$survey_id) {
    die(json_encode(["error" => "Survey ID missing"]));
}

$sql = "SELECT * FROM surveys WHERE id = $survey_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $survey = $result->fetch_assoc();
    $questions = [];

    $qsql = "SELECT * FROM questions WHERE survey_id = $survey_id";
    $qresult = $conn->query($qsql);
    while ($row = $qresult->fetch_assoc()) {
        $questions[] = $row;
    }

    echo json_encode(["title" => $survey['title'], "questions" => $questions]);
} else {
    echo json_encode(["error" => "Survey not found"]);
}
?>
