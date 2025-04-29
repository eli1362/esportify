<?php
global $db;
if (!isset($_GET['id'])) {
    echo "Aucun événement sélectionné.";
    exit;
}
require 'backend/config.php';

$eventId = intval($_GET['id']);
$stmt = $db->prepare("SELECT e.*, u.username AS organizer FROM events e JOIN users u ON u.id = e.organizer_id WHERE e.id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Événement introuvable.";
    exit;
}
?>

<div class="event-detail">
    <h2><?= htmlspecialchars($event['title']) ?></h2>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
    <p><strong>Joueurs:</strong> <?= $event['player_count'] ?></p>
    <p><strong>Organisateur:</strong> <?= htmlspecialchars($event['organizer']) ?></p>
    <p><strong>Date:</strong> <?= date("d/m/Y H:i", strtotime($event['start_time'])) ?> → <?= date("d/m/Y H:i", strtotime($event['end_time'])) ?></p>
    <p><strong>Statut:</strong> <?= htmlspecialchars($event['status']) ?></p>

    <?php if (!empty($event['image_path'])): ?>
        <img src="<?= htmlspecialchars($event['image_path']) ?>" alt="image événement" class="event-detail__img">
    <?php endif; ?>
</div>

