<?php
require_once 'dbConnect.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $name = trim($_POST['name']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);
        $created_at = date('Y-m-d H:i:s');

        // Validations
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        }
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $errors['user_exist'] = 'Email is already registered';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: register.php');
            exit();
        }

        // Hash password & insert user
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name, created_at) VALUES (:email, :password, :name, :created_at)");
        $stmt->execute([
            'email' => $email,
            'password' => $hashedPassword,
            'name' => $name,
            'created_at' => $created_at
        ]);

        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage()); 
    }
}

// LOGIN LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        if (empty($password)) {
            $errors['password'] = 'Password cannot be empty';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php');
            exit();
        }

        // Fetch user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'created_at' => $user['created_at']
            ];
            header('Location: home.php');
            exit();
        } else {
            $errors['login'] = 'Invalid email or password';
            $_SESSION['errors'] = $errors;
            header('Location: index.php');
            exit();
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>
