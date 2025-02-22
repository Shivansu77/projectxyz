<?php
$host = "localhost";
$port = 8889; // MAMP default MySQL port
$dbname = "projectx"; // Update with your database name
$username = "root";
$password = "root"; // MAMP default MySQL password

try {
    // Establishing the connection using PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);

    // Set error mode to exception for debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connected successfully!";
} catch (PDOException $e) {
    // Display error message if connection fails
    die("❌ Database Connection Failed: " . $e->getMessage());
}
?>
