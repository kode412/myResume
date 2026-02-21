<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        die('Invalid CSRF token');
    }
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'editor';

    if (empty($username) || empty($password)) {
        die('Username dan password diperlukan.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hash, $role]);

    header('Location: hal_users.php');
    exit();
}

// Show form
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tambah User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mx-auto max-w-md px-4 py-8">
        <div class="bg-white p-6 rounded shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Tambah User</h2>
            <form method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Username</label>
                    <input name="username" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input name="email" type="email" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input name="password" type="password" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2">
                        <option value="admin">admin</option>
                        <option value="editor" selected>editor</option>
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
