<?php

session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $adminExists = false;
    $adminCheck = $conn->query("SELECT COUNT(*) AS admin_count FROM users WHERE role = 'admin'");
    if ($adminCheck && $adminRow = $adminCheck->fetch_assoc()) {
        $adminExists = (int)$adminRow['admin_count'] > 0;
    }

    if ($adminExists && $role === 'admin') {
        // Prevent additional admins from being registered
        $_SESSION['register_error'] = 'An admin account already exists. Please register as a user.';
        $_SESSION['active_form'] = 'register';
        header("Location: index.php");
        exit();
    }
    
    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");
    }
        
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            if ($user['role'] === 'admin') {
                header ("Location: ../aylagch_4x4/12.php");
            } else {
                header ("Location: user_page.php");
            }
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header ("Location: index.php");
    exit();
}

?>