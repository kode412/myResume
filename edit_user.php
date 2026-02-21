<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: hal_users.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        die('Invalid CSRF token');
    }

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'editor';

    if (empty($username)) {
        die('Username diperlukan.');
    }

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $hash, $role, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $role, $id]);
    }

    header('Location: hal_users.php');
    exit();
}

$stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { header('Location: hal_users.php'); exit(); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mx-auto max-w-md px-4 py-8">
        <div class="bg-white p-6 rounded shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Edit User</h2>
            <form method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input name="username" value="<?= htmlspecialchars($user['username']) ?>" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input name="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password (kosongkan jika tidak ingin mengubah)</label>
                    <input name="password" type="password" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2">
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                        <option value="editor" <?= $user['role'] === 'editor' ? 'selected' : '' ?>>editor</option>
                    </select>
                </div>
                <div class="text-right">
                    <a href="hal_users.php" class="mr-2">Batal</a>
                    <button class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
