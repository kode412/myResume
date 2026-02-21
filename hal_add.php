<?php 
require_once 'koneksi.php';
// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Build query based on filter
$query = "SELECT c.* FROM cards c";
$params = [];

if ($filter !== 'all') {
    $query .= " WHERE c.project_type = ?";
    $params[] = $filter;
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all tags for the form
$allTags = getAllTags($pdo);

// Get project types for tabs
$projectTypes = ['all' => 'All Projects', 'Web' => 'Web', 'Mobile' => 'Mobile', 'Arduino' => 'Arduino', 'Desktop' => 'Desktop', 'IoT' => 'IoT'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Add Data</title>
</head>

<body>
    <div class="mx-auto max-w-3xl px-4 py-8">
        <h1 class="text-center text-2xl font-bold mb-6">Tambah Project Baru</h1>
        <div class="bg-white p-6 rounded shadow-sm">
            <form action="add_card.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Project</label>
                    <input type="text" id="title" name="title" required class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2" />
                </div>

                <div>
                    <label for="project_type" class="block text-sm font-medium text-gray-700">Jenis Project</label>
                    <select id="project_type" name="project_type" required class="mt-1 block w-full border border-gray-200 rounded-md px-3 py-2">
                        <option value="">Pilih Jenis Project</option>
                        <option value="Web">Web Development</option>
                        <option value="Mobile">Mobile App</option>
                        <option value="Arduino">Arduino Project</option>
                        <option value="Desktop">Desktop Application</option>
                        <option value="IoT">Internet of Things</option>
                    </select>
                </div>

                <div>
                    <label for="tags" class="block text-sm font-medium text-gray-700">Tech Stack</label>
                    <div class="checkbox-group mt-2">
                        <?php foreach ($allTags as $tag): ?>
                        <label class="inline-flex items-center mr-3"><input type="checkbox" id="tag_<?= $tag['id'] ?>" name="tags[]" value="<?= $tag['id'] ?>" class="mr-2"> <?= $tag['name'] ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label for="progress" class="block text-sm font-medium text-gray-700">Progress (%)</label>
                    <input type="number" id="progress" name="progress" min="0" max="100" required class="mt-1 block w-32 border border-gray-200 rounded-md px-3 py-2" />
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Gambar Project</label>
                    <input type="file" id="image" name="image" accept="image/*" required class="mt-1" />
                </div>

                <div class="text-right">
                    <button type="submit" class="submit-btn bg-green-600 text-white px-4 py-2 rounded">Tambah Project</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>