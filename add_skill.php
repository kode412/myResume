<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $proficiency = $_POST['proficiency'] ?? 50;
    $category = $_POST['category'] ?? 'Technical';

    if (empty($name)) {
        die('Nama skill harus diisi.');
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE skills SET name=?, proficiency=?, category=? WHERE id=?");
        $stmt->execute([$name, $proficiency, $category, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO skills (name, proficiency, category) VALUES (?, ?, ?)");
        $stmt->execute([$name, $proficiency, $category]);
    }

    header('Location: hal_content.php');
    exit();
}
?>
