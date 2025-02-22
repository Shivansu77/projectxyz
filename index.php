<?php
session_start();
$errors = $_SESSION['errors'] ?? []; // Ensures $errors is always an array
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register & Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
  <div class="bg-white w-96 p-6 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center mb-4">Sign In</h1>
    <?php if (!empty($errors['login'])): ?>
      <div class="bg-red-500 text-white text-center p-2 rounded mb-3">
        <p><?= htmlspecialchars($errors['login']) ?></p>
      </div>
    <?php endif; ?>
    <form method="POST" action="user-account.php">
      <div class="mb-4 relative">
        <i class="fas fa-envelope absolute left-3 top-3 text-gray-500"></i>
        <input type="email" name="email" placeholder="Email" required class="w-full pl-10 p-2 border rounded-lg focus:border-blue-500 focus:ring-blue-500">
        <?php if (!empty($errors['email'])): ?>
          <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
        <?php endif; ?>
      </div>
      <div class="mb-4 relative">
        <i class="fas fa-lock absolute left-3 top-3 text-gray-500"></i>
        <input id="password" type="password" name="password" placeholder="Password" required class="w-full pl-10 p-2 border rounded-lg focus:border-blue-500 focus:ring-blue-500">
        <i id="eye" class="fa fa-eye absolute right-3 top-3 text-gray-500 cursor-pointer"></i>
        <?php if (!empty($errors['password'])): ?>
          <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
        <?php endif; ?>
      </div>
      <p class="text-right text-blue-500 hover:underline text-sm mb-4">
        <a href="#">Recover Password</a>
      </p>
      <input type="submit" class="w-full bg-indigo-400 text-white p-2 rounded-lg hover:bg-indigo-800 cursor-pointer" value="Sign In" name="signin">
    </form>
    <p class="text-center my-4 text-gray-500">----------or----------</p>
    <div class="flex justify-center space-x-4">
      <i class="fab fa-google text-blue-500 text-2xl cursor-pointer"></i>
      <i class="fab fa-facebook text-blue-700 text-2xl cursor-pointer"></i>
    </div>
    <div class="text-center mt-4 text-sm">
      <p>Don't have an account yet? <a href="register.php" class="text-blue-500 hover:underline">Sign Up</a></p>
    </div>
  </div>

  <!-- Link to external script -->
  <script src="scripts/script.js" defer></script>

</body>
</html>

<?php unset($_SESSION['errors']); ?>
