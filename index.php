<?php

session_start();
require_once 'config.php';

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
]; 
$activeForm = $_SESSION['active_form'] ?? 'login';

// Check if an admin already exists to control role options
$adminExists = false;
$adminCheck = $conn->query("SELECT COUNT(*) AS admin_count FROM users WHERE role = 'admin'");
if ($adminCheck && $adminRow = $adminCheck->fetch_assoc()) {
    $adminExists = (int)$adminRow['admin_count'] > 0;
}

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>АЯЛАГЧ 4X4 — Login & Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login-page">
        <header class="login-header">
            <h1>АЯЛАГЧ <span>4X4</span></h1>
        </header>
        <div class="container">
        <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login_register.php" method="post">
                <h2>Login</h2>
                <?= showError($errors['login']); ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account?<a href="#" onclick="showForm('register-form')"> Register</a></p>
            </form>
        </div>

        <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="login_register.php" method="post">
                <h2>Register</h2>
                <?= showError($errors['register']); ?>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" required>
                    <option value="">--Select Role--</option>
                    <option value="user">User</option>
                    <?php if (!$adminExists): ?>
                        <option value="admin">Admin</option>
                    <?php endif; ?>
                </select>
                <button type="submit" name="register">Register</button>
                <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
            </form>
        </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>