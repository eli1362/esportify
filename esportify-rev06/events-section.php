<?php
require_once 'backend/config.php';
global $pdo;

$today = date('Y-m-d');
$sql = "SELECT * FROM events WHERE is_validated = 1 AND end_date >= :today ORDER BY start_date ASC LIMIT 8";
$stmt = $pdo->prepare($sql);
$stmt->execute(['today' => $today]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="events-section">
    <h2 class="events-title">Événements e-sport à venir</h2>

    <div class="event-cards">
        <?php if (empty($events)): ?>
            <p class="no-events">Aucun événement disponible pour le moment.</p>
        <?php else: ?>
            <?php foreach ($events as $index => $event): ?>
                <div class="event-card <?= $index % 2 === 0 ? 'border-blue' : 'border-gradient' ?>">
                    <h3 class="event-name"><?= htmlspecialchars($event['name']) ?></h3>

                    <p class="event-participants">
                        Nombre de participants :
                        <span style="color: var(--Green-Blue);">
                            <?= htmlspecialchars($event['max_participants']) ?>
                        </span>
                    </p>

                    <p class="event-description"><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                    <div class="event-info">
                        <i class="fa-regular fa-calendar-days"></i>
                        <span>
                            Du <?= date("d M Y", strtotime($event['start_date'])) ?>
                            au <?= date("d M Y", strtotime($event['end_date'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <!-- "Voir tous les événements" button -->
    <a href="all-events.php" class="btn see-all-events">Voir tous les événements</a>
</section>
