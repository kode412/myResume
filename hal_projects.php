<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$tags = $pdo->query("SELECT * FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare(
    "SELECT SQL_CALC_FOUND_ROWS c.*, COALESCE(GROUP_CONCAT(t.name SEPARATOR ', '), '') AS tags
     FROM cards c
     LEFT JOIN card_tags ct ON ct.card_id = c.id
     LEFT JOIN tags t ON t.id = ct.tag_id
     GROUP BY c.id
     ORDER BY c.id DESC
     LIMIT ? OFFSET ?"
);
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = (int)$pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = (int)ceil($total / $perPage);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin - Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Simple modal backdrop */
        .modal-backdrop { background: rgba(0,0,0,0.4); }
    </style>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Projects</h1>
        <button id="openAdd" class="bg-blue-600 text-white px-4 py-2 rounded">Add Project</button>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Progress</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Tags</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <?php foreach ($cards as $c): ?>
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-700"><?= $c['id'] ?></td>
                    <td class="px-4 py-3"><img src="<?= htmlspecialchars($c['image_path']) ?>" class="w-16 h-10 object-cover rounded" alt=""></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?= htmlspecialchars($c['title']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($c['project_type']) ?></td>
                    <td class="px-4 py-3">
                        <div class="w-40 bg-gray-200 rounded h-3 overflow-hidden">
                            <div class="bg-green-500 h-3" style="width:<?= (int)$c['progress'] ?>%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1"><?= (int)$c['progress'] ?>%</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600"><?= htmlspecialchars($c['tags']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <a href="hal_edit_content.php?id=<?= $c['id'] ?>" class="text-blue-600 mr-3">Edit</a>
                        <a href="delete_project.php?id=<?= $c['id'] ?>&token=<?= csrf_token() ?>" class="text-red-600" onclick="return confirm('Hapus project ini?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-center space-x-2">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="px-3 py-1 rounded <?= $p==$page? 'bg-blue-600 text-white':'bg-white' ?> border" href="?page=<?= $p ?>"><?= $p ?></a>
        <?php endfor; ?>
    </div>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="absolute inset-0 modal-backdrop" id="backdrop"></div>
    <div class="bg-white rounded shadow-lg z-60 w-full max-w-2xl mx-4 overflow-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Add Project</h2>
                <button id="close" class="text-gray-500">✕</button>
            </div>
            <form action="do_add_project.php" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input name="title" required class="mt-1 block w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project Type</label>
                        <input name="project_type" class="mt-1 block w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Progress (%)</label>
                        <input type="number" name="progress" min="0" max="100" value="0" class="mt-1 block w-full border rounded p-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="image" accept="image/*" required class="mt-1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tags</label>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <?php foreach ($tags as $t): ?>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="tags[]" value="<?= $t['id'] ?>" class="form-checkbox" />
                                    <span class="ml-2"><?= htmlspecialchars($t['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="cancel" class="mr-2 px-4 py-2 rounded border">Cancel</button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const openBtn = document.getElementById('openAdd');
    const modal = document.getElementById('modal');
    const closeBtn = document.getElementById('close');
    const cancelBtn = document.getElementById('cancel');
    const backdrop = document.getElementById('backdrop');

    function showModal() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function hideModal() { modal.classList.remove('flex'); modal.classList.add('hidden'); }

    openBtn && openBtn.addEventListener('click', showModal);
    closeBtn && closeBtn.addEventListener('click', hideModal);
    cancelBtn && cancelBtn.addEventListener('click', hideModal);
    backdrop && backdrop.addEventListener('click', hideModal);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hideModal(); });
</script>
</body>
</html>
