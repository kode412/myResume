<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Manajemen Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mx-auto max-w-5xl px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Users</h1>
            <div class="space-x-2">
                <a href="hal_content.php" class="px-3 py-2 rounded border">Konten</a>
                <a href="hal_contacts.php" class="px-3 py-2 rounded border">Pesan</a>
                <a href="logout.php" class="px-3 py-2 rounded bg-red-600 text-white">Logout</a>
            </div>
        </div>

        <div class="flex justify-end mb-4">
            <a href="add_user.php" class="bg-green-600 text-white px-4 py-2 rounded">Tambah User</a>
        </div>

        <div class="bg-white rounded shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= $u['id'] ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($u['username']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= $u['role'] ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= $u['created_at'] ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <a href="edit_user.php?id=<?= $u['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                            <span class="mx-2">|</span>
                            <a href="delete_user.php?id=<?= $u['id'] ?>&token=<?= urlencode(csrf_token()) ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus user ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
