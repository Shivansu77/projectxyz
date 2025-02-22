<?php
session_start();
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id'];

// Database Connection
$conn = new mysqli("localhost", "root", "root", "projectx");

// Check Connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Get survey_id from URL and validate
if (!isset($_GET['survey_id']) || empty($_GET['survey_id']) || !is_numeric($_GET['survey_id'])) {
    header("Location: home.php?error=invalid_survey");
    exit();
}
$surveyId = intval($_GET['survey_id']);

// Fetch survey title
$surveyQuery = "SELECT title FROM surveys WHERE id = ?";
$stmt = $conn->prepare($surveyQuery);
$stmt->bind_param("i", $surveyId);
$stmt->execute();
$surveyResult = $stmt->get_result();

if ($surveyResult->num_rows === 0) {
    header("Location: home.php?error=survey_not_found");
    exit();
}
$survey = $surveyResult->fetch_assoc();

// Fetch questions related to this survey
$questionQuery = "SELECT id, question_text FROM questions WHERE survey_id = ?";
$stmt = $conn->prepare($questionQuery);
$stmt->bind_param("i", $surveyId);
$stmt->execute();
$questionResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Survey - <?php echo htmlspecialchars($survey['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <nav class="bg-gray-700 p-4 text-white font-mullish flex justify-between">
      <span class="font-bold text-lg">Survey: <?php echo htmlspecialchars($survey['title']); ?></span>
      <a href="home.php" class="text-white underline">Back to Home</a>
  </nav>

  <div class="max-w-3xl mx-auto p-6 bg-white shadow-lg mt-6 rounded-lg">
    <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($survey['title']); ?></h1>

    <?php if ($questionResult->num_rows > 0): ?>
        <form action="submit_survey.php" method="POST" class="space-y-4">
            <input type="hidden" name="survey_id" value="<?php echo $surveyId; ?>">

            <?php while ($question = $questionResult->fetch_assoc()): ?>
                <div>
                    <label class="block font-semibold mb-1">
                        <?php echo htmlspecialchars($question['question_text']); ?>
                    </label>
                    <input type="text" name="answers[<?php echo $question['id']; ?>]" class="border p-2 w-full rounded" required>
                </div>
            <?php endwhile; ?>

            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Submit Survey
                </button>
                <a href="home.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    <?php else: ?>
        <p class="text-red-500 font-bold">No questions available for this survey.</p>
        <a href="home.php" class="block text-blue-500 mt-4">Go Back</a>
    <?php endif; ?>
  </div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
