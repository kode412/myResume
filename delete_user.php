<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$id = $_GET['id'] ?? null;
if ($id) {
    $token = $_GET['token'] ?? '';
    if (validate_csrf($token)) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}
header('Location: hal_users.php');
exit();
