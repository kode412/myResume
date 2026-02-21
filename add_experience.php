<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $company = $_POST['company'] ?? '';
    $position = $_POST['position'] ?? '';
    $year_start = $_POST['year_start'] ?? null;
    $year_end = $_POST['year_end'] ?? null;
    $description = $_POST['description'] ?? '';

    if (empty($company) || empty($position)) {
        die('Company dan position harus diisi.');
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE experience SET company=?, position=?, year_start=?, year_end=?, description=? WHERE id=?");
        $stmt->execute([$company, $position, $year_start, $year_end, $description, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO experience (company, position, year_start, year_end, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$company, $position, $year_start, $year_end, $description]);
    }

    header('Location: hal_content.php');
    exit();
}
?>
