<?php
$host = 'localhost';
$dbname = 'progress';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Get all tags
function getAllTags($pdo) {
    $stmt = $pdo->query("SELECT * FROM tags ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get tags for a specific card
function getCardTags($pdo, $cardId) {
    $stmt = $pdo->prepare("SELECT t.* FROM tags t 
                          JOIN card_tags ct ON t.id = ct.tag_id 
                          WHERE ct.card_id = ?");
    $stmt->execute([$cardId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>