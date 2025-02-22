<?php
session_start();
$errors = $_SESSION['errors'] ?? []; // Ensure $errors is always an array
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-center mb-4">Register</h1>

        <?php if (!empty($errors['user_exist'])) : ?>
            <div class="bg-red-500 text-white text-center p-2 rounded mb-3">
                <p><?= htmlspecialchars($errors['user_exist']) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="user-account.php">
            <div class="mb-4 relative">
                <i class="fas fa-user absolute left-3 top-3 text-gray-500"></i>
                <input type="text" name="name" id="name" placeholder="Name" required
                    class="w-full pl-10 p-2 border rounded-lg focus:border-blue-500 focus:ring-blue-500">
                <?php if (!empty($errors['name'])) : ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4 relative">
                <i class="fas fa-envelope absolute left-3 top-3 text-gray-500"></i>
                <input type="email" name="email" id="email" placeholder="Email" required
                    class="w-full pl-10 p-2 border rounded-lg focus:border-blue-500 focus:ring-blue-500">
                <?php if (!empty($errors['email'])) : ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4 relative">
                <i class="fas fa-lock absolute left-3 top-3 text-gray-500"></i>
                <input type="password" name="password" id="password" placeholder="Password"
                    class="w-full pl-10 p-2 border rounded-lg focus:border-blue-500 focus:ring-blue-500">
                <i id="eye" class="fa fa-eye absolute right-3 top-3 text-gray-500 cursor-pointer"></i>
                <?php if (!empty($errors['password'])) : ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4 relative">
                <i class="fas fa-lock absolute left-3 top-3 text-gray-500"></i>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required
                    class="w-full pl-10 p-2 border rounded-lg focus:border-blue-500 focus:ring-blue-500">
                <?php if (!empty($errors['confirm_password'])) : ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                <?php endif; ?>
            </div>

            <input type="submit" class="w-full bg-indigo-400 text-white p-2 rounded-lg hover:bg-indigo-600 cursor-pointer"
                value="Sign Up" name="signup">
        </form>

        <p class="text-center my-4 text-gray-500">----------or----------</p>

        <div class="flex justify-center space-x-4">
            <i class="fab fa-google text-blue-500 text-2xl cursor-pointer"></i>
            <i class="fab fa-facebook text-blue-700 text-2xl cursor-pointer"></i>
        </div>

        <div class="text-center mt-4 text-sm">
            <p>Already Have an Account? <a href="index.php" class="text-blue-500 hover:underline">Sign In</a></p>
        </div>
    </div>

    <script src="scripts/script.js"></script>
</body>

</html>

<?php unset($_SESSION['errors']); // Clear errors after displaying ?>
