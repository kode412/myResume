<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$id = $_GET['id'] ?? null;
$token = $_GET['token'] ?? '';
if ($id && validate_csrf($token)) {
    // remove tags mapping
    $stmt = $pdo->prepare("DELETE FROM card_tags WHERE card_id = ?");
    $stmt->execute([$id]);

    // get image path to unlink
    $stmt = $pdo->prepare("SELECT image_path FROM cards WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['image_path']) && file_exists($row['image_path'])) {
        @unlink($row['image_path']);
    }

    // delete card
    $stmt = $pdo->prepare("DELETE FROM cards WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: hal_projects.php');
exit();

?>
