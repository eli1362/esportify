<?php
require_once 'backend/config.php';
global $pdo;

$today = date('Y-m-d');

// Check if a search term is provided
$search_term = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

// Modify the query to filter events by name if a search term is provided
$sql = "SELECT * FROM events WHERE is_validated = 1 AND end_date >= :today AND name LIKE :search ORDER BY start_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['today' => $today, 'search' => $search_term]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user has favorited the event
function is_favorited($event_id, $user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM user_favorites WHERE event_id = :event_id AND user_id = :user_id");
    $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);
    return $stmt->fetch() !== false; // Returns true if found
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esportify</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<header class="header">
    <?php include_once "header.php" ?>
</header>
<main>
<section class="events-section">
    <div class="container">
        <h2 class="events-title">Tous les événements e-sport</h2>

        <!-- Search bar to filter events by name -->
        <form method="GET" action="all-events.php" class="filters-form" style="margin-bottom: 2rem;">
            <input type="text" name="search" placeholder="Rechercher par nom d'événement" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="padding: 10px; margin-right: 10px;"/>
            <button type="submit" class="btn" style="padding: 10px; border: 1px solid var(--Blue-Neon)">Filtrer</button>
        </form>

        <div class="event-cards">
            <?php if (empty($events)): ?>
                <div class="no-events">
                    <h3>Aucun événement disponible</h3>
                    <p>Il n'y a pas d'événements à venir pour le moment. Revenez plus tard !</p>
                </div>
            <?php else: ?>
                <?php foreach ($events as $index => $event): ?>
                    <div class="event-card <?= $index % 2 === 0 ? 'border-blue' : 'border-gradient' ?>">
                        <h3 class="event-name"><?= htmlspecialchars($event['name']) ?></h3>

                        <p class="event-participants">
                            Nombre de participants :
                            <span style="color: var(--Green-Blue);"><?= htmlspecialchars($event['max_participants']) ?></span>
                        </p>

                        <p class="event-description"><?= nl2br(htmlspecialchars($event['description'])) ?></p>

                        <div class="event-info">
                            <i class="fa-regular fa-calendar-days"></i>
                            <span>Du <?= date("d M Y", strtotime($event['start_date'])) ?> au <?= date("d M Y", strtotime($event['end_date'])) ?></span>
                        </div>

                        <!-- Favorite Button (only if the user is logged in) -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="favorite_event.php" style="margin-top: 1rem;">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn user-dashboard__btn" style="border: 1px solid var(--Blue-Neon)">
                                    <i class="fa-regular <?= is_favorited($event['id'], $_SESSION['user_id']) ? 'fa-heart' : 'fa-heart-circle' ?>"></i>
                                    <?= is_favorited($event['id'], $_SESSION['user_id']) ? 'Retiré des favoris' : 'Ajouter aux favoris' ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <!-- Display unavailable if event is not validated -->
                        <?php if ($event['is_validated'] == 0): ?>
                            <p><em>Non disponible (non validé)</em></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
</main>
<footer>
    <?php
    require_once "footer.php";
    ?>
</footer>
<script src="assets/js/script.js"></script>

</body>
</html>
