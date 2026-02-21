<?php
require_once 'koneksi.php';

$type = $_GET['type'] ?? 'all';

// Build query based on filter
$query = "SELECT c.* FROM cards c";
$params = [];

if ($type !== 'all') {
    $query .= " WHERE c.project_type = ?";
    $params[] = $type;
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cards as $card): 
    $cardTags = getCardTags($pdo, $card['id']);
?>
    <div class="card">
        <img src="<?= htmlspecialchars($card['image_path']) ?>" alt="<?= htmlspecialchars($card['title']) ?>" class="card-image">
        <div class="card-content">
            <h3 class="card-title"><?= htmlspecialchars($card['title']) ?></h3>
            <span class="project-type"><?= htmlspecialchars($card['project_type']) ?></span>
            
            <div class="tags-container">
                <?php foreach ($cardTags as $tag): ?>
                    <span class="tag" style="background-color: <?= $tag['color'] ?>">
                        <?= htmlspecialchars($tag['name']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
            
            <div class="progress-container">
                            style="width: <?= $card['progress'] ?>%; background-color: <?= ($card['progress'] == 100) ? 'green' : 'default-color' ?>;">
            </div>
            <p class="progress-text"><?= $card['progress'] ?>% selesai</p>
        </div>
    </div>
<?php endforeach; 
if (empty($cards)): ?>
    <div class="no-projects">
        Tidak ada project yang ditemukan untuk kategori ini.
    </div>
<?php endif; ?>