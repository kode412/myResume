<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $school = $_POST['school'] ?? '';
    $degree = $_POST['degree'] ?? '';
    $field = $_POST['field'] ?? '';
    $year_start = $_POST['year_start'] ?? null;
    $year_end = $_POST['year_end'] ?? null;
    $description = $_POST['description'] ?? '';

    if (empty($school) || empty($degree)) {
        die('School dan degree harus diisi.');
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE education SET school=?, degree=?, field=?, year_start=?, year_end=?, description=? WHERE id=?");
        $stmt->execute([$school, $degree, $field, $year_start, $year_end, $description, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO education (school, degree, field, year_start, year_end, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$school, $degree, $field, $year_start, $year_end, $description]);
    }

    header('Location: hal_content.php');
    exit();
}
?>
