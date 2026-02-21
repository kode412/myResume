<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi data
    $title = $_POST['title'] ?? '';
    $project_type = $_POST['project_type'] ?? '';
    $progress = $_POST['progress'] ?? 0;
    $tags = $_POST['tags'] ?? [];
    
    // Handle file upload
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . uniqid() . '_' . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Validasi file
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        die("File bukan gambar.");
    }
    
    // Batasi ukuran file (max 2MB)
    if ($_FILES["image"]["size"] > 2000000) {
        die("Ukuran gambar terlalu besar. Maksimal 2MB.");
    }
    
    // Hanya format gambar tertentu
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        die("Hanya format JPG, JPEG, PNG & GIF yang diizinkan.");
    }
    
    // Upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        try {
            $pdo->beginTransaction();
            
            // Simpan card ke database
            $stmt = $pdo->prepare("INSERT INTO cards (title, project_type, image_path, progress) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $project_type, $targetFile, $progress]);
            $cardId = $pdo->lastInsertId();
            
            // Simpan tags
            foreach ($tags as $tagId) {
                $stmt = $pdo->prepare("INSERT INTO card_tags (card_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$cardId, $tagId]);
            }
            
            $pdo->commit();
            
            // Redirect ke halaman utama
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Terjadi kesalahan: " . $e->getMessage());
        }
    } else {
        die("Terjadi kesalahan saat mengupload gambar.");
    }
}
?>