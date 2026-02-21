<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? '';
if ($id && validate_csrf($token)) {
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: hal_contacts.php');
exit();
