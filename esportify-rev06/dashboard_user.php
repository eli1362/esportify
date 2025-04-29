<?php
session_start();
require_once "backend/config.php";
global $pdo;

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve favorited events (based on user_favorites table)
$stmt = $pdo->prepare("SELECT events.* 
                       FROM events 
                       JOIN user_favorites ON events.id = user_favorites.event_id
                       WHERE user_favorites.user_id = :user_id AND events.is_validated = 1");
$stmt->execute(['user_id' => $user_id]);
$favorited_events = $stmt->fetchAll();

// Retrieve events created by the user (organizer)
$stmt = $pdo->prepare("SELECT * FROM events WHERE organizer = :username");
$stmt->execute(['username' => $_SESSION['username']]);
$created_events = $stmt->fetchAll();

// Retrieve user's event participation history (including scores and status)
$stmt = $pdo->prepare("SELECT events.name, user_events.score, user_events.status, events.start_date 
                       FROM user_events
                       JOIN events ON user_events.event_id = events.id 
                       WHERE user_events.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/user-dashboard.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

<header class="header">
    <?php include_once "header.php"; ?>
</header>

<main class="main">
    <div class="container user-dashboard">

        <h1 class="user-title">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <section class="hero-welcome">
            <div class="hero-left">
                <h2>Bienvenue <?= htmlspecialchars($_SESSION['username']) ?> !</h2>
                <p>Découvrez vos événements favoris, créez-en de nouveaux, et suivez vos performances.</p>
                <a href="create_event.php" class="btn user-dashboard__btn">+ Créer un événement</a>
            </div>
            <div class="hero-right">
                <img src="assets/images/png/dashboard_illustration.png" alt="Illustration dashboard">
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Événements Favoris</h2>
            <div class="cards-grid">
                <?php if ($favorited_events): ?>
                    <?php foreach ($favorited_events as $event): ?>
                        <div class="card event-card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <?php
                            $current_time = new DateTime();
                            $start_time = new DateTime($event['start_date']);
                            $early_start = clone $start_time;
                            $early_start->modify('-30 minutes');

                            $event_id = $event['id'];

                            // Check if the user has already joined the event
                            $joinedStmt = $pdo->prepare("SELECT 1 FROM user_events WHERE user_id = :user_id AND event_id = :event_id");
                            $joinedStmt->execute(['user_id' => $_SESSION['user_id'], 'event_id' => $event_id]);
                            $userJoined = $joinedStmt->rowCount() > 0;

                            // Check the number of participants in the event
                            $countStmt = $pdo->prepare("SELECT current_participants FROM events WHERE id = :event_id");
                            $countStmt->execute(['event_id' => $event_id]);
                            $currentParticipants = $countStmt->fetchColumn();
                            $isFull = $currentParticipants >= $event['max_participants'];

                            if ($userJoined): ?>
                                <p><em>Déjà inscrit</em></p>
                            <?php elseif ($isFull): ?>
                                <p><em>Événement complet</em></p>
                            <?php else: ?>
                                <a href="register_event.php?event_id=<?= $event['id'] ?>" class="btn" style="border: .5px solid var(--Blue-Neon)">S'inscrire</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun événement favori trouvé.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Mes Événements Créés</h2>
            <div class="cards-grid">
                <?php if ($created_events): ?>
                    <?php foreach ($created_events as $event): ?>
                        <div class="card event-card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <p>Status: <strong><?= htmlspecialchars($event['validation_status']) ?></strong></p>
                            <?php
                            $current_time = new DateTime();
                            $start_time = new DateTime($event['start_date']);
                            $early_start = clone $start_time;
                            $early_start->modify('-30 minutes');

                            if (
                                $event['validation_status'] == 'validé' &&
                                $current_time >= $early_start &&
                                $event['session_started'] == 0
                            ): ?>
                                <form method="POST" action="start_event.php">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn start">Démarrer</button>
                                </form>
                            <?php elseif ($event['session_started'] == 1): ?>
                                <p><em>Session en cours</em></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun événement créé.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Historique des Scores</h2>
            <div class="cards-grid">
                <?php if ($user_events): ?>
                    <?php foreach ($user_events as $event): ?>
                        <div class="card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p>Status: <?= htmlspecialchars($event['status']) ?></p>
                            <p>Score: <?= htmlspecialchars($event['score']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun score enregistré.</p>
                <?php endif; ?>
            </div>
        </section>

    </div>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
