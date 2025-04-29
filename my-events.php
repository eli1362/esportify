<?php
require_once 'backend/config.php';
global $pdo;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';

// Prepare the query to fetch events based on the user's role
$sql = "SELECT * FROM events WHERE is_validated = 1 AND end_date >= :today";

// Get today's date
$today = date('Y-m-d');

// For admin, they can see all validated events
if ($user_role === 'admin') {
    // Optionally, filter by organizer for admins if needed
    if (isset($_GET['organizer']) && !empty($_GET['organizer'])) {
        $sql .= " AND organizer LIKE :organizer";
    }
}
// For employees, they can only see the events they created OR the events they organized
else if ($user_role === 'employee') {
    $sql .= " AND (created_by = :user_id OR organizer = :username)";
}

// Apply search functionality
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $sql .= " AND name LIKE :search";
}

$sql .= " ORDER BY start_date DESC"; // Order by start date

$stmt = $pdo->prepare($sql);

// Bind parameters
$stmt->bindValue(':today', $today);

// For employee role, bind `user_id` for `created_by` or `organizer` fields
if ($user_role === 'employee') {
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':username', $_SESSION['username'], PDO::PARAM_STR);
} else {
    // For admin, bind organizer if filtering by organizer
    if (isset($_GET['organizer']) && !empty($_GET['organizer'])) {
        $stmt->bindValue(':organizer', '%' . $_GET['organizer'] . '%');
    }
}

// Bind search parameter if applicable
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $stmt->bindValue(':search', '%' . $_GET['search'] . '%');
}

$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Événements</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header>
    <?php include 'header.php'; ?>
</header>

<main>
<section class="events-section">
    <div class="container">
        <h2 class="events-title">Mes Événements</h2>

        <form method="GET" action="my-events.php" class="filters-form">
            <!-- Search bar -->
            <input type="text" name="search" placeholder="Rechercher par nom d'événement" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />

            <?php if ($user_role === 'admin'): ?>
                <!-- Filter by organizer for admins -->
                <input type="text" name="organizer" placeholder="Filtrer par organisateur" value="<?= isset($_GET['organizer']) ? htmlspecialchars($_GET['organizer']) : '' ?>" />
            <?php endif; ?>

            <button type="submit" class="btn" style="border: 1px solid var(--Blue-Neon)">Filtrer</button>
        </form>

        <?php if (count($events) > 0): ?>
            <div class="event-cards">
                <?php foreach ($events as $event): ?>
                    <div class="event-card <?= $event['is_validated'] == 1 ? 'border-blue' : 'border-gradient' ?>">
                        <h3 class="event-name"><?= htmlspecialchars($event['name']) ?></h3>
                        <p class="event-participants">Nombre de participants: <span><?= htmlspecialchars($event['max_participants']) ?></span></p>
                        <p class="event-description"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                        <div class="event-info">
                            <i class="fa-regular fa-calendar-days"></i>
                            <span>Du <?= date("d M Y", strtotime($event['start_date'])) ?> au <?= date("d M Y", strtotime($event['end_date'])) ?></span>
                        </div>

                        <a href="event-details.php?id=<?= $event['id'] ?>" class="btn" style="border: 1px solid var(--Blue-Neon)">Voir Détails</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-events">
                <h3>Aucun événement disponible</h3>
                <p>Il n'y a pas d'événements à venir pour le moment. Revenez plus tard !</p>
            </div>
        <?php endif; ?>
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
