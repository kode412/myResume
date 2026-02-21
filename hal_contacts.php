<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pesan Masuk - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mx-auto max-w-5xl px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Pesan Masuk</h1>
            <div class="space-x-2">
                <a href="hal_content.php" class="px-3 py-2 rounded border">Konten</a>
                <a href="hal_users.php" class="px-3 py-2 rounded border">Users</a>
                <a href="logout.php" class="px-3 py-2 rounded bg-red-600 text-white">Logout</a>
            </div>
        </div>

        <div class="bg-white rounded shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Pesan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    <?php foreach ($contacts as $c): ?>
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= $c['id'] ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($c['email']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= nl2br(htmlspecialchars($c['message'])) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?= $c['created_at'] ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <a href="delete_contact.php?id=<?= $c['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus pesan ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
