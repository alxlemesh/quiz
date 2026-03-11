<?php
session_start();
require_once '../utils/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } elseif (strlen($password) < 4) {
        $error = 'Password must be at least 4 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        if (registerUser($username, $password)) {
            $success = 'Registration successful! You can now log in.';
        } else {
            $error = 'Username already exists. Please choose a different username.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h1 class="text-center mb-4">Register</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center py-2"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success text-center py-2"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required minlength="3" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <div class="form-text">At least 3 characters</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="4">
                <div class="form-text">At least 4 characters</div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="4">
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <div class="text-center mt-3">
            <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Log in here</a></p>
        </div>
    </div>
</body>
</html>
