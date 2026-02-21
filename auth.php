<?php
// Simple auth helper (uses `users` table when available). Development helper.
session_start();
require_once __DIR__ . '/koneksi.php';

function ensure_default_admin_if_needed($pdo) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM users");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || intval($row['c']) === 0) {
            $username = 'admin';
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $email = 'admin@example.local';
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$username, $email, $password]);
        }
    } catch (Exception $e) {
        // table might not exist yet; ignore
    }
}

function login_user($username, $password) {
    global $pdo;
    try {
        ensure_default_admin_if_needed($pdo);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
            // Allow legacy plain-text password (if DB was seeded manually)
            if ($password === $user['password']) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
        }
    } catch (Exception $e) {
        // fallback to simple hardcoded admin for environments without DB
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user'] = 'admin';
            $_SESSION['role'] = 'admin';
            return true;
        }
    }
    return false;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function logout_user() {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}

// -----------------------
// CSRF helpers
// -----------------------
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf($token) {
    if (empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field() {
    $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return "<input type=\"hidden\" name=\"csrf\" value=\"$t\">";
}

?>
