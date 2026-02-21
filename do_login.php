<?php
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login_user($username, $password)) {
        header('Location: hal_content.php');
        exit();
    }
}

header('Location: login.php?error=Invalid+credentials');
exit();
