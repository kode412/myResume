<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: hal_projects.php');
    exit();
}

$token = $_POST['csrf_token'] ?? '';
if (!validate_csrf($token)) {
    die('Invalid CSRF token');
}

$title = $_POST['title'] ?? '';
$project_type = $_POST['project_type'] ?? '';
$progress = (int)($_POST['progress'] ?? 0);
$tags = $_POST['tags'] ?? [];

// Handle file upload
$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$fileName = basename($_FILES["image"]["name"] ?? '');
if (empty($fileName)) {
    die('No image uploaded');
}
$targetFile = $targetDir . uniqid() . '_' . $fileName;
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

$check = getimagesize($_FILES["image"]["tmp_name"]);
if ($check === false) {
    die("File bukan gambar.");
}

if ($_FILES["image"]["size"] > 2000000) {
    die("Ukuran gambar terlalu besar. Maksimal 2MB.");
}

if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
    die("Hanya format JPG, JPEG, PNG & GIF yang diizinkan.");
}

if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO cards (title, project_type, image_path, progress) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $project_type, $targetFile, $progress]);
        $cardId = $pdo->lastInsertId();

        foreach ($tags as $tagId) {
            $stmt = $pdo->prepare("INSERT INTO card_tags (card_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$cardId, $tagId]);
        }

        $pdo->commit();
        header('Location: hal_projects.php');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die('Terjadi kesalahan: ' . $e->getMessage());
    }
} else {
    die("Terjadi kesalahan saat mengupload gambar.");
}
