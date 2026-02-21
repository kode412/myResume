<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['is_identity'])) {
        // Handle Identity and Contact Profile
        $identity_name = $_POST['identity_name'] ?? '';
        $identity_subtitle = $_POST['identity_subtitle'] ?? '';
        $identity_content = $_POST['identity_content'] ?? '';
        
        $contact_email = $_POST['contact_email'] ?? '';
        $contact_phone = $_POST['contact_phone'] ?? '';
        $contact_location = $_POST['contact_location'] ?? '';

        // Save About/Identity
        $stmt = $pdo->prepare("SELECT id FROM pages WHERE `type` = 'about' LIMIT 1");
        $stmt->execute();
        $about_id = $stmt->fetchColumn();
        if ($about_id) {
            $pdo->prepare("UPDATE pages SET title = ?, subtitle = ?, content = ? WHERE id = ?")
                ->execute([$identity_name, $identity_subtitle, $identity_content, $about_id]);
        } else {
            $pdo->prepare("INSERT INTO pages (`type`, title, subtitle, content) VALUES ('about', ?, ?, ?)")
                ->execute([$identity_name, $identity_subtitle, $identity_content]);
        }

        // Save Contact
        $stmt = $pdo->prepare("SELECT id FROM pages WHERE `type` = 'contact' LIMIT 1");
        $stmt->execute();
        $contact_id = $stmt->fetchColumn();
        if ($contact_id) {
            $pdo->prepare("UPDATE pages SET title = ?, subtitle = ?, content = ? WHERE id = ?")
                ->execute([$contact_email, $contact_phone, $contact_location, $contact_id]);
        } else {
            $pdo->prepare("INSERT INTO pages (`type`, title, subtitle, content) VALUES ('contact', ?, ?, ?)")
                ->execute([$contact_email, $contact_phone, $contact_location]);
        }

        header('Location: hal_content.php');
        exit();
    }

    $id = $_POST['id'] ?? null;
    $type = $_POST['type'] ?? '';
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $content = $_POST['content'] ?? '';
    $pdf_file = $_POST['pdf_file'] ?? null;

    if (empty($type) || empty($content)) {
        die('Tipe dan konten harus diisi.');
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE pages SET `type` = ?, title = ?, subtitle = ?, content = ?, pdf_file = ? WHERE id = ?");
        $stmt->execute([$type, $title, $subtitle, $content, $pdf_file, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO pages (`type`, title, subtitle, content, pdf_file) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$type, $title, $subtitle, $content, $pdf_file]);
    }

    header('Location: hal_content.php');
    exit();
}

header('Location: hal_add_content.php');
exit();
?>