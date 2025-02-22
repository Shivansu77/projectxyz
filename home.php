<?php
session_start();
if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$userId = $user['id']; // Logged-in user ID

// Database Connection
$conn = new mysqli("localhost", "root", "root", "projectx");

// Check Connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Fetch all surveys (not just the ones created by the logged-in user)
$surveyQuery = "SELECT id, title FROM surveys";
$stmt = $conn->prepare($surveyQuery);
$stmt->execute();
$surveyResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <nav class="bg-gray-700 p-3 text-white font-mullish">
    <div class="flex justify-between">
        <div class="flex gap-4">
            <span class="mx-3 font-bold text-lg">Survey Dashboard</span>
            <a href="about.php" class="text-white hover:underline">About</a>
            <a href="contact.php" class="text-white hover:underline">Contact</a>
        </div>
        <div class="flex gap-4">
            <a href="formcreate.php">
                <button class="bg-white text-black rounded text-sm p-2 font-bold">Create Survey</button>
            </a>
            <a href="logout.php" class="bg-red-500 text-white rounded text-sm p-2 font-bold">Logout</a>
        </div>
    </div>
  </nav>

  <div class="flex h-screen">
    <aside class="w-72 bg-emerald-600 text-white p-4">
        <h2 class="font-bold text-lg">Available Surveys</h2>
        <?php if ($surveyResult->num_rows > 0): ?>
            <ul class="space-y-2">
                <?php while ($row = $surveyResult->fetch_assoc()): ?>
                    <li class="bg-gray-800 p-2 rounded flex justify-between items-center">
                        <span><?php echo htmlspecialchars($row['title']); ?></span>
                        <a href="take_survey.php?survey_id=<?php echo urlencode($row['id']); ?>">
                            <button class="bg-yellow-400 text-black px-3 py-1 rounded text-sm hover:bg-yellow-500">
                                Take Survey
                            </button>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-300 mt-3">No surveys available.</p>
        <?php endif; ?>
    </aside>

    <main class="flex-1 p-6 bg-gray-100">
        <h1 class="text-2xl font-bold">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
        <p class="text-gray-600">Your email: <?php echo htmlspecialchars($user['email']); ?></p>
    </main>
  </div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
