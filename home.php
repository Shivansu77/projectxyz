<?php
session_start();
require_once 'dbConnect.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['user'];

try {
    // Get all surveys
    $stmt = $pdo->query("SELECT id, title FROM surveys");
    $surveys = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Error loading surveys. Please try again later.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-stone-50">
    <nav class="bg-white p-4 text-gray-800 font-sans shadow-md">
        <div class="flex justify-between items-center">
            <div class="flex gap-6 items-center">
                <span class="mx-3 font-extrabold text-xl">Survey Dashboard</span>
                <a href="about.php" class="hover:underline hover:text-gray-500 transition">About</a>
                <a href="contact.php" class="hover:underline hover:text-gray-500 transition">Contact</a>
            </div>
            <div class="flex gap-6">
                <a href="formcreate.php">
                    <button class="bg-gray-500 hover:bg-gray-900 text-white rounded-lg text-sm p-3 font-semibold transition shadow-md">
                        Create Survey
                    </button>
                </a>
                <a href="logout.php">
                    <button class="bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm p-3 font-semibold transition shadow-md">
                        Logout
                    </button>
                </a>
            </div>
        </div>
    </nav>

    <div class="py-5 bg-purple-50 border border-purple-500 rounded-lg shadow-sm text-center font-semibold text-purple-700">
        Welcome back, <?= htmlspecialchars($user['name']) ?>!
    </div>

    <div class="flex h-screen">
        <aside class="w-64 bg-orange-50 text-black p-6 border border-orange-500">
            <h2 class="font-extrabold text-lg mb-4">Available Surveys</h2>
            <?php if (!empty($surveys)): ?>
                <ul class="space-y-4">
                    <?php foreach ($surveys as $survey): ?>
                        <li class="bg-purple-200 p-3 rounded-lg shadow-sm flex justify-between items-center text-black font-sans">
                            <span><?= htmlspecialchars($survey['title']) ?></span>
                            <a href="take_survey.php?survey_id=<?= $survey['id'] ?>">
                                <button class="bg-amber-400 hover:bg-yellow-500 text-black px-4 py-2 rounded-lg text-sm shadow-lg transition">
                                    Take Survey
                                </button>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500 mt-3">No surveys available.</p>
            <?php endif; ?>
        </aside>

        <main class="flex-1 p-8 bg-stone-50">
            <div class="flex justify-center gap-10">
                <div class="home-proceed h-48 w-3/5 rounded-lg flex flex-col items-center justify-center shadow-lg">
                    <div class="flex w-4/5 items-center gap-3 bg-neutral-100 border-2 border-stone-300 py-1 px-1 rounded-2xl">
                        <input type="text" placeholder="Enter survey code..." class="flex-1 p-3 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-600">
                        <button class="bg-purple-700 text-white px-4 py-2 rounded-lg hover:bg-purple-800 transition">
                            Join
                        </button>
                    </div>
                </div>
                <div class="home-avatar bg-purple-200 h-48 w-1/3 rounded-lg flex flex-col items-center justify-center text-purple-700 shadow-lg">
                    <h1 class="text-2xl font-extrabold">Hello, <?= htmlspecialchars($user['name']) ?>!</h1>
                    <p class="text-gray-500 mt-2"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>