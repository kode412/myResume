<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

$action = $_GET['action'] ?? 'pages';

header('Content-Type: text/html; charset=utf-8');

if ($action === 'identity') {
    // Fetch information from 'pages' table for identity entries
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE `type` IN ('about', 'contact')");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $about = [];
    $contact = [];
    foreach ($data as $d) {
        if ($d['type'] === 'about') $about = $d;
        if ($d['type'] === 'contact') $contact = $d;
    }
    ?>
    <div class="mb-6">
        <h2 class="text-2xl font-bold gradient-text">Identitas & Kontak</h2>
        <p class="text-gray-600">Kelola informasi diri Anda yang akan tampil di Resume dan Portofolio.</p>
    </div>

    <form action="add_content.php" method="post" class="space-y-6 max-w-4xl">
        <?= csrf_field() ?>
        <input type="hidden" name="is_identity" value="1">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- PERSONAL IDENTITY -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Identitas Diri</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input name="identity_name" value="<?= htmlspecialchars($about['title'] ?? '') ?>" required class="mt-1 block w-full border rounded-xl p-3 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Contoh: Purwanto Santoso" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gelar / Posisi (Subtitle)</label>
                    <input name="identity_subtitle" value="<?= htmlspecialchars($about['subtitle'] ?? '') ?>" class="mt-1 block w-full border rounded-xl p-3 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Contoh: Full Stack Developer" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ringkasan Diri (Summary)</label>
                    <textarea name="identity_content" class="mt-1 block w-full border rounded-xl p-3 focus:ring-2 focus:ring-primary-500 outline-none" rows="5" placeholder="Tuliskan bio singkat Anda..."><?= htmlspecialchars($about['content'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- CONTACT INFO -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Informasi Kontak</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Utama (Tampil di Resume)</label>
                    <input name="contact_email" value="<?= htmlspecialchars($contact['title'] ?? '') ?>" class="mt-1 block w-full border rounded-xl p-3 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="email@example.com" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor HP / WhatsApp</label>
                    <input name="contact_phone" value="<?= htmlspecialchars($contact['subtitle'] ?? '') ?>" class="mt-1 block w-full border rounded-xl p-3 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="+62 8xx..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lokasi / Alamat</label>
                    <input name="contact_location" value="<?= htmlspecialchars($contact['content'] ?? '') ?>" class="mt-1 block w-full border rounded-xl p-3 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Contoh: Jakarta, Indonesia" />
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 text-white font-bold shadow-lg hover:shadow-primary-500/30 transition-all duration-300">
                Simpan Perubahan
            </button>
        </div>
    </form>
    <?php
    exit;
}

if ($action === 'pages') {
    $stmt = $pdo->query("SELECT * FROM pages ORDER BY `type`, id DESC");
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="flex justify-end mb-4">
        <button id="addBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg">Tambah Konten</button>
    </div>
    <div class="bg-white rounded shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <?php foreach ($pages as $p): ?>
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $p['id'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($p['type']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($p['title']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <button class="text-blue-600 hover:underline editBtn" data-id="<?= $p['id'] ?>" data-type="page">Edit</button>
                        <span class="mx-2">|</span>
                        <a href="delete_content.php?id=<?= $p['id'] ?>&token=<?= urlencode(csrf_token()) ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus konten ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    exit;
}

if ($action === 'projects') {
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

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
    <div class="flex justify-end mb-4">
        <button id="addBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Add Project</button>
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
                        <a href="delete_project.php?id=<?= $c['id'] ?>&token=<?= urlencode(csrf_token()) ?>" class="text-red-600" onclick="return confirm('Hapus project ini?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-6 flex justify-center space-x-2">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="px-3 py-1 rounded <?= $p==$page? 'bg-blue-600 text-white':'bg-white' ?> border" href="#" data-page="<?= $p ?>"><?= $p ?></a>
        <?php endfor; ?>
    </div>
    <?php
    exit;
}

if ($action === 'contacts') {
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
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
                        <a href="delete_contact.php?id=<?= $c['id'] ?>&token=<?= urlencode(csrf_token()) ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus pesan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    exit;
}

if ($action === 'users') {
    $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="flex justify-end mb-4">
        <button id="addBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg">Tambah User</button>
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
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($u['role']) ?></td>
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
    <?php
    exit;
}

if ($action === 'form') {
    $type = $_GET['type'] ?? 'page';
    $isEdit = isset($_GET['isEdit']) && $_GET['isEdit'] == '1';
    $editId = $isEdit ? ($_GET['id'] ?? null) : null;
    $pageData = null;
    
    if ($isEdit && $editId) {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$editId]);
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($type === 'project') {
        $tags = $pdo->query("SELECT * FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <form action="do_add_project.php" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input name="title" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Masukkan judul project" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Project Type</label>
                    <select name="project_type" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="Web">Web Development</option>
                        <option value="Mobile">Mobile App</option>
                        <option value="Arduino">Arduino Project</option>
                        <option value="Desktop">Desktop Application</option>
                        <option value="IoT">Internet of Things</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Progress (%)</label>
                    <input type="number" name="progress" min="0" max="100" value="0" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Image</label>
                    <input type="file" name="image" accept="image/*" required class="mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tags</label>
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-2">
                        <?php foreach ($tags as $t): ?>
                            <label class="inline-flex items-center text-sm text-gray-600">
                                <input type="checkbox" name="tags[]" value="<?= $t['id'] ?>" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                                <span class="ml-2"><?= htmlspecialchars($t['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </form>
        <?php
    } elseif ($type === 'page') {
        ?>
        <form action="add_content.php" method="post" class="space-y-4">
                <?= csrf_field() ?>
                <?php if ($isEdit && $pageData): ?>
                    <input type="hidden" name="id" value="<?= $pageData['id'] ?>" />
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="about" <?= ($pageData && $pageData['type'] === 'about') ? 'selected' : '' ?>>About / Hero</option>
                        <option value="service" <?= ($pageData && $pageData['type'] === 'service') ? 'selected' : '' ?>>Service</option>
                        <option value="project" <?= ($pageData && $pageData['type'] === 'project') ? 'selected' : '' ?>>Project Section</option>
                        <option value="contact" <?= ($pageData && $pageData['type'] === 'contact') ? 'selected' : '' ?>>Contact</option>
                        <option value="resume" <?= ($pageData && $pageData['type'] === 'resume') ? 'selected' : '' ?>>Resume</option>
                        <option value="other" <?= ($pageData && $pageData['type'] === 'other') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input name="title" value="<?= $pageData ? htmlspecialchars($pageData['title']) : '' ?>" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Judul konten" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" rows="6" placeholder="Isi konten..."><?= $pageData ? htmlspecialchars($pageData['content']) : '' ?></textarea>
            </div>
        </form>
        <?php
    } elseif ($type === 'education') {
        $educationData = null;
        if ($isEdit && $editId) {
            $stmt = $pdo->prepare("SELECT * FROM education WHERE id = ?");
            $stmt->execute([$editId]);
            $educationData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>
        <form action="add_education.php" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($isEdit && $educationData): ?>
                <input type="hidden" name="id" value="<?= $educationData['id'] ?>" />
            <?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700">Sekolah / Universitas</label>
                <input name="school" value="<?= $educationData ? htmlspecialchars($educationData['school']) : '' ?>" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Gelar</label>
                <input name="degree" value="<?= $educationData ? htmlspecialchars($educationData['degree']) : '' ?>" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bidang Studi</label>
                <input name="field" value="<?= $educationData ? htmlspecialchars($educationData['field']) : '' ?>" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Mulai</label>
                    <input type="number" name="year_start" value="<?= $educationData ? $educationData['year_start'] : '' ?>" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Selesai</label>
                    <input type="number" name="year_end" value="<?= $educationData ? $educationData['year_end'] : '' ?>" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" rows="3"><?= $educationData ? htmlspecialchars($educationData['description']) : '' ?></textarea>
            </div>
        </form>
        <?php
    } elseif ($type === 'skill') {
        $skillData = null;
        if ($isEdit && $editId) {
            $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
            $stmt->execute([$editId]);
            $skillData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>
        <form action="add_skill.php" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($isEdit && $skillData): ?>
                <input type="hidden" name="id" value="<?= $skillData['id'] ?>" />
            <?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Skill</label>
                <input name="name" value="<?= $skillData ? htmlspecialchars($skillData['name']) : '' ?>" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                <select name="category" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="Technical" <?= ($skillData && $skillData['category'] === 'Technical') ? 'selected' : 'selected' ?>>Technical</option>
                    <option value="Soft Skills" <?= ($skillData && $skillData['category'] === 'Soft Skills') ? 'selected' : '' ?>>Soft Skills</option>
                    <option value="Languages" <?= ($skillData && $skillData['category'] === 'Languages') ? 'selected' : '' ?>>Languages</option>
                    <option value="Other" <?= ($skillData && $skillData['category'] === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Profisiensi (0-100%)</label>
                <input type="number" name="proficiency" min="0" max="100" value="<?= $skillData ? $skillData['proficiency'] : '50' ?>" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
        </form>
        <?php
    } elseif ($type === 'experience') {
        $experienceData = null;
        if ($isEdit && $editId) {
            $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ?");
            $stmt->execute([$editId]);
            $experienceData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        ?>
        <form action="add_experience.php" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <?php if ($isEdit && $experienceData): ?>
                <input type="hidden" name="id" value="<?= $experienceData['id'] ?>" />
            <?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700">Perusahaan</label>
                <input name="company" value="<?= $experienceData ? htmlspecialchars($experienceData['company']) : '' ?>" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Posisi</label>
                <input name="position" value="<?= $experienceData ? htmlspecialchars($experienceData['position']) : '' ?>" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Mulai</label>
                    <input type="number" name="year_start" value="<?= $experienceData ? $experienceData['year_start'] : '' ?>" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tahun Selesai</label>
                    <input type="number" name="year_end" value="<?= $experienceData ? $experienceData['year_end'] : '' ?>" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" rows="3"><?= $experienceData ? htmlspecialchars($experienceData['description']) : '' ?></textarea>
            </div>
        </form>
        <?php
    } elseif ($type === 'user') {
        ?>
        <form action="add_user.php" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input name="username" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input name="email" type="email" class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input name="password" type="password" required class="mt-1 block w-full border rounded p-2 focus:ring-2 focus:ring-primary-500 outline-none" />
            </div>
        </form>
        <?php
    }

    exit;
}

if ($action === 'education') {
    $stmt = $pdo->query("SELECT * FROM education ORDER BY year_end DESC, year_start DESC");
    $education = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="flex justify-end mb-4">
        <button id="addBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg">Tambah Pendidikan</button>
    </div>
    <div class="bg-white rounded shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Sekolah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Gelar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Bidang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Tahun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <?php foreach ($education as $e): ?>
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $e['id'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($e['school']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($e['degree']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($e['field']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $e['year_start'] ?> - <?= $e['year_end'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <button class="text-blue-600 hover:underline editBtn" data-id="<?= $e['id'] ?>" data-type="education">Edit</button>
                        <span class="mx-2">|</span>
                        <a href="delete_education.php?id=<?= $e['id'] ?>&token=<?= urlencode(csrf_token()) ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus pendidikan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    exit;
}

if ($action === 'skills') {
    $stmt = $pdo->query("SELECT * FROM skills ORDER BY category, sort_order, name");
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="flex justify-end mb-4">
        <button id="addBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg">Tambah Skill</button>
    </div>
    <div class="bg-white rounded shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Profisiensi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <?php foreach ($skills as $s): ?>
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $s['id'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($s['name']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($s['category']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <div class="w-40 bg-gray-200 h-3 rounded overflow-hidden">
                            <div class="bg-green-500 h-3" style="width:<?= (int)$s['proficiency'] ?>%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1"><?= (int)$s['proficiency'] ?>%</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <button class="text-blue-600 hover:underline editBtn" data-id="<?= $s['id'] ?>" data-type="skill">Edit</button>
                        <span class="mx-2">|</span>
                        <a href="delete_skill.php?id=<?= $s['id'] ?>&token=<?= urlencode(csrf_token()) ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus skill ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    exit;
}

if ($action === 'experience') {
    $stmt = $pdo->query("SELECT * FROM experience ORDER BY year_end DESC, year_start DESC");
    $experience = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="flex justify-end mb-4">
        <button id="addBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg">Tambah Pengalaman</button>
    </div>
    <div class="bg-white rounded shadow-sm overflow-x-auto">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Perusahaan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Posisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Tahun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y">
                <?php foreach ($experience as $ex): ?>
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $ex['id'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($ex['company']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($ex['position']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= $ex['year_start'] ?> - <?= $ex['year_end'] ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <button class="text-blue-600 hover:underline editBtn" data-id="<?= $ex['id'] ?>" data-type="experience">Edit</button>
                        <span class="mx-2">|</span>
                        <a href="delete_experience.php?id=<?= $ex['id'] ?>&token=<?= urlencode(csrf_token()) ?>" class="text-red-600 hover:underline" onclick="return confirm('Hapus pengalaman ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    exit;
}

echo "";
