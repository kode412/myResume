<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? null;

if ($id && validate_csrf($token)) {
    $stmt = $pdo->prepare("DELETE FROM experience WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: hal_content.php');
exit();
?>
