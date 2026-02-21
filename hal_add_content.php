<?php
require_once 'koneksi.php';

$types = ['about' => 'About', 'service' => 'Service', 'contact' => 'Contact', 'resume' => 'Resume'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Konten - ProgressKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mx-auto max-w-3xl px-4 py-8">
        <h1 class="text-center text-2xl font-bold mb-6">Tambah Konten Baru</h1>
        <form action="add_content.php" method="POST" class="space-y-4 bg-white p-6 rounded shadow-sm">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                <select name="type" id="type" required class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2">
                    <?php foreach ($types as $k => $v): ?>
                        <option value="<?= $k ?>"><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                <input type="text" id="title" name="title" required class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" />
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Konten (HTML diizinkan)</label>
                <textarea id="content" name="content" rows="8" required class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2"></textarea>
            </div>

            <div class="text-right">
                <button type="submit" class="submit-btn bg-green-600 text-white px-4 py-2 rounded">Simpan Konten</button>
            </div>
        </form>
    </div>
</body>
</html>